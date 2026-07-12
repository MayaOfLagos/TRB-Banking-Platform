<?php
/**
 * webcli.php — Web-based CLI for Laravel/Artisan
 *
 * SECURITY: this file boots Laravel and runs arbitrary Artisan/PHP inside your app.
 * Anyone who has the password (or bypasses auth) can wipe your database, exfiltrate .env,
 * or modify code. Treat it like SSH: strong password, IP allowlist if possible, HTTPS only.
 *
 * ACTIVATION: add to core/.env
 *   WEBCLI_ENABLED=true
 *   WEBCLI_PASSWORD=some-string-with-at-least-20-characters
 *   # optional hardening:
 *   WEBCLI_ALLOWED_IPS=1.2.3.4,5.6.7.8
 *   WEBCLI_ALLOW_EVAL=true          # allow the 'eval' command (Tinker-like)
 *   WEBCLI_ALLOW_SHELL=false        # allow the 'shell'/'composer' commands
 */

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
@set_time_limit(120);

const WEBCLI_ROOT = __DIR__;
const WEBCLI_CORE = __DIR__ . DIRECTORY_SEPARATOR . 'core';
const WEBCLI_ENV_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . '.env';
const WEBCLI_LOG_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'webcli.log';
const WEBCLI_RL_PATH = __DIR__ . DIRECTORY_SEPARATOR . '.webcli_ratelimit.json';
const WEBCLI_SESSION = 'webcli_sess';

// ---------------------------------------------------------------------------
// .env loader (works before Laravel boot)
function webcli_load_env(): array {
    if (!is_readable(WEBCLI_ENV_PATH)) return [];
    $env = [];
    foreach (file(WEBCLI_ENV_PATH, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
    }
    return $env;
}

$env = webcli_load_env();
$enabled     = ($env['WEBCLI_ENABLED']     ?? 'false') === 'true';
$password    = $env['WEBCLI_PASSWORD']     ?? '';
$allowEval   = ($env['WEBCLI_ALLOW_EVAL']  ?? 'true')  === 'true';
$allowShell  = ($env['WEBCLI_ALLOW_SHELL'] ?? 'false') === 'true';
$allowedIps  = array_filter(array_map('trim', explode(',', $env['WEBCLI_ALLOWED_IPS'] ?? '')));
$clientIp    = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

// ---------------------------------------------------------------------------
// Logging + rate limit helpers
function webcli_log(string $ip, string $event, string $detail = ''): void {
    $line = sprintf("[%s] %s %s %s\n", date('Y-m-d H:i:s'), $ip, $event, $detail);
    @file_put_contents(WEBCLI_LOG_PATH, $line, FILE_APPEND | LOCK_EX);
}
function webcli_rl_load(): array {
    if (!is_readable(WEBCLI_RL_PATH)) return [];
    $data = @file_get_contents(WEBCLI_RL_PATH);
    return $data ? (json_decode($data, true) ?: []) : [];
}
function webcli_rl_save(array $rl): void {
    @file_put_contents(WEBCLI_RL_PATH, json_encode($rl), LOCK_EX);
}
function webcli_ip_locked(string $ip): bool {
    $rl = webcli_rl_load();
    return isset($rl[$ip]) && ($rl[$ip]['lockedUntil'] ?? 0) > time();
}
function webcli_note_fail(string $ip): void {
    $rl = webcli_rl_load();
    $rec = $rl[$ip] ?? ['count' => 0, 'lockedUntil' => 0];
    $rec['count']++;
    if ($rec['count'] >= 5) { $rec['lockedUntil'] = time() + 1800; $rec['count'] = 0; }
    $rl[$ip] = $rec;
    webcli_rl_save($rl);
}
function webcli_note_ok(string $ip): void {
    $rl = webcli_rl_load();
    unset($rl[$ip]);
    webcli_rl_save($rl);
}

// ---------------------------------------------------------------------------
// Disabled path: friendly instructions
if (!$enabled || $password === '') {
    header('Content-Type: text/html; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    http_response_code(503);
    echo webcli_instructions_page($enabled, $password !== '');
    exit;
}

// Password strength gate
if (strlen($password) < 20) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo "WEBCLI_PASSWORD must be at least 20 characters. Refusing to start.\n";
    exit;
}

// IP allowlist
if ($allowedIps && !in_array($clientIp, $allowedIps, true)) {
    webcli_log($clientIp, 'ip-denied');
    http_response_code(403);
    echo '403 - Access denied.';
    exit;
}

// ---------------------------------------------------------------------------
// Session
session_name(WEBCLI_SESSION);
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Strict',
    'secure'   => (($_SERVER['HTTPS'] ?? '') === 'on' || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')),
]);
@session_start();

