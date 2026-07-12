<?php
/**
 * reset-admin.php — Web tool to reset an admin's password.
 *
 * Uses direct mysqli against core/.env, so it works even when Laravel
 * itself can't boot. Password is bcrypted at cost 12 (same as Laravel default).
 *
 * Auth: shares WEBCLI_ENABLED / WEBCLI_PASSWORD with webcli/deploy/artisan-runner.
 */

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');
@set_time_limit(30);

const RA_ROOT = __DIR__;
const RA_ENV  = __DIR__ . '/core/.env';
const RA_LOG  = __DIR__ . '/reset-admin.log';
const RA_SESS = 'reset_admin_sess';
const RA_MIN_PASSWORD_LEN = 8;

// ---------------------------------------------------------------------------
function ra_env(): array {
    if (!is_readable(RA_ENV)) return [];
    $out = [];
    foreach (file(RA_ENV, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
        if (str_starts_with(trim($l), '#') || !str_contains($l, '=')) continue;
        [$k, $v] = explode('=', $l, 2);
        $out[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
    }
    return $out;
}

$env       = ra_env();
$enabled   = ($env['WEBCLI_ENABLED'] ?? 'false') === 'true';
$password  = $env['WEBCLI_PASSWORD'] ?? '';
$clientIp  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

function ra_log(string $ip, string $event, string $detail = ''): void {
    @file_put_contents(RA_LOG, sprintf("[%s] %s %s %s\n", date('Y-m-d H:i:s'), $ip, $event, $detail), FILE_APPEND | LOCK_EX);
}

if (!$enabled || $password === '') {
    http_response_code(503);
    header('Content-Type: text/html; charset=utf-8');
    echo ra_disabled_page($enabled, $password !== '');
    exit;
}
if (strlen($password) < 20) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "WEBCLI_PASSWORD must be at least 20 characters.\n";
    exit;
}

// ---------------------------------------------------------------------------
// Session
session_name(RA_SESS);
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Strict',
    'secure'   => (($_SERVER['HTTPS'] ?? '') === 'on') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'),
]);
@session_start();

if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (session_id()) session_destroy();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$authenticated = ($_SESSION['auth'] ?? false) === true;

if (!$authenticated) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if (hash_equals($password, (string)$_POST['password'])) {
            session_regenerate_id(true);
            $_SESSION['auth'] = true;
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
            ra_log($clientIp, 'auth-ok');
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }
        ra_log($clientIp, 'auth-fail');
        echo ra_login_page('Invalid password.');
        exit;
    }
    echo ra_login_page();
    exit;
}

// ---------------------------------------------------------------------------
// DB connection
function ra_db(): mysqli|array {
    $env = ra_env();
    $host = $env['DB_HOST']     ?? '127.0.0.1';
    $port = (int)($env['DB_PORT'] ?? 3306);
    $name = $env['DB_DATABASE'] ?? '';
    $user = $env['DB_USERNAME'] ?? '';
    $pass = $env['DB_PASSWORD'] ?? '';
    if ($name === '' || $user === '') return ['error' => 'core/.env is missing DB_DATABASE or DB_USERNAME.'];
    try {
        $mysqli = @new mysqli($host, $user, $pass, $name, $port);
        if ($mysqli->connect_error) return ['error' => 'DB connect failed: ' . $mysqli->connect_error];
        $mysqli->set_charset('utf8mb4');
        return $mysqli;
    } catch (\Throwable $e) {
        return ['error' => 'DB error: ' . $e->getMessage()];
    }
}

// ---------------------------------------------------------------------------
// Actions
$flash = ['type' => null, 'msg' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset') {
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['_csrf'] ?? ''))) {
        $flash = ['type' => 'err', 'msg' => 'CSRF token mismatch. Reload the page.'];
    } else {
        $id       = (int)($_POST['admin_id'] ?? 0);
        $newPw    = (string)($_POST['new_password'] ?? '');
        $confirm  = (string)($_POST['confirm_password'] ?? '');

        if ($id <= 0)                              $flash = ['type' => 'err', 'msg' => 'Pick an admin from the list.'];
        elseif (strlen($newPw) < RA_MIN_PASSWORD_LEN) $flash = ['type' => 'err', 'msg' => 'Password must be at least ' . RA_MIN_PASSWORD_LEN . ' characters.'];
        elseif ($newPw !== $confirm)               $flash = ['type' => 'err', 'msg' => 'Passwords do not match.'];
        else {
            $db = ra_db();
            if (is_array($db)) {
                $flash = ['type' => 'err', 'msg' => $db['error']];
            } else {
                $hash = password_hash($newPw, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt = $db->prepare('UPDATE admins SET password = ? WHERE id = ?');
                $stmt->bind_param('si', $hash, $id);
                if (!$stmt->execute()) {
                    $flash = ['type' => 'err', 'msg' => 'Update failed: ' . $stmt->error];
                } elseif ($stmt->affected_rows === 0) {
                    $flash = ['type' => 'warn', 'msg' => 'No admin with id ' . $id . ' was found. Nothing changed.'];
                } else {
                    ra_log($clientIp, 'reset', 'admin_id=' . $id);
                    $flash = ['type' => 'ok', 'msg' => "Password reset for admin id={$id}. Log in with the new password."];
                }
                $stmt->close();
                $db->close();
            }
        }
    }
}

