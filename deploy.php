<?php
/**
 * deploy.php — Web-based deploy helper for shared hosting without SSH.
 *
 * Handles the "I just uploaded my code but composer isn't installed" case:
 *   1. Downloads composer.phar for you.
 *   2. Runs `composer install` in the background so it survives PHP timeouts.
 *   3. Streams the log to your browser as it happens.
 *   4. Confirms when vendor/ is ready.
 *
 * Auth: reuses the same env vars as webcli.php.
 *   WEBCLI_ENABLED=true
 *   WEBCLI_PASSWORD=<20+ chars>
 *
 * Runs even when Laravel is broken (no vendor/ dependency).
 */

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');
@set_time_limit(60);

const D_ROOT   = __DIR__;
const D_CORE   = __DIR__ . '/core';
const D_ENV    = __DIR__ . '/core/.env';
const D_LOG    = __DIR__ . '/composer_install.log';
const D_PHAR   = __DIR__ . '/composer.phar';
const D_SESS   = 'deploy_sess';

// ---------------------------------------------------------------------------
function d_env(): array {
    if (!is_readable(D_ENV)) return [];
    $env = [];
    foreach (file(D_ENV, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
    }
    return $env;
}

$env      = d_env();
$enabled  = ($env['WEBCLI_ENABLED'] ?? 'false') === 'true';
$password = $env['WEBCLI_PASSWORD'] ?? '';

// Disabled state — friendly instructions
if (!$enabled || $password === '') {
    header('Content-Type: text/html; charset=utf-8');
    http_response_code(503);
    $enabledLine  = $enabled ? '✅' : '❌ set WEBCLI_ENABLED=true';
    $passwordLine = $password !== '' ? '✅' : '❌ set WEBCLI_PASSWORD=<20+ chars>';
    echo <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Deploy · disabled</title>
<style>body{font-family:ui-monospace,Menlo,monospace;background:#0e1116;color:#c9d1d9;padding:2rem}
h1{color:#58a6ff}pre{background:#161b22;border:1px solid #30363d;padding:.7rem;border-radius:6px}</style></head>
<body><h1>🔒 Deploy — disabled</h1>
<p>Set these in <code>core/.env</code>, then reload:</p>
<pre>WEBCLI_ENABLED=true
WEBCLI_PASSWORD=&lt;20+ characters&gt;</pre>
<p>Enabled: {$enabledLine}<br>Password: {$passwordLine}</p></body></html>
HTML;
    exit;
}

if (strlen($password) < 20) {
    http_response_code(500);
    exit("WEBCLI_PASSWORD must be at least 20 characters.\n");
}

// ---------------------------------------------------------------------------
// Session + auth
session_name(D_SESS);
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
    $err = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if (hash_equals($password, (string)$_POST['password'])) {
            session_regenerate_id(true);
            $_SESSION['auth'] = true;
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }
        $err = 'Invalid password.';
    }
    header('Content-Type: text/html; charset=utf-8');
    echo d_login_page($err);
    exit;
}

// ---------------------------------------------------------------------------
// AJAX endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['_csrf'] ?? ''))) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'CSRF token mismatch. Refresh the page.']);
        exit;
    }
    echo json_encode(d_dispatch((string)$_POST['action']));
    exit;
}

// ---------------------------------------------------------------------------
// Render page
header('Content-Type: text/html; charset=utf-8');
echo d_main_page($_SESSION['csrf']);
exit;

// ===========================================================================
// ACTIONS
// ===========================================================================
function d_dispatch(string $action): array {
    return match ($action) {
        'state'    => d_state(),
        'download' => d_download_composer(),
        'install'  => d_start_install(),
        'poll'     => d_poll(),
        'log'      => ['ok' => true, 'log' => d_tail_log(500)],
        'reset'    => d_reset_log(),
        default    => ['ok' => false, 'error' => "Unknown action: {$action}"],
    };
}

function d_state(): array {
    $composer = d_find_composer();
    $vendor   = is_file(D_CORE . '/vendor/autoload.php');
    return [
        'ok'            => true,
        'php_version'   => PHP_VERSION,
        'shell_exec'    => function_exists('shell_exec'),
        'composer_path' => $composer,
        'vendor_ready'  => $vendor,
        'installing'    => d_is_installing(),
        'core_present'  => is_dir(D_CORE),
        'env_present'   => is_file(D_ENV),
    ];
}