// Logout
if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (session_id()) session_destroy();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$authenticated = ($_SESSION['auth'] ?? false) === true;

// ---------------------------------------------------------------------------
// Login flow
if (!$authenticated) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if (webcli_ip_locked($clientIp)) {
            webcli_log($clientIp, 'auth-lockout');
            http_response_code(429);
            header('Content-Type: text/plain; charset=utf-8');
            echo "Too many failed attempts. Try again in 30 minutes.\n";
            exit;
        }
        if (hash_equals($password, (string)$_POST['password'])) {
            session_regenerate_id(true);
            $_SESSION['auth'] = true;
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
            webcli_note_ok($clientIp);
            webcli_log($clientIp, 'auth-ok');
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }
        webcli_note_fail($clientIp);
        webcli_log($clientIp, 'auth-fail');
        header('Content-Type: text/html; charset=utf-8');
        echo webcli_login_page('Invalid password.');
        exit;
    }
    header('Content-Type: text/html; charset=utf-8');
    echo webcli_login_page();
    exit;
}

// ---------------------------------------------------------------------------
// Authenticated: JSON command endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['_csrf'] ?? ''))) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'output' => "CSRF token mismatch. Refresh the page to get a new one."]);
        exit;
    }
    $cmd = trim((string)$_POST['cmd']);
    webcli_log($clientIp, 'cmd', $cmd);
    echo json_encode(webcli_dispatch($cmd, $allowEval, $allowShell));
    exit;
}

// Authenticated: serve terminal UI
header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');
echo webcli_terminal_page($_SESSION['csrf'], $allowEval, $allowShell);
exit;

// ===========================================================================
// COMMAND DISPATCH
// ===========================================================================
function webcli_dispatch(string $cmd, bool $allowEval, bool $allowShell): array {
    if ($cmd === '') return ['ok' => true, 'output' => ''];
    try {
        $argv = webcli_parse($cmd);
        $verb = strtolower((string)array_shift($argv));
        return match ($verb) {
            'help', '?'        => webcli_cmd_help(),
            'artisan', 'php'   => webcli_cmd_artisan($argv),
            'eval', 'tinker'   => $allowEval  ? webcli_cmd_eval($argv)             : webcli_denied('WEBCLI_ALLOW_EVAL=false'),
            'env'              => webcli_cmd_env($argv),
            'logs', 'log'      => webcli_cmd_logs($argv),
            'composer'         => $allowShell ? webcli_cmd_composer($argv)         : webcli_denied('WEBCLI_ALLOW_SHELL=false — composer needs a shell'),
            'shell', 'sh', 'exec' => $allowShell ? webcli_cmd_shell($argv)         : webcli_denied('WEBCLI_ALLOW_SHELL=false'),
            'pwd'              => ['ok' => true, 'output' => getcwd() ?: ''],
            'ls', 'dir'        => webcli_cmd_ls($argv),
            'cat'              => webcli_cmd_cat($argv),
            'whoami'           => ['ok' => true, 'output' => webcli_whoami()],
            'phpinfo'          => webcli_cmd_phpinfo($argv),
            'clear', 'cls'     => ['ok' => true, 'output' => '', 'clear' => true],
            default            => ['ok' => false, 'output' => "Unknown command: {$verb}\nType 'help' for available commands."],
        };
    } catch (\Throwable $e) {
        return ['ok' => false, 'output' => "Error: " . $e->getMessage() . "\n\n" . $e->getTraceAsString()];
    }
}