// ---------------------------------------------------------------------------
// Fetch admins list
$admins = [];
$dbErr  = null;
$db = ra_db();
if (is_array($db)) {
    $dbErr = $db['error'];
} else {
    $res = @$db->query('SELECT id, name, username, email, updated_at FROM admins ORDER BY id ASC');
    if ($res) while ($row = $res->fetch_assoc()) $admins[] = $row;
    $db->close();
}

header('Content-Type: text/html; charset=utf-8');
echo ra_page($_SESSION['csrf'], $admins, $flash, $dbErr, $env['DB_DATABASE'] ?? '?');
exit;

// ===========================================================================
function ra_disabled_page(bool $enabled, bool $hasPassword): string {
    $e = $enabled ? '✅' : '❌ set WEBCLI_ENABLED=true';
    $p = $hasPassword ? '✅' : '❌ set WEBCLI_PASSWORD=<20+ chars>';
    return <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Reset Admin · disabled</title>
<style>body{font-family:ui-monospace,Menlo,monospace;background:#0e1116;color:#c9d1d9;padding:2rem;line-height:1.6}
h1{color:#58a6ff}pre{background:#161b22;border:1px solid #30363d;padding:.7rem;border-radius:6px}</style></head>
<body><h1>🔒 Reset Admin — disabled</h1>
<p>Add to <code>core/.env</code>:</p>
<pre>WEBCLI_ENABLED=true
WEBCLI_PASSWORD=&lt;20+ characters&gt;</pre>
<p>Enabled: {$e}<br>Password set: {$p}</p></body></html>
HTML;
}

function ra_login_page(string $err = ''): string {
    $e = $err ? '<div class="err">' . htmlspecialchars($err, ENT_QUOTES) . '</div>' : '';
    return <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Reset Admin · Sign in</title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
<form method="POST" autocomplete="off" class="bg-white rounded-lg shadow p-6 w-96">
<h1 class="text-2xl font-bold text-blue-700 mb-4">🔑 Admin Password Reset</h1>
{$e}
<label class="block text-sm text-gray-600 mb-1">Password</label>
<input name="password" type="password" required autofocus class="w-full border border-gray-300 rounded px-3 py-2 mb-3 focus:outline-none focus:border-blue-500">
<button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded">Sign in</button>
</form>
<style>.err{background:#fee;color:#900;padding:.6rem;border-radius:6px;margin-bottom:.8rem;font-size:.9rem}</style>
</body></html>
HTML;
}

function ra_page(string $csrf, array $admins, array $flash, ?string $dbErr, string $dbName): string {
    $csrf   = htmlspecialchars($csrf, ENT_QUOTES);
    $dbName = htmlspecialchars($dbName, ENT_QUOTES);
    $minLen = RA_MIN_PASSWORD_LEN;

    $flashHtml = '';
    if ($flash['msg']) {
        $cls = match ($flash['type']) {
            'ok'   => 'bg-green-50 border-green-400 text-green-800',
            'warn' => 'bg-yellow-50 border-yellow-400 text-yellow-800',
            default=> 'bg-red-50 border-red-400 text-red-800',
        };
        $icon = match ($flash['type']) {
            'ok'   => 'fa-check-circle',
            'warn' => 'fa-exclamation-triangle',
            default=> 'fa-times-circle',
        };
        $safeMsg = htmlspecialchars($flash['msg'], ENT_QUOTES);
        $flashHtml = "<div class=\"border-l-4 p-4 mb-6 rounded {$cls}\"><i class=\"fas {$icon} mr-1\"></i> {$safeMsg}</div>";
    }

    $errBlock = $dbErr
        ? '<div class="border-l-4 p-4 mb-6 rounded bg-red-50 border-red-400 text-red-800"><i class="fas fa-database mr-1"></i> ' . htmlspecialchars($dbErr, ENT_QUOTES) . '</div>'
        : '';

    $rows = '';
    if ($admins) {
        foreach ($admins as $a) {
            $id       = (int)$a['id'];
            $name     = htmlspecialchars((string)($a['name']     ?? ''), ENT_QUOTES);
            $username = htmlspecialchars((string)($a['username'] ?? ''), ENT_QUOTES);
            $email    = htmlspecialchars((string)($a['email']    ?? ''), ENT_QUOTES);
            $updated  = htmlspecialchars((string)($a['updated_at'] ?? ''), ENT_QUOTES);
            $rows .= "<tr class=\"border-t hover:bg-blue-50 cursor-pointer\" onclick=\"pickAdmin({$id}, '{$username}', '{$name}')\">
                <td class=\"px-4 py-2 text-sm\">
                    <input type=\"radio\" name=\"admin_id_ui\" value=\"{$id}\" data-username=\"{$username}\" data-name=\"{$name}\" class=\"admin-radio\">
                </td>
                <td class=\"px-4 py-2 text-sm font-mono\">{$id}</td>
                <td class=\"px-4 py-2 text-sm font-medium\">{$username}</td>
                <td class=\"px-4 py-2 text-sm\">{$name}</td>
                <td class=\"px-4 py-2 text-sm text-gray-600\">{$email}</td>
                <td class=\"px-4 py-2 text-xs text-gray-500\">{$updated}</td>
            </tr>";
        }
    } else {
        $rows = '<tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No admins found in the database.</td></tr>';
    }

    return <<<HTML
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Admin Password</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>.admin-radio{transform:scale(1.2)}</style>
</head><body class="bg-gray-50">
<div class="min-h-screen">
  <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-6 shadow-lg">
    <div class="max-w-5xl mx-auto px-4 flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold"><i class="fas fa-key"></i> Admin Password Reset</h1>
        <p class="text-blue-100 mt-2">Database: <code>{$dbName}</code> · Logged to <code>reset-admin.log</code></p>
      </div>
      <a href="?logout" class="text-blue-100 hover:text-white"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>

  <div class="max-w-5xl mx-auto px-4 py-8">
    {$flashHtml}
    {$errBlock}

    <form method="POST" autocomplete="off" onsubmit="return confirmReset(event)">
      <input type="hidden" name="_csrf" value="{$csrf}">
      <input type="hidden" name="action" value="reset">
      <input type="hidden" name="admin_id" id="admin_id" value="">

      <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-5 py-3 border-b bg-gray-50 rounded-t-lg">
          <h2 class="font-bold text-gray-700"><i class="fas fa-user-shield text-blue-600 mr-1"></i> 1. Pick the admin</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-100 text-xs uppercase text-gray-600">
              <tr>
                <th class="px-4 py-2 w-10"></th>
                <th class="px-4 py-2 text-left">ID</th>
                <th class="px-4 py-2 text-left">Username</th>
                <th class="px-4 py-2 text-left">Name</th>
                <th class="px-4 py-2 text-left">Email</th>
                <th class="px-4 py-2 text-left">Last updated</th>
              </tr>
            </thead>
            <tbody>{$rows}</tbody>
          </table>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow mb-6 p-5">
        <h2 class="font-bold text-gray-700 mb-3"><i class="fas fa-lock text-blue-600 mr-1"></i> 2. Set new password</h2>
        <div class="mb-3 text-sm text-gray-700">Selected: <span id="selectedLabel" class="font-mono font-bold text-blue-700">— none —</span></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">New password (min {$minLen} chars)</label>
            <input name="new_password" id="new_password" type="password" required minlength="{$minLen}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 font-mono">
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Confirm password</label>
            <input name="confirm_password" id="confirm_password" type="password" required minlength="{$minLen}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 font-mono">
          </div>
        </div>
        <div class="mt-3 flex items-center gap-2 text-sm">
          <input type="checkbox" id="showPw" onchange="togglePw()" class="w-4 h-4">
          <label for="showPw" class="text-gray-600">Show passwords</label>
        </div>
        <div class="mt-2 text-xs text-gray-500">Password will be bcrypt-hashed (cost 12) and stored in the <code>admins.password</code> column.</div>
      </div>

      <div class="flex gap-3">
        <button type="submit" id="submitBtn" disabled class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded shadow disabled:bg-gray-400 disabled:cursor-not-allowed">
          <i class="fas fa-key"></i> Reset password
        </button>
        <button type="reset" onclick="location.reload()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold px-6 py-3 rounded">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
let selectedLabel = null;
function pickAdmin(id, username, name){
  document.getElementById('admin_id').value = id;
  document.querySelector('input.admin-radio[value="' + id + '"]').checked = true;
  const label = username + (name ? ' (' + name + ')' : '') + ' [id=' + id + ']';
  document.getElementById('selectedLabel').textContent = label;
  selectedLabel = label;
  checkReady();
}
document.querySelectorAll('.admin-radio').forEach(r => {
  r.addEventListener('click', ev => {
    ev.stopPropagation();
    pickAdmin(parseInt(r.value, 10), r.dataset.username || '', r.dataset.name || '');
  });
});
function togglePw(){
  const t = document.getElementById('showPw').checked ? 'text' : 'password';
  document.getElementById('new_password').type = t;
  document.getElementById('confirm_password').type = t;
}
function checkReady(){
  const id = document.getElementById('admin_id').value;
  const pw = document.getElementById('new_password').value;
  const cf = document.getElementById('confirm_password').value;
  document.getElementById('submitBtn').disabled = !id || pw.length < {$minLen} || pw !== cf;
}
['new_password','confirm_password'].forEach(id => document.getElementById(id).addEventListener('input', checkReady));
function confirmReset(ev){
  const id = document.getElementById('admin_id').value;
  if (!id){ ev.preventDefault(); alert('Pick an admin first.'); return false; }
  const pw = document.getElementById('new_password').value;
  const cf = document.getElementById('confirm_password').value;
  if (pw !== cf){ ev.preventDefault(); alert('Passwords do not match.'); return false; }
  return confirm('Reset the password for ' + (selectedLabel || 'this admin') + '?');
}
</script>
</body></html>
HTML;
}