function d_find_composer(): ?string {
    $home = getenv('HOME') ?: '';
    $candidates = [
        D_PHAR,
        D_ROOT . '/composer',
        dirname(D_ROOT) . '/composer',
        $home . '/composer',
        $home . '/composer.phar',
        '/usr/local/bin/composer',
        '/opt/cpanel/composer/bin/composer',
        '/usr/bin/composer',
    ];
    foreach ($candidates as $c) {
        if ($c && is_file($c)) return $c;
    }
    if (function_exists('shell_exec')) {
        $which = trim((string)@shell_exec('command -v composer 2>/dev/null'));
        if ($which !== '' && is_file($which)) return $which;
    }
    return null;
}

function d_find_php(): string {
    if (function_exists('shell_exec')) {
        $which = trim((string)@shell_exec('command -v php 2>/dev/null'));
        if ($which !== '') return $which;
    }
    return defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';
}

// Detect the user's home dir. LiteSpeed's shell_exec context often lacks a HOME
// env var, which breaks Composer. Falls back to posix, then to the repo path.
function d_home(): string {
    $home = getenv('HOME');
    if ($home) return $home;
    if (function_exists('posix_geteuid')) {
        $pw = @posix_getpwuid(posix_geteuid());
        if (!empty($pw['dir'])) return $pw['dir'];
    }
    // /home/<user>/... — walk up two dirs
    $parts = explode('/', trim(D_ROOT, '/'));
    if (($parts[0] ?? '') === 'home' && !empty($parts[1])) return '/home/' . $parts[1];
    return sys_get_temp_dir();
}

// Prefix for shell commands so Composer's installer/runner sees HOME + COMPOSER_HOME.
function d_env_prefix(): string {
    $home = d_home();
    $ch   = $home . '/.composer';
    if (!is_dir($ch)) @mkdir($ch, 0755, true);
    return 'HOME=' . escapeshellarg($home) . ' COMPOSER_HOME=' . escapeshellarg($ch) . ' ';
}

function d_download_composer(): array {
    if (!function_exists('shell_exec')) return ['ok' => false, 'error' => 'shell_exec is disabled on this host — cannot run the installer.'];
    if (!ini_get('allow_url_fopen')) return ['ok' => false, 'error' => 'allow_url_fopen is off — cannot download the installer.'];

    $installer = sys_get_temp_dir() . '/composer-setup-' . uniqid() . '.php';
    $bytes = @file_get_contents('https://getcomposer.org/installer');
    if ($bytes === false) return ['ok' => false, 'error' => 'Failed to fetch https://getcomposer.org/installer'];
    if (@file_put_contents($installer, $bytes) === false) return ['ok' => false, 'error' => "Cannot write {$installer}"];

    $target_dir  = dirname(D_PHAR);
    $target_name = basename(D_PHAR);
    $php = d_find_php();
    $cmd = d_env_prefix()
         . escapeshellarg($php) . ' ' . escapeshellarg($installer)
         . ' --install-dir=' . escapeshellarg($target_dir)
         . ' --filename='    . escapeshellarg($target_name) . ' 2>&1';
    $out = (string)@shell_exec($cmd);
    @unlink($installer);

    if (!is_file(D_PHAR)) {
        return ['ok' => false, 'error' => 'Composer installer ran but composer.phar not found.', 'output' => $out];
    }
    @chmod(D_PHAR, 0755);
    return ['ok' => true, 'output' => $out, 'path' => D_PHAR];
}

