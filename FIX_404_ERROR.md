# 🚨 404 Error Fix Guide - "Not Found" on Admin Panel

## Problem
After uploading vendor folder, accessing `/admin` shows:
```
Not Found
The requested URL was not found on this server.
Additionally, a 404 Not Found error was encountered while trying to use an ErrorDocument to handle the request.
```

---

## 🎯 Root Cause

Laravel applications need proper routing configuration. The issue is usually:
1. **Wrong document root** - Server pointing to wrong folder
2. **Missing .htaccess files** - URL rewriting not configured
3. **mod_rewrite disabled** - Apache module not enabled
4. **Permission issues** - Server can't read files

---

## ✅ Solution 1: Configure Document Root (MOST IMPORTANT)

Your Laravel app structure should be:
```
/home/hantscap/public_html/my.hantscapital.com/
├── index.php                 ← Root redirector
├── .htaccess                 ← Root htaccess
├── core/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── public/              ← This should be document root!
│   │   ├── index.php        ← Main entry point
│   │   └── .htaccess        ← Public htaccess
│   ├── vendor/              ← Your uploaded folder
│   └── .env
└── install/
```

### **Option A: Set Document Root to `core/public` (Recommended)**

**Via cPanel:**
1. **cPanel** → **Domains** → **Domains**
2. Find domain: `my.hantscapital.com`
3. Click **Manage**
4. Under **Document Root**, change to:
   ```
   /home/hantscap/public_html/my.hantscapital.com/core/public
   ```
5. Click **Save**
6. Wait 2-5 minutes for changes to apply

**Result:** Your URLs will work directly:
- ✅ `https://my.hantscapital.com` → Homepage
- ✅ `https://my.hantscapital.com/admin` → Admin panel

---

### **Option B: Use Root Redirector (If can't change document root)**

If your hosting doesn't allow changing document root, use this method:

**Step 1: Create/Edit root index.php**

File: `/home/hantscap/public_html/my.hantscapital.com/index.php`
```php
<?php
/**
 * Laravel Application Loader
 * This file redirects all requests to the Laravel public directory
 */

// Get the current request URI
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Remove trailing slash
$uri = rtrim($uri, '/');

// Define the public directory path
$publicPath = __DIR__ . '/core/public';

// Check if the file exists in public directory
if ($uri !== '' && file_exists($publicPath . $uri)) {
    return false;
}

// Load Laravel application
require_once $publicPath . '/index.php';
```

**Step 2: Create/Edit root .htaccess**

File: `/home/hantscap/public_html/my.hantscapital.com/.htaccess`
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect all requests to core/public
    RewriteRule ^(.*)$ core/public/$1 [L]
</IfModule>
```

**Step 3: Verify core/public/.htaccess exists**

File: `/home/hantscap/public_html/my.hantscapital.com/core/public/.htaccess`
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## ✅ Solution 2: Enable mod_rewrite (If disabled)

**Via SSH:**
```bash
# Check if mod_rewrite is enabled
apache2ctl -M | grep rewrite
# Should show: rewrite_module (shared)
```

**Via .htaccess (Force enable):**

Add to root `.htaccess`:
```apache
# Enable mod_rewrite
<IfModule !mod_rewrite.c>
    <IfModule mod_alias.c>
        RedirectMatch 307 ^/$ /core/public/
        RedirectMatch 307 ^/(.*)$ /core/public/$1
    </IfModule>
</IfModule>
```

**Contact hosting support if mod_rewrite is disabled** - they need to enable it.

---

## ✅ Solution 3: Check File Permissions

**Via SSH:**
```bash
cd /home/hantscap/public_html/my.hantscapital.com

# Set correct permissions
chmod 644 index.php
chmod 644 .htaccess
chmod 755 core
chmod 644 core/public/index.php
chmod 644 core/public/.htaccess
chmod -R 755 core/storage
chmod -R 755 core/bootstrap/cache
```

**Via cPanel File Manager:**
1. Right-click each file → Permissions
2. Set:
   - Files: `644` (Owner: read/write, Group: read, World: read)
   - Folders: `755` (Owner: all, Group: read/execute, World: read/execute)

---

## ✅ Solution 4: Clear Laravel Cache

Sometimes cached routes cause 404 errors.

**Via SSH:**
```bash
cd /home/hantscap/public_html/my.hantscapital.com/core

php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
```

**Via PHP script (if no SSH):**

Create file: `/home/hantscap/public_html/my.hantscapital.com/clear-cache.php`
```php
<?php
define('LARAVEL_START', microtime(true));

require __DIR__.'/core/vendor/autoload.php';
$app = require_once __DIR__.'/core/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Clearing caches...\n";
$kernel->call('route:clear');
echo "✓ Routes cleared\n";
$kernel->call('config:clear');
echo "✓ Config cleared\n";
$kernel->call('cache:clear');
echo "✓ Cache cleared\n";
$kernel->call('view:clear');
echo "✓ Views cleared\n";

echo "\nCaching...\n";
$kernel->call('config:cache');
echo "✓ Config cached\n";
$kernel->call('route:cache');
echo "✓ Routes cached\n";

