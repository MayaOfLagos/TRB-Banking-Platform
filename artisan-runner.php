<?php
/**
 * artisan-runner.php — Web-based Artisan command runner.
 *
 * Categorized-button UI for the common Laravel commands, plus a "custom
 * command" field for anything else. Same auth as webcli.php and deploy.php:
 *
 *   WEBCLI_ENABLED=true
 *   WEBCLI_PASSWORD=<20+ chars>       # required for browser use
 *   WEBCLI_CRON_TOKEN=<long random>   # optional, allows ?token=... for cron
 *
 * Cron example:
 *   curl -s "https://mydomain.com/artisan-runner.php?token=T&cmd=cache:clear"
 */

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');
@set_time_limit(300);

const AR_ROOT = __DIR__;
const AR_CORE = __DIR__ . '/core';
const AR_ENV  = __DIR__ . '/core/.env';
const AR_LOG  = __DIR__ . '/artisan-runner.log';
const AR_SESS = 'artisan_runner_sess';

// ---------------------------------------------------------------------------
function ar_env(): array {
    if (!is_readable(AR_ENV)) return [];
    $out = [];
    foreach (file(AR_ENV, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
        if (str_starts_with(trim($l), '#') || !str_contains($l, '=')) continue;
        [$k, $v] = explode('=', $l, 2);
        $out[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
    }
    return $out;
}

$env       = ar_env();
$enabled   = ($env['WEBCLI_ENABLED'] ?? 'false') === 'true';
$password  = $env['WEBCLI_PASSWORD'] ?? '';
$cronToken = $env['WEBCLI_CRON_TOKEN'] ?? '';
$clientIp  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if (!$enabled || $password === '') {
    http_response_code(503);
    header('Content-Type: text/html; charset=utf-8');
    echo ar_disabled_page($enabled, $password !== '');
    exit;
}
if (strlen($password) < 20) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "WEBCLI_PASSWORD must be at least 20 characters.\n";
    exit;
}

function ar_log(string $ip, string $event, string $detail = ''): void {
    @file_put_contents(AR_LOG, sprintf("[%s] %s %s %s\n", date('Y-m-d H:i:s'), $ip, $event, $detail), FILE_APPEND | LOCK_EX);
}

// ---------------------------------------------------------------------------
// Command allow-list, grouped by category. Add here as you need.
$AR_COMMANDS = [
    'Cache' => [
        'cache:clear'   => 'Flush the application cache',
        'config:clear'  => 'Remove the configuration cache file',
        'config:cache'  => 'Create a cache file for faster configuration loading',
        'route:clear'   => 'Remove the route cache file',
        'route:cache'   => 'Create a route cache file for faster route registration',
        'view:clear'    => 'Clear all compiled view files',
        'view:cache'    => 'Compile all Blade templates',
        'event:clear'   => 'Clear all cached events and listeners',
    ],
    'Database' => [
        'migrate'           => 'Run pending database migrations',
        'migrate:status'    => 'Show status of each migration',
        'migrate:rollback'  => 'Rollback the last migration batch',
        'migrate:fresh'     => 'Drop all tables and re-run all migrations',
        'db:seed'           => 'Seed the database',
        'db:show'           => 'Show database info',
    ],
    'Optimization' => [
        'optimize'          => 'Cache the framework bootstrap files',
        'optimize:clear'    => 'Remove all cached bootstrap files',
        'storage:link'      => 'Create the symbolic link to public/storage',
        'key:generate'      => 'Set the application key',
        'queue:restart'     => 'Restart queue worker daemons after their current job',
    ],
    'Inspection' => [
        'about'         => 'Display basic information about your application',
        'route:list'    => 'List all registered routes',
        '--version'     => 'Show Laravel framework version',
        'env'           => 'Show the current framework environment',
    ],
];

// ---------------------------------------------------------------------------
// Session
session_name(AR_SESS);
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

// Cron-token bypass path: allows unattended calls without session
$queryToken = $_GET['token'] ?? $_POST['token'] ?? '';
$cronAuthed = $cronToken !== '' && $queryToken !== '' && hash_equals($cronToken, (string)$queryToken);

$authenticated = ($_SESSION['auth'] ?? false) === true;

// Direct API mode — ?cmd=<name>&token=<cron token>&format=json
if ($cronAuthed && isset($_GET['cmd']) || isset($_POST['cmd'])) {
    $cmdStr = trim((string)($_GET['cmd'] ?? $_POST['cmd']));
    if (!$cronAuthed) {
        // Not a cron call → require session auth
        if (!$authenticated) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'unauthorized']);
            exit;
        }
        // CSRF check for authenticated POSTs
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['_csrf'] ?? ''))) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'csrf mismatch']);
            exit;
        }
    }
    if ($cmdStr === '') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'empty command']);
        exit;
    }
    // Enforce allow-list unless allow_custom flag is set
    $allowCustom = ($env['WEBCLI_ARTISAN_ALLOW_CUSTOM'] ?? 'true') === 'true';
    $allowed = ar_flat_list($AR_COMMANDS);
    $verb = explode(' ', $cmdStr)[0];
    if (!$allowCustom && !in_array($verb, $allowed, true)) {
        ar_log($clientIp, 'reject-custom', $cmdStr);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'command not in allow-list; set WEBCLI_ARTISAN_ALLOW_CUSTOM=true to enable']);
        exit;
    }
    ar_log($clientIp, ($cronAuthed ? 'cron' : 'ui'), $cmdStr);
    $result = ar_run($cmdStr);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