function d_start_install(): array {
    if (!function_exists('shell_exec')) return ['ok' => false, 'error' => 'shell_exec is disabled on this host.'];
    if (!is_dir(D_CORE))                return ['ok' => false, 'error' => 'core/ directory not found.'];
    $composer = d_find_composer();
    if (!$composer)                     return ['ok' => false, 'error' => 'No composer binary yet. Click "Download Composer" first.'];
    if (d_is_installing())              return ['ok' => false, 'error' => 'An install is already running. Wait for it to finish.'];

    @file_put_contents(D_LOG, sprintf("[%s] Starting composer install\n", date('Y-m-d H:i:s')));
    @chmod(D_LOG, 0644);

    $php = d_find_php();
    $cmd = 'cd ' . escapeshellarg(D_CORE)
         . ' && ' . d_env_prefix() . 'nohup ' . escapeshellarg($php) . ' -d memory_limit=-1 ' . escapeshellarg($composer)
         . ' install --no-dev --optimize-autoloader --no-security-blocking --no-interaction --no-progress'
         . ' >> ' . escapeshellarg(D_LOG) . ' 2>&1 </dev/null &';
    @shell_exec($cmd);

    // Give it a moment to actually spawn before returning state
    usleep(400 * 1000);
    return ['ok' => true, 'started' => true];
}

function d_poll(): array {
    return [
        'ok'          => true,
        'installing'  => d_is_installing(),
        'vendor_ready'=> is_file(D_CORE . '/vendor/autoload.php'),
        'log'         => d_tail_log(200),
    ];
}

function d_tail_log(int $n): string {
    if (!is_readable(D_LOG)) return '';
    $lines = @file(D_LOG);
    if (!$lines) return '';
    return implode('', array_slice($lines, -$n));
}

function d_is_installing(): bool {
    if (!is_readable(D_LOG)) return false;
    $mtime = @filemtime(D_LOG);
    if (!$mtime) return false;
    // If the log has been modified in the last 90 seconds, consider it live.
    $recent = (time() - $mtime) < 90;
    // Also consider it done if vendor exists AND recent activity looks like success.
    $vendor = is_file(D_CORE . '/vendor/autoload.php');
    if ($vendor && !$recent) return false;
    $tail = d_tail_log(20);
    $done = preg_match('/(Generating optimized autoload files|packages? you are using are looking for funding|Nothing to install)/i', $tail);
    // Detect fatal errors that end the run
    $failed = preg_match('/(fatal error|Allowed memory size|Your requirements could not be resolved)/i', $tail);
    if ($done || $failed) return false;
    return $recent;
}

function d_reset_log(): array {
    if (is_file(D_LOG) && !@unlink(D_LOG)) return ['ok' => false, 'error' => 'Cannot delete log file.'];
    return ['ok' => true];
}