echo "\nDone! Delete this file now.\n";
```

Visit: `https://my.hantscapital.com/clear-cache.php`

Then **delete the file** for security!

---

## ✅ Solution 5: Check .env Configuration

Verify `core/.env` has correct APP_URL:

```env
APP_URL=https://my.hantscapital.com
```

**NOT:**
```env
APP_URL=http://localhost  ← Wrong!
APP_URL=https://my.hantscapital.com/core  ← Wrong!
```

After changing, clear config:
```bash
php artisan config:clear
php artisan config:cache
```

---

## 🔍 Debugging Steps

### **Step 1: Check if Laravel is loading**

Create test file: `/home/hantscap/public_html/my.hantscapital.com/test.php`
```php
<?php
phpinfo();
```

Visit: `https://my.hantscapital.com/test.php`
- If shows PHP info → Server is working
- If 404 → Server configuration issue
- Delete file after testing

### **Step 2: Check Laravel routing**

Create: `/home/hantscap/public_html/my.hantscapital.com/core/public/test-laravel.php`
```php
<?php
echo "Laravel public directory is accessible!";
echo "<br>Document root: " . $_SERVER['DOCUMENT_ROOT'];
echo "<br>Script: " . $_SERVER['SCRIPT_FILENAME'];
```

Visit: `https://my.hantscapital.com/test-laravel.php`
- If shows message → Routing working
- If 404 → .htaccess or document root issue

### **Step 3: Test .htaccess**

Create: `/home/hantscap/public_html/my.hantscapital.com/.htaccess.test`
```apache
# Test file
RewriteEngine On
```

Rename to `.htaccess` and test site:
- If site breaks → mod_rewrite disabled
- If site works → .htaccess is being read

### **Step 4: Check error logs**

**Via cPanel:**
1. cPanel → Metrics → Errors
2. Look for recent errors

**Via SSH:**
```bash
tail -f /home/hantscap/public_html/my.hantscapital.com/core/storage/logs/laravel.log
tail -f /home/hantscap/public_html/error_log
```

---

## 📋 Quick Fix Checklist

Work through this in order:

- [ ] **Step 1:** Change document root to `core/public` (cPanel → Domains)
- [ ] **Step 2:** Verify `.htaccess` files exist in root and `core/public`
- [ ] **Step 3:** Set permissions (644 for files, 755 for folders)
- [ ] **Step 4:** Clear Laravel cache (`php artisan cache:clear`)
- [ ] **Step 5:** Check `APP_URL` in `core/.env`
- [ ] **Step 6:** Test: Visit `https://my.hantscapital.com`
- [ ] **Step 7:** Test: Visit `https://my.hantscapital.com/admin`

---

## 🎯 Most Common Solution

**90% of the time, this is the fix:**

1. **Set document root** to `/home/hantscap/public_html/my.hantscapital.com/core/public`
2. Wait 5 minutes
3. Visit site
4. Done! ✅

---

## 💡 Alternative: Use Subdomain

If you can't fix the main domain, create a subdomain:

**Via cPanel:**
1. cPanel → Domains → Subdomains
2. Create: `app.hantscapital.com`
3. Document Root: `/home/hantscap/public_html/my.hantscapital.com/core/public`
4. Create subdomain
5. Update `.env`:
   ```env
   APP_URL=https://app.hantscapital.com
   ```
6. Clear cache: `php artisan config:cache`

Now access via: `https://app.hantscapital.com/admin`

---

## 📞 Still Having Issues?

### **Provide this information for support:**

1. **Current Setup:**
   ```
   Document Root: ___________________
   APP_URL in .env: ___________________
   ```

2. **What URLs you're trying:**
   ```
   https://my.hantscapital.com → (Result: _______)
   https://my.hantscapital.com/admin → (Result: _______)
   https://my.hantscapital.com/core/public → (Result: _______)
   ```

3. **Error log excerpt:**
   ```
   (Paste last 5 lines from error_log)
   ```

4. **File structure:**
   ```bash
   ls -la /home/hantscap/public_html/my.hantscapital.com/
   ls -la /home/hantscap/public_html/my.hantscapital.com/core/public/
   ```

---

## ✅ Success Verification

Your site is fixed when:
- ✅ `https://my.hantscapital.com` shows homepage
- ✅ `https://my.hantscapital.com/admin` shows login page
- ✅ No 404 errors
- ✅ Can login to admin panel
- ✅ Assets (CSS/JS) loading properly

---

## 🔒 After Everything Works

1. **Delete test files:**
   - `test.php`
   - `test-laravel.php`
   - `clear-cache.php`

2. **Delete install folder:**
   ```bash
   rm -rf /home/hantscap/public_html/my.hantscapital.com/install
   ```

3. **Set production mode:**
   ```env
   APP_DEBUG=false
   APP_ENV=production
   ```

4. **Clear cache again:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

**Quick Answer:** Change your domain's **document root** to point to `core/public` folder in cPanel → Domains → Manage → Document Root

**Last Updated:** October 4, 2025