// Browser session flow
if (!$authenticated) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if (hash_equals($password, (string)$_POST['password'])) {
            session_regenerate_id(true);
            $_SESSION['auth'] = true;
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
            ar_log($clientIp, 'auth-ok');
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }
        ar_log($clientIp, 'auth-fail');
        echo ar_login_page('Invalid password.');
        exit;
    }
    echo ar_login_page();
    exit;
}

// Authenticated GET → render UI
header('Content-Type: text/html; charset=utf-8');
echo ar_ui_page($AR_COMMANDS, $_SESSION['csrf'], $cronToken !== '');
exit;

// ===========================================================================
function ar_flat_list(array $grouped): array {
    $flat = [];
    foreach ($grouped as $cmds) foreach (array_keys($cmds) as $c) $flat[] = $c;
    return $flat;
}

// Run an artisan command via kernel->handle() so ALL Symfony console
// syntax works: --version, --help, flags, positional args, options.
function ar_run(string $cmdStr): array {
    $started = microtime(true);
    $vendorPath = AR_CORE . '/vendor/autoload.php';
    if (!is_file($vendorPath)) {
        return [
            'success'   => false,
            'command'   => $cmdStr,
            'output'    => "vendor/autoload.php not found at {$vendorPath}\nDeploy vendor/ or run `composer install`.",
            'exit_code' => 1,
            'timestamp' => date('Y-m-d H:i:s'),
            'duration_ms' => (int)((microtime(true) - $started) * 1000),
        ];
    }
    try {
        require_once $vendorPath;
        $app    = require AR_CORE . '/bootstrap/app.php';
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $argv   = ar_split_cmd($cmdStr);
        $input  = new \Symfony\Component\Console\Input\ArgvInput(array_merge(['artisan'], $argv));
        $output = new \Symfony\Component\Console\Output\BufferedOutput();
        $status = $kernel->handle($input, $output);
        $text   = $output->fetch();
        if ($text === '') $text = "(no output; exit code {$status})";
        return [
            'success'   => $status === 0,
            'command'   => $cmdStr,
            'output'    => $text,
            'exit_code' => $status,
            'timestamp' => date('Y-m-d H:i:s'),
            'duration_ms' => (int)((microtime(true) - $started) * 1000),
        ];
    } catch (\Throwable $e) {
        return [
            'success'   => false,
            'command'   => $cmdStr,
            'output'    => "Error: " . $e->getMessage(),
            'exit_code' => 1,
            'timestamp' => date('Y-m-d H:i:s'),
            'duration_ms' => (int)((microtime(true) - $started) * 1000),
        ];
    }
}

// Simple shell-like tokenizer (single/double quote support)
function ar_split_cmd(string $s): array {
    $out = []; $buf = ''; $q = null;
    for ($i = 0, $n = strlen($s); $i < $n; $i++) {
        $c = $s[$i];
        if ($q !== null) {
            if ($c === '\\' && $i + 1 < $n) { $buf .= $s[++$i]; continue; }
            if ($c === $q) { $q = null; continue; }
            $buf .= $c;
        } elseif ($c === '"' || $c === "'") {
            $q = $c;
        } elseif ($c === ' ' || $c === "\t") {
            if ($buf !== '') { $out[] = $buf; $buf = ''; }
        } else {
            $buf .= $c;
        }
    }
    if ($buf !== '') $out[] = $buf;
    return $out;
}