// Shell-like tokenizer with single/double quote handling
function webcli_parse(string $cmd): array {
    $tokens = []; $buf = ''; $quote = null;
    for ($i = 0, $n = strlen($cmd); $i < $n; $i++) {
        $c = $cmd[$i];
        if ($quote !== null) {
            if ($c === '\\' && $i + 1 < $n) { $buf .= $cmd[++$i]; continue; }
            if ($c === $quote) { $quote = null; continue; }
            $buf .= $c;
        } elseif ($c === '"' || $c === "'") {
            $quote = $c;
        } elseif ($c === ' ' || $c === "\t") {
            if ($buf !== '') { $tokens[] = $buf; $buf = ''; }
        } else {
            $buf .= $c;
        }
    }
    if ($buf !== '') $tokens[] = $buf;
    return $tokens;
}

function webcli_denied(string $reason): array { return ['ok' => false, 'output' => "Command disabled: {$reason}"]; }
function webcli_whoami(): string { return function_exists('posix_geteuid') ? (posix_getpwuid(posix_geteuid())['name'] ?? 'unknown') : (getenv('USER') ?: (getenv('USERNAME') ?: 'php-web')); }

// ---------------------------------------------------------------------------
// Artisan (lazy Laravel bootstrap so this file works when the app is broken)
function webcli_boot_laravel(): array {
    static $booted = null;
    if ($booted !== null) return $booted;
    $vendorPath = WEBCLI_CORE . '/vendor/autoload.php';
    if (!is_file($vendorPath)) {
        return $booted = ['ok' => false, 'error' => "vendor/autoload.php not found at {$vendorPath}\nRun `composer install` in core/ first."];
    }
    try {
        require_once $vendorPath;
        $app = require WEBCLI_CORE . '/bootstrap/app.php';
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        return $booted = ['ok' => true, 'app' => $app];
    } catch (\Throwable $e) {
        return $booted = ['ok' => false, 'error' => "Laravel boot failed: " . $e->getMessage()];
    }
}

function webcli_cmd_artisan(array $argv): array {
    if (!$argv) return ['ok' => false, 'output' => "Usage: artisan <command> [args...]\ne.g. artisan route:list"];
    $boot = webcli_boot_laravel();
    if (!$boot['ok']) return ['ok' => false, 'output' => $boot['error']];
    try {
        // Mirror the real artisan script: use ArgvInput so --version, --help,
        // positional args, and options all behave exactly as they would on CLI.
        $input = new \Symfony\Component\Console\Input\ArgvInput(array_merge(['artisan'], $argv));
        $output = new \Symfony\Component\Console\Output\BufferedOutput();
        $kernel = $boot['app']->make(\Illuminate\Contracts\Console\Kernel::class);
        $code = $kernel->handle($input, $output);
        $text = $output->fetch();
        if ($text === '') $text = "(no output; exit code {$code})";
        return ['ok' => $code === 0, 'output' => $text];
    } catch (\Throwable $e) {
        return ['ok' => false, 'output' => "Artisan error: " . $e->getMessage()];
    }
}

// ---------------------------------------------------------------------------
// eval / tinker-like: run PHP in the booted Laravel context
function webcli_cmd_eval(array $argv): array {
    $code = trim(implode(' ', $argv));
    if ($code === '') return ['ok' => false, 'output' => 'Usage: eval <php-code>   e.g. eval User::count()'];
    $boot = webcli_boot_laravel();
    if (!$boot['ok']) return ['ok' => false, 'output' => $boot['error']];
    // Auto-import common Laravel facades and app models
    $prelude = 'use Illuminate\\Support\\Facades\\{DB,Cache,Config,Log,Route,Schema,Storage,Auth,Artisan};';
    ob_start();
    try {
        // Return-value form: single expression
        $result = @eval($prelude . ' return (' . $code . ');');
        $captured = ob_get_clean();
        $outStr = $captured;
        if ($outStr === '') {
            $outStr = webcli_stringify($result);
        }
        return ['ok' => true, 'output' => (string)$outStr];
    } catch (\ParseError|\Error|\Throwable $e) {
        // Fallback: statement form (no return)
        ob_end_clean();
        ob_start();
        try {
            eval($prelude . $code . ';');
            $captured = ob_get_clean();
            return ['ok' => true, 'output' => (string)$captured];
        } catch (\Throwable $e2) {
            ob_end_clean();
            return ['ok' => false, 'output' => "Error: " . $e2->getMessage()];
        }
    }
}