// ===========================================================================
// HTML
// ===========================================================================
function d_login_page(string $err): string {
    $errHtml = $err ? '<div class="err">' . htmlspecialchars($err, ENT_QUOTES) . '</div>' : '';
    return <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Deploy · Sign in</title>
<style>
*{box-sizing:border-box}body{font-family:ui-monospace,Menlo,monospace;background:#0e1116;color:#c9d1d9;margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center}
form{background:#161b22;border:1px solid #30363d;border-radius:8px;padding:1.5rem;width:min(360px,92vw)}
h1{margin:0 0 1rem;font-size:1.15rem;color:#58a6ff}
input{width:100%;padding:.7rem;border-radius:6px;border:1px solid #30363d;background:#0d1117;color:#e6edf3;font-family:inherit;font-size:1rem}
input:focus{outline:none;border-color:#58a6ff}
button{margin-top:.8rem;width:100%;padding:.7rem;border-radius:6px;border:none;background:#238636;color:#fff;font-weight:bold;cursor:pointer;font-family:inherit;font-size:1rem}
button:hover{background:#2ea043}
.err{background:#3c1618;border:1px solid #f85149;color:#ffa198;padding:.6rem;border-radius:6px;margin-bottom:.8rem;font-size:.9rem}
label{display:block;margin-bottom:.4rem;font-size:.9rem;color:#8b949e}
</style></head><body>
<form method="POST" autocomplete="off">
<h1>🚀 Deploy Helper</h1>
{$errHtml}
<label for="p">Password</label>
<input id="p" name="password" type="password" autofocus required>
<button type="submit">Sign in</button>
</form></body></html>
HTML;
}

function d_main_page(string $csrf): string {
    $csrf = htmlspecialchars($csrf, ENT_QUOTES);
    return <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Deploy Helper</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:ui-monospace,Menlo,monospace;background:#0e1116;color:#c9d1d9;padding:1rem;min-height:100vh}
header{display:flex;justify-content:space-between;align-items:center;padding:.3rem 0 1rem;border-bottom:1px solid #30363d;margin-bottom:1rem}
header h1{color:#58a6ff;font-size:1.1rem;font-weight:normal}
header a{color:#8b949e;text-decoration:none;font-size:.9rem}
header a:hover{color:#c9d1d9}
.grid{display:grid;grid-template-columns:1fr;gap:1rem;max-width:1000px;margin:0 auto}
@media(min-width:900px){.grid{grid-template-columns:1fr 1fr}}
.card{background:#161b22;border:1px solid #30363d;border-radius:8px;padding:1rem}
.card h2{color:#f0f6fc;font-size:1rem;margin-bottom:.6rem;font-weight:bold}
.row{display:flex;justify-content:space-between;padding:.35rem 0;border-bottom:1px solid #21262d;font-size:.9rem}
.row:last-child{border:none}
.row span:first-child{color:#8b949e}
.ok{color:#3fb950}
.bad{color:#f85149}
.warn{color:#d29922}
.actions{display:flex;flex-direction:column;gap:.5rem}
button{padding:.7rem;border:none;border-radius:6px;background:#238636;color:#fff;font-weight:bold;cursor:pointer;font-family:inherit;font-size:.95rem;transition:background .15s}
button:hover:not(:disabled){background:#2ea043}
button:disabled{background:#30363d;color:#6e7681;cursor:not-allowed}
button.secondary{background:#21262d;color:#c9d1d9;border:1px solid #30363d}
button.secondary:hover:not(:disabled){background:#30363d}
.log{background:#0d1117;border:1px solid #30363d;border-radius:6px;padding:.8rem;font-size:.82rem;line-height:1.4;max-height:400px;overflow-y:auto;white-space:pre-wrap;word-break:break-word;color:#c9d1d9}
.log:empty::before{content:'(no output yet — click "Run composer install" to start)';color:#6e7681;font-style:italic}
.notice{background:#0d2f4c;border-left:3px solid #58a6ff;padding:.6rem .8rem;border-radius:4px;margin-bottom:.6rem;font-size:.85rem;color:#c9d1d9}
.success{background:#0d3218;border-left:3px solid #3fb950;padding:.8rem;border-radius:4px;margin-bottom:.8rem;color:#3fb950;font-weight:bold}
.success a{color:#58a6ff}
.err{background:#3c1618;border-left:3px solid #f85149;padding:.6rem .8rem;border-radius:4px;font-size:.85rem;color:#ffa198;margin-top:.6rem}
.spinner{display:inline-block;animation:spin 1s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
::-webkit-scrollbar{width:8px;height:8px}
::-webkit-scrollbar-thumb{background:#30363d;border-radius:4px}
</style></head>
<body>
<header><h1>🚀 Deploy Helper · Composer / vendor install</h1><a href="?logout">logout</a></header>

<div class="grid">
  <section class="card">
    <h2>State</h2>
    <div id="state">
      <div class="row"><span>PHP</span><span id="s_php">…</span></div>
      <div class="row"><span>shell_exec</span><span id="s_shell">…</span></div>
      <div class="row"><span>core/ present</span><span id="s_core">…</span></div>
      <div class="row"><span>core/.env present</span><span id="s_env">…</span></div>
      <div class="row"><span>composer binary</span><span id="s_composer">…</span></div>
      <div class="row"><span>core/vendor/ ready</span><span id="s_vendor">…</span></div>
      <div class="row"><span>install running</span><span id="s_running">…</span></div>
    </div>
    <div id="success_box"></div>
    <div id="error_box"></div>
  </section>

  <section class="card">
    <h2>Actions</h2>
    <div class="actions">
      <button id="b_download">1. Download composer.phar</button>
      <button id="b_install">2. Run composer install</button>
      <button id="b_refresh" class="secondary">Refresh state</button>
      <button id="b_reset" class="secondary">Reset log</button>
    </div>
    <div class="notice" style="margin-top:.8rem">
      <b>How it works:</b> Composer install runs in the background (via <code>nohup … &</code>) so PHP's timeout doesn't kill it. The log below auto-refreshes every 2 seconds. Expect 3–8 minutes for 162 packages.
    </div>
  </section>

  <section class="card" style="grid-column:1/-1">
    <h2>Install log <span id="poll_indicator" style="font-weight:normal;color:#8b949e;font-size:.8rem"></span></h2>
    <div class="log" id="log"></div>
  </section>
</div>

<script>
(function(){
  const CSRF="{$csrf}";
  const \$ = id => document.getElementById(id);
  const s = {php:'s_php', shell:'s_shell', core:'s_core', env:'s_env', composer:'s_composer', vendor:'s_vendor', running:'s_running'};

  async function api(action, extra={}){
    const body = new URLSearchParams({action, _csrf:CSRF, ...extra});
    const res = await fetch(window.location.pathname, {method:'POST',body,credentials:'same-origin'});
    return res.json();
  }
  function ok(t){ return '<span class="ok">'+t+'</span>'; }
  function bad(t){ return '<span class="bad">'+t+'</span>'; }
  function warn(t){ return '<span class="warn">'+t+'</span>'; }
  function esc(t){ return String(t).replace(/[&<>]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;'}[c])); }

  function renderState(st){
    \$(s.php).textContent = st.php_version;
    \$(s.shell).innerHTML = st.shell_exec ? ok('yes') : bad('DISABLED');
    \$(s.core).innerHTML = st.core_present ? ok('yes') : bad('missing');
    \$(s.env).innerHTML = st.env_present ? ok('yes') : warn('missing');
    \$(s.composer).innerHTML = st.composer_path ? ok(esc(st.composer_path)) : bad('not found');
    \$(s.vendor).innerHTML = st.vendor_ready ? ok('READY') : bad('missing');
    \$(s.running).innerHTML = st.installing ? warn('yes') : '<span>no</span>';

    \$('b_download').disabled = !st.shell_exec || st.installing;
    \$('b_install').disabled  = !st.shell_exec || !st.composer_path || st.installing || !st.core_present;

    \$('success_box').innerHTML = st.vendor_ready ? '<div class="success">✅ vendor/ is ready. <a href="/">Visit site →</a></div>' : '';
    if (!st.shell_exec) \$('error_box').innerHTML = '<div class="err">shell_exec is disabled on this host. You will need to upload vendor/ manually via File Manager.</div>';
  }

  async function refreshState(){
    const r = await api('state');
    if (r.ok) renderState(r);
  }

  let polling = false;
  async function poll(){
    if (polling) return;
    polling = true;
    \$('poll_indicator').innerHTML = '<span class="spinner">◐</span> polling every 2s';
    while (true){
      const r = await api('poll');
      if (r.ok){
        \$('log').textContent = r.log;
        \$('log').scrollTop = \$('log').scrollHeight;
        if (!r.installing) break;
      }
      await new Promise(res => setTimeout(res, 2000));
    }
    \$('poll_indicator').textContent = '';
    await refreshState();
    polling = false;
  }

  \$('b_download').addEventListener('click', async () => {
    \$('b_download').disabled = true;
    \$('b_download').innerHTML = '<span class="spinner">◐</span> downloading…';
    const r = await api('download');
    \$('b_download').innerHTML = '1. Download composer.phar';
    if (!r.ok){ \$('error_box').innerHTML = '<div class="err">'+esc(r.error||'download failed')+(r.output?'\\n'+esc(r.output):'')+'</div>'; }
    else \$('error_box').innerHTML = '';
    await refreshState();
  });

  \$('b_install').addEventListener('click', async () => {
    \$('b_install').disabled = true;
    const r = await api('install');
    if (!r.ok){ \$('error_box').innerHTML = '<div class="err">'+esc(r.error||'install failed')+'</div>'; await refreshState(); return; }
    \$('error_box').innerHTML = '';
    await refreshState();
    poll();
  });

  \$('b_refresh').addEventListener('click', async () => {
    await refreshState();
    const r = await api('log');
    if (r.ok) \$('log').textContent = r.log;
  });

  \$('b_reset').addEventListener('click', async () => {
    await api('reset');
    \$('log').textContent = '';
    await refreshState();
  });

  // Initial load — if an install is already in progress from a previous session, resume polling
  (async () => {
    const r = await api('state');
    if (r.ok) renderState(r);
    const lr = await api('log');
    if (lr.ok) \$('log').textContent = lr.log;
    if (r.installing) poll();
  })();
})();
</script>
</body></html>
HTML;
}