// ===========================================================================
// HTML
// ===========================================================================
function ar_disabled_page(bool $enabled, bool $hasPassword): string {
    $e = $enabled ? '✅' : '❌ set WEBCLI_ENABLED=true';
    $p = $hasPassword ? '✅' : '❌ set WEBCLI_PASSWORD=<20+ chars>';
    return <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Artisan Runner · disabled</title>
<style>body{font-family:ui-monospace,Menlo,monospace;background:#0e1116;color:#c9d1d9;padding:2rem;line-height:1.6}
h1{color:#58a6ff}pre{background:#161b22;border:1px solid #30363d;padding:.7rem;border-radius:6px}</style></head>
<body><h1>🔒 Artisan Runner — disabled</h1>
<p>Add to <code>core/.env</code>:</p>
<pre>WEBCLI_ENABLED=true
WEBCLI_PASSWORD=&lt;20+ characters&gt;
# Optional: for cron / unattended calls
WEBCLI_CRON_TOKEN=&lt;long random string&gt;</pre>
<p>Enabled: {$e}<br>Password set: {$p}</p></body></html>
HTML;
}

function ar_login_page(string $err = ''): string {
    $e = $err ? '<div class="err">' . htmlspecialchars($err, ENT_QUOTES) . '</div>' : '';
    return <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Artisan Runner · Sign in</title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
<form method="POST" autocomplete="off" class="bg-white rounded-lg shadow p-6 w-96">
<h1 class="text-2xl font-bold text-blue-700 mb-4">🚀 Artisan Runner</h1>
{$e}
<label class="block text-sm text-gray-600 mb-1">Password</label>
<input name="password" type="password" required autofocus class="w-full border border-gray-300 rounded px-3 py-2 mb-3 focus:outline-none focus:border-blue-500">
<button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded">Sign in</button>
</form>
<style>.err{background:#fee;color:#900;padding:.6rem;border-radius:6px;margin-bottom:.8rem;font-size:.9rem}</style>
</body></html>
HTML;
}

function ar_ui_page(array $commands, string $csrf, bool $hasCronToken): string {
    $csrf = htmlspecialchars($csrf, ENT_QUOTES);
    $host = htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'yourdomain.com', ENT_QUOTES);

    // Build the category cards
    $catHtml = '';
    $icons = ['Cache' => 'bolt', 'Database' => 'database', 'Optimization' => 'rocket', 'Inspection' => 'search'];
    foreach ($commands as $category => $cmds) {
        $icon = $icons[$category] ?? 'terminal';
        $btns = '';
        foreach ($cmds as $cmd => $desc) {
            $safeCmd = htmlspecialchars($cmd, ENT_QUOTES);
            $safeDesc = htmlspecialchars($desc, ENT_QUOTES);
            $btns .= "<button onclick=\"runCommand('{$safeCmd}')\" class=\"cmd-btn w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded text-sm text-left transition\" title=\"{$safeDesc}\">→ {$safeCmd}</button>";
        }
        $catHtml .= "<div class=\"bg-white rounded-lg shadow p-5\">
            <h2 class=\"text-lg font-bold mb-3 capitalize\"><i class=\"fas fa-{$icon} text-blue-600 mr-1\"></i> {$category}</h2>
            <div class=\"space-y-2\">{$btns}</div>
        </div>";
    }

    $cronBlock = $hasCronToken
        ? "<code class=\"text-xs text-blue-600 break-all\">curl -s \"https://{$host}/artisan-runner.php?token=YOUR_CRON_TOKEN&cmd=cache:clear\"</code>"
        : "<span class=\"text-sm text-gray-600\">Set <code>WEBCLI_CRON_TOKEN=&lt;long random&gt;</code> in core/.env to enable an unauthenticated URL-token path for cron.</span>";

    return <<<HTML
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laravel Artisan Runner</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.output-console{font-family:'Courier New',monospace;white-space:pre-wrap;word-wrap:break-word;max-height:500px;overflow-y:auto;background:#111;color:#0f0;padding:15px;border-radius:8px;font-size:12px;line-height:1.5}
.cmd-btn{transition:all .3s ease}
.cmd-btn:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(0,0,0,.2)}
</style></head><body class="bg-gray-50">
<div class="min-h-screen">
  <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-6 shadow-lg">
    <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold"><i class="fas fa-terminal"></i> Laravel Artisan Runner</h1>
        <p class="text-blue-100 mt-2">Password-gated · CSRF-protected · Logged to <code>artisan-runner.log</code></p>
      </div>
      <a href="?logout" class="text-blue-100 hover:text-white"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>

  <div class="max-w-6xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      {$catHtml}
    </div>

    <div class="bg-white rounded-lg shadow p-5 mb-6">
      <h2 class="text-lg font-bold mb-3"><i class="fas fa-keyboard text-blue-600 mr-1"></i> Custom command</h2>
      <div class="flex gap-2">
        <input id="customCmd" type="text" placeholder="e.g. make:controller FooController --resource" class="flex-1 border border-gray-300 rounded px-3 py-2 font-mono text-sm focus:outline-none focus:border-blue-500">
        <button onclick="runCustom()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded">Run</button>
      </div>
      <p class="text-xs text-gray-500 mt-2">Runs anything Symfony console accepts. Everything after the verb is passed as ArgvInput.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-bold">
          <span id="statusIcon"><i class="fas fa-play text-gray-400"></i></span>
          <span id="statusText">Ready</span>
        </h2>
        <button onclick="clearOutput()" class="bg-gray-400 hover:bg-gray-600 text-white px-3 py-1.5 rounded text-sm"><i class="fas fa-eraser"></i> Clear</button>
      </div>
      <div id="outputConsole" class="output-console"><div class="text-blue-300">\$ Ready to execute commands...</div></div>
      <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-gray-100 p-3 rounded"><p class="text-gray-600 text-xs">Last Run</p><p id="lastRun" class="text-base font-bold">--:--:--</p></div>
        <div class="bg-gray-100 p-3 rounded"><p class="text-gray-600 text-xs">Status</p><p id="statusBadge" class="text-base font-bold text-gray-400">Idle</p></div>
        <div class="bg-gray-100 p-3 rounded"><p class="text-gray-600 text-xs">Exit Code</p><p id="exitCode" class="text-base font-bold text-gray-400">--</p></div>
        <div class="bg-gray-100 p-3 rounded"><p class="text-gray-600 text-xs">Duration</p><p id="duration" class="text-base font-bold text-gray-400">0 ms</p></div>
      </div>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-400 p-5 mt-6 rounded">
      <h3 class="text-base font-bold mb-2"><i class="fas fa-clock"></i> Cron job setup</h3>
      <div class="bg-white p-3 rounded">{$cronBlock}</div>
    </div>
  </div>
</div>

<script>
const CSRF = "{$csrf}";
const consoleEl = document.getElementById('outputConsole');
const statusText = document.getElementById('statusText');
const statusIcon = document.getElementById('statusIcon');
const statusBadge = document.getElementById('statusBadge');
const exitCodeEl = document.getElementById('exitCode');
const durationEl = document.getElementById('duration');
const lastRunEl = document.getElementById('lastRun');

function esc(s){return String(s).replace(/[&<>]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]));}
function print(cls, text){
  const div = document.createElement('div');
  div.className = cls;
  div.textContent = text;
  consoleEl.appendChild(div);
  consoleEl.scrollTop = consoleEl.scrollHeight;
}

async function runCommand(command){
  const start = Date.now();
  statusText.textContent = 'Executing...';
  statusIcon.innerHTML = '<i class="fas fa-spinner fa-spin text-yellow-500"></i>';
  statusBadge.textContent = 'Running';
  statusBadge.className = 'text-base font-bold text-yellow-600';
  print('text-yellow-400', '$ php artisan ' + command);

  try {
    const body = new URLSearchParams({cmd: command, _csrf: CSRF});
    const res = await fetch(window.location.pathname, {method:'POST', body, credentials:'same-origin'});
    const data = await res.json();
    const dur = Date.now() - start;

    if (data.output) print('text-gray-200', data.output);
    if (data.success){
      print('text-green-400', '✓ Success (' + dur + ' ms)');
      statusBadge.textContent = 'Success';
      statusBadge.className = 'text-base font-bold text-green-600';
      statusIcon.innerHTML = '<i class="fas fa-check text-green-500"></i>';
    } else {
      print('text-red-400', '✗ Failed' + (data.error ? ': ' + data.error : ''));
      statusBadge.textContent = 'Failed';
      statusBadge.className = 'text-base font-bold text-red-600';
      statusIcon.innerHTML = '<i class="fas fa-times text-red-500"></i>';
    }
    exitCodeEl.textContent = data.exit_code === undefined ? '--' : data.exit_code;
    durationEl.textContent = dur + ' ms';
    lastRunEl.textContent = new Date().toLocaleTimeString();
    statusText.textContent = 'Complete';
  } catch (e){
    print('text-red-400', '✗ Network/JSON error: ' + e.message);
    statusBadge.textContent = 'Error';
    statusBadge.className = 'text-base font-bold text-red-600';
    statusIcon.innerHTML = '<i class="fas fa-exclamation text-red-500"></i>';
    statusText.textContent = 'Error';
  }
}

function runCustom(){
  const v = document.getElementById('customCmd').value.trim();
  if (v) runCommand(v);
}
document.getElementById('customCmd').addEventListener('keydown', e => {
  if (e.key === 'Enter') { e.preventDefault(); runCustom(); }
});

function clearOutput(){
  consoleEl.innerHTML = '<div class="text-blue-300">\$ Ready to execute commands...</div>';
  statusText.textContent = 'Ready';
  statusIcon.innerHTML = '<i class="fas fa-play text-gray-400"></i>';
  statusBadge.textContent = 'Idle';
  statusBadge.className = 'text-base font-bold text-gray-400';
  exitCodeEl.textContent = '--';
  durationEl.textContent = '0 ms';
}
</script>
</body></html>
HTML;
}