function webcli_stringify($v): string {
    if (is_null($v))   return 'null';
    if (is_bool($v))   return $v ? 'true' : 'false';
    if (is_scalar($v)) return (string)$v;
    if (is_object($v) && method_exists($v, 'toArray')) return json_encode($v->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (is_object($v) && method_exists($v, '__toString')) return (string)$v;
    return json_encode($v, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: var_export($v, true);
}

// ---------------------------------------------------------------------------
// env
function webcli_cmd_env(array $argv): array {
    $sub = strtolower($argv[0] ?? 'list');
    $env = webcli_load_env();
    if ($sub === 'list' || $sub === '') {
        $out = '';
        foreach ($env as $k => $v) {
            $lc = strtolower($k);
            $mask = str_contains($lc, 'password') || str_contains($lc, 'secret') || str_contains($lc, 'key') || str_contains($lc, 'token');
            $shown = $mask ? (strlen($v) > 0 ? str_repeat('*', min(strlen($v), 8)) : '') : $v;
            $out .= "{$k}={$shown}\n";
        }
        return ['ok' => true, 'output' => rtrim($out)];
    }
    if ($sub === 'get') {
        $key = $argv[1] ?? '';
        if ($key === '') return ['ok' => false, 'output' => 'Usage: env get <KEY>'];
        return ['ok' => true, 'output' => $env[$key] ?? '(not set)'];
    }
    if ($sub === 'set') {
        $key = $argv[1] ?? '';
        $val = $argv[2] ?? null;
        if ($key === '' || $val === null) return ['ok' => false, 'output' => 'Usage: env set <KEY> <VALUE>'];
        return webcli_env_set($key, $val);
    }
    return ['ok' => false, 'output' => "Unknown env action: {$sub}\nUse: env [list|get KEY|set KEY VALUE]"];
}

function webcli_env_set(string $key, string $value): array {
    if (!is_readable(WEBCLI_ENV_PATH)) return ['ok' => false, 'output' => WEBCLI_ENV_PATH . ' is not readable'];
    $lines = file(WEBCLI_ENV_PATH, FILE_IGNORE_NEW_LINES);
    if ($lines === false) return ['ok' => false, 'output' => 'Cannot read .env'];
    $found = false;
    foreach ($lines as $i => $l) {
        $trim = ltrim($l);
        if ($trim === '' || str_starts_with($trim, '#') || !str_contains($l, '=')) continue;
        [$k] = explode('=', $l, 2);
        if (trim($k) === $key) { $lines[$i] = $key . '=' . $value; $found = true; break; }
    }
    if (!$found) $lines[] = $key . '=' . $value;
    if (@file_put_contents(WEBCLI_ENV_PATH, implode("\n", $lines) . "\n", LOCK_EX) === false) {
        return ['ok' => false, 'output' => 'Failed to write .env (permissions?)'];
    }
    return ['ok' => true, 'output' => "Set {$key}. Consider `artisan config:clear` to refresh Laravel's config cache."];
}

// ---------------------------------------------------------------------------
// logs
function webcli_cmd_logs(array $argv): array {
    $n = (int)($argv[0] ?? 100);
    $n = max(1, min($n, 5000));
    $logFile = WEBCLI_CORE . '/storage/logs/laravel.log';
    if (!is_readable($logFile)) return ['ok' => false, 'output' => "No log at {$logFile}"];
    $lines = @file($logFile);
    if (!$lines) return ['ok' => true, 'output' => '(empty)'];
    return ['ok' => true, 'output' => implode('', array_slice($lines, -$n))];
}

// ---------------------------------------------------------------------------
// composer / shell (only if allowed)
function webcli_cmd_composer(array $argv): array {
    if (!function_exists('shell_exec') && !function_exists('proc_open')) {
        return ['ok' => false, 'output' => 'shell_exec / proc_open disabled on this host — composer cannot run'];
    }
    $cmd = 'composer ' . implode(' ', array_map('escapeshellarg', $argv));
    $out = @shell_exec('cd ' . escapeshellarg(WEBCLI_CORE) . ' && ' . $cmd . ' 2>&1');
    return ['ok' => true, 'output' => (string)$out];
}

function webcli_cmd_shell(array $argv): array {
    if (!function_exists('shell_exec')) return ['ok' => false, 'output' => 'shell_exec disabled on this host'];
    $cmd = implode(' ', $argv);
    if ($cmd === '') return ['ok' => false, 'output' => 'Usage: shell <command>'];
    $out = @shell_exec($cmd . ' 2>&1');
    return ['ok' => true, 'output' => (string)$out];
}

// ---------------------------------------------------------------------------
// filesystem helpers
function webcli_cmd_ls(array $argv): array {
    $dir = $argv[0] ?? WEBCLI_ROOT;
    if (!is_dir($dir)) return ['ok' => false, 'output' => "Not a directory: {$dir}"];
    $items = @scandir($dir);
    if ($items === false) return ['ok' => false, 'output' => "Cannot read {$dir}"];
    sort($items);
    $out = '';
    foreach ($items as $i) {
        if ($i === '.' || $i === '..') continue;
        $full = $dir . DIRECTORY_SEPARATOR . $i;
        $out .= (is_dir($full) ? '[d] ' : '    ') . $i . "\n";
    }
    return ['ok' => true, 'output' => $out !== '' ? rtrim($out) : '(empty)'];
}

function webcli_cmd_cat(array $argv): array {
    if (!$argv) return ['ok' => false, 'output' => 'Usage: cat <file>'];
    $path = $argv[0];
    if (!is_readable($path)) return ['ok' => false, 'output' => "Cannot read: {$path}"];
    $size = @filesize($path);
    if ($size !== false && $size > 512 * 1024) return ['ok' => false, 'output' => "Refusing to cat file > 512 KB: {$path} (" . number_format($size) . " bytes)"];
    return ['ok' => true, 'output' => (string)@file_get_contents($path)];
}

function webcli_cmd_phpinfo(array $argv): array {
    $section = $argv[0] ?? '';
    ob_start();
    if ($section === 'ext') {
        echo "Loaded extensions:\n" . implode("\n", get_loaded_extensions());
    } elseif ($section === 'ini') {
        echo "Loaded ini: " . php_ini_loaded_file() . "\n";
        echo "Scanned: " . (php_ini_scanned_files() ?: '(none)');
    } else {
        echo "PHP " . PHP_VERSION . " (" . PHP_SAPI . ")\n";
        echo "OS: " . php_uname() . "\n";
        echo "extension_dir: " . ini_get('extension_dir') . "\n";
        echo "memory_limit: " . ini_get('memory_limit') . "\n";
        echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
        echo "shell_exec available: " . (function_exists('shell_exec') ? 'yes' : 'no') . "\n";
    }
    return ['ok' => true, 'output' => ob_get_clean()];
}

// ---------------------------------------------------------------------------
// help
function webcli_cmd_help(): array {
    return ['ok' => true, 'output' => <<<HELP
Commands:

  artisan <cmd> [args...]     Any Artisan command
                              artisan route:list
                              artisan migrate --force
                              artisan cache:clear
                              artisan make:controller FooController

  eval <php>                  Run PHP in the booted Laravel context (Tinker-like)
                              eval User::count()
                              eval config('app.url')
                              eval DB::table('users')->first()

  env                         List env vars (secrets masked)
  env get KEY                 Show one value
  env set KEY VALUE           Update .env (creates key if absent)

  logs [N]                    Tail last N lines of core/storage/logs/laravel.log (default 100)

  composer <cmd>              (requires WEBCLI_ALLOW_SHELL=true) run composer in core/
  shell <cmd>                 (requires WEBCLI_ALLOW_SHELL=true) run raw shell

  pwd                         Current working directory
  ls [dir]                    List files
  cat <file>                  Print file contents (up to 512 KB)
  whoami                      Process user
  phpinfo [ext|ini]           Quick PHP info

  clear                       Clear screen
  help                        This help

Append ?logout to the URL to sign out.
Keyboard: Enter to run, Up/Down history, Ctrl+L clear.
HELP
    ];
}

// ===========================================================================
// HTML PAGES
// ===========================================================================
function webcli_instructions_page(bool $enabled, bool $hasPassword): string {
    $whyEnabled  = $enabled ? '✅' : '❌ set WEBCLI_ENABLED=true';
    $whyPassword = $hasPassword ? '✅' : '❌ set WEBCLI_PASSWORD=<20+ chars>';
    return <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Web CLI</title>
<style>
body{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;background:#0e1116;color:#c9d1d9;margin:0;padding:2rem;line-height:1.6}
h1{color:#58a6ff;margin-top:0}
h2{color:#f0f6fc;font-size:1.1rem;border-bottom:1px solid #30363d;padding-bottom:.4rem;margin-top:2rem}
code,pre{background:#161b22;color:#e6edf3;border:1px solid #30363d;border-radius:6px;padding:.6rem;display:block;overflow:auto}
code{display:inline;padding:.15rem .35rem}
.status{margin:.4rem 0}
.warn{color:#f0883e;border-left:3px solid #f0883e;padding:.6rem .8rem;background:#3c2415;border-radius:4px;margin:1rem 0}
</style></head><body>
<h1>🔒 Web CLI — disabled</h1>
<p>Add the following to <code>core/.env</code>, then reload this page.</p>
<div class="status">Enabled flag: {$whyEnabled}</div>
<div class="status">Password set: {$whyPassword}</div>

<h2>Minimum configuration</h2>
<pre>WEBCLI_ENABLED=true
WEBCLI_PASSWORD=&lt;a strong string of at least 20 characters&gt;</pre>

<h2>Optional hardening (recommended)</h2>
<pre># Restrict to your admin IP(s)
WEBCLI_ALLOWED_IPS=1.2.3.4

# Allow the 'eval' command (Tinker-like PHP eval) — default: true
WEBCLI_ALLOW_EVAL=true

# Allow 'shell' / 'composer' commands — default: false. Only turn on if your host permits shell_exec.
WEBCLI_ALLOW_SHELL=false</pre>

<div class="warn">This endpoint boots your Laravel app and executes arbitrary commands. Serve over HTTPS only.
A leaked password is equivalent to giving away SSH access. All commands are logged to <code>webcli.log</code>.</div>

</body></html>
HTML;
}

function webcli_login_page(?string $error = null): string {
    $err = $error ? '<div class="err">' . htmlspecialchars($error, ENT_QUOTES) . '</div>' : '';
    return <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Web CLI · Sign in</title>
<style>
*{box-sizing:border-box}
body{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;background:#0e1116;color:#c9d1d9;margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center}
form{background:#161b22;border:1px solid #30363d;border-radius:8px;padding:1.5rem;width:min(360px,92vw)}
h1{margin:0 0 1rem;font-size:1.2rem;color:#58a6ff}
input{width:100%;padding:.7rem;border-radius:6px;border:1px solid #30363d;background:#0d1117;color:#e6edf3;font-family:inherit;font-size:1rem}
input:focus{outline:none;border-color:#58a6ff}
button{margin-top:.8rem;width:100%;padding:.7rem;border-radius:6px;border:none;background:#238636;color:#fff;font-weight:bold;cursor:pointer;font-family:inherit}
button:hover{background:#2ea043}
.err{background:#3c1618;border:1px solid #f85149;color:#ffa198;padding:.6rem;border-radius:6px;margin-bottom:.8rem;font-size:.9rem}
label{display:block;margin-bottom:.4rem;font-size:.9rem;color:#8b949e}
</style></head><body>
<form method="POST" autocomplete="off">
<h1>🔐 Web CLI</h1>
{$err}
<label for="p">Password</label>
<input id="p" name="password" type="password" autofocus required>
<button type="submit">Sign in</button>
</form></body></html>
HTML;
}

function webcli_terminal_page(string $csrf, bool $allowEval, bool $allowShell): string {
    $csrf = htmlspecialchars($csrf, ENT_QUOTES);
    $flags = [];
    if (!$allowEval)  $flags[] = 'eval OFF';
    if (!$allowShell) $flags[] = 'shell OFF';
    $flagStr = $flags ? ' · ' . implode(' · ', $flags) : '';
    return <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Web CLI · Terminal</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;background:#0e1116;color:#c9d1d9;display:flex;flex-direction:column}
header{background:#161b22;border-bottom:1px solid #30363d;padding:.6rem 1rem;display:flex;justify-content:space-between;align-items:center;font-size:.9rem}
header h1{font-size:1rem;color:#58a6ff;font-weight:normal}
header a{color:#8b949e;text-decoration:none;margin-left:.8rem}
header a:hover{color:#c9d1d9}
#out{flex:1;overflow-y:auto;padding:1rem;white-space:pre-wrap;word-break:break-word;font-size:.92rem}
.line{margin-bottom:.4rem}
.cmd{color:#79c0ff}
.cmd::before{content:'\$ ';color:#7ee787}
.err{color:#ff7b72}
.ok{color:#c9d1d9}
.dim{color:#8b949e;font-style:italic}
#prompt{background:#161b22;border-top:1px solid #30363d;padding:.6rem 1rem;display:flex;align-items:center}
#prompt::before{content:'\$';color:#7ee787;margin-right:.5rem;font-weight:bold}
#cmd{flex:1;background:transparent;border:none;outline:none;color:#e6edf3;font-family:inherit;font-size:1rem}
#cmd:disabled{opacity:.5}
::-webkit-scrollbar{width:10px;height:10px}
::-webkit-scrollbar-track{background:#0e1116}
::-webkit-scrollbar-thumb{background:#30363d;border-radius:5px}
.spinner{display:inline-block;animation:spin 1s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
</style></head><body>
<header><h1>Web CLI · Laravel/Artisan{$flagStr}</h1><nav><a href="?logout">logout</a></nav></header>
<div id="out"><div class="line dim">Web CLI ready. Type <span class="cmd">help</span> for commands. Ctrl+L to clear.</div></div>
<form id="prompt" onsubmit="return false"><input id="cmd" autocomplete="off" spellcheck="false" autofocus placeholder="Type a command and press Enter"></form>
<script>
(function(){
  const CSRF="{$csrf}";
  const out=document.getElementById('out');
  const input=document.getElementById('cmd');
  const history=JSON.parse(localStorage.getItem('webcli_hist')||'[]');
  let histIdx=history.length;
  function print(cls,text){
    const div=document.createElement('div');div.className='line '+cls;
    div.textContent=text;out.appendChild(div);out.scrollTop=out.scrollHeight;
  }
  function pushHist(cmd){
    if(!cmd)return;
    if(history[history.length-1]!==cmd){history.push(cmd);if(history.length>200)history.shift();
      localStorage.setItem('webcli_hist',JSON.stringify(history));}
    histIdx=history.length;
  }
  async function run(cmd){
    print('cmd',cmd);
    if(cmd==='clear'||cmd==='cls'){out.innerHTML='';return;}
    input.disabled=true;
    const spin=document.createElement('div');spin.className='line dim';spin.innerHTML='<span class="spinner">◐</span> running…';
    out.appendChild(spin);out.scrollTop=out.scrollHeight;
    try{
      const body=new URLSearchParams({cmd,_csrf:CSRF});
      const res=await fetch(window.location.pathname,{method:'POST',body,credentials:'same-origin'});
      const json=await res.json();
      spin.remove();
      if(json.clear){out.innerHTML='';}
      else{print(json.ok?'ok':'err', json.output||'(no output)');}
    }catch(e){
      spin.remove();
      print('err','Network/JSON error: '+e.message);
    }finally{
      input.disabled=false;input.value='';input.focus();
    }
  }
  input.addEventListener('keydown',ev=>{
    if(ev.key==='Enter'){ev.preventDefault();const v=input.value.trim();if(!v)return;pushHist(v);run(v);}
    else if(ev.key==='ArrowUp'){ev.preventDefault();if(histIdx>0){histIdx--;input.value=history[histIdx]||'';}}
    else if(ev.key==='ArrowDown'){ev.preventDefault();if(histIdx<history.length-1){histIdx++;input.value=history[histIdx]||'';}else{histIdx=history.length;input.value='';}}
    else if(ev.ctrlKey && (ev.key==='l'||ev.key==='L')){ev.preventDefault();out.innerHTML='';}
  });
  document.addEventListener('click',()=>input.focus());
})();
</script></body></html>
HTML;
}
