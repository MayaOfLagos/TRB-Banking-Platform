# Post-Installation Setup Guide

## 🚨 Error: "vendor/autoload.php not found"

If you see this error after successful installation:
```
PHP Fatal error: Failed opening required 'core/vendor/autoload.php'
```

This means **Composer dependencies are missing**. Follow the solutions below:

---

## ✅ Solution 1: Install via SSH (Recommended)

**Step 1: Connect to your server via SSH**
```bash
ssh your_username@hantscapital.com
# Or use PuTTY on Windows
```

**Step 2: Navigate to your application directory**
```bash
cd /home/hantscap/public_html/my.hantscapital.com/core
```

**Step 3: Install Composer dependencies**
```bash
composer install --optimize-autoloader --no-dev
```

**Step 4: Set proper permissions**
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs
```

**Step 5: Clear and cache configuration**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Step 6: Test your site**
Visit: https://my.hantscapital.com

---

## ✅ Solution 2: Upload vendor folder (If no SSH access)

If you don't have SSH access, you need to:

**Step 1: Generate vendor folder locally**

On your local machine (XAMPP):
```bash
cd C:\xampp\htdocs\core
composer install --optimize-autoloader --no-dev
```

**Step 2: Compress vendor folder**
```bash
# Create a zip file of the vendor folder
# Right-click vendor folder → Send to → Compressed folder
# Or use 7-Zip/WinRAR
```

**Step 3: Upload via FTP/cPanel File Manager**
1. Go to cPanel → File Manager
2. Navigate to: `/home/hantscap/public_html/my.hantscapital.com/core/`
3. Upload `vendor.zip`
4. Extract it (right-click → Extract)
5. Delete the zip file after extraction

**Step 4: Set permissions via cPanel**
- File Manager → Select `storage` folder → Change Permissions → 755
- File Manager → Select `bootstrap/cache` → Change Permissions → 755

---

## ✅ Solution 3: Use cPanel Terminal (If available)

Some cPanel accounts have Terminal access:

**Step 1: Open cPanel Terminal**
- cPanel → Advanced → Terminal

**Step 2: Run these commands**
```bash
cd public_html/my.hantscapital.com/core
composer install --optimize-autoloader --no-dev
chmod -R 755 storage bootstrap/cache
php artisan config:cache
```

---

## 🔧 After Composer Install

Once vendor folder is installed, run these commands:

### **1. Clear all caches**
```bash
cd /home/hantscap/public_html/my.hantscapital.com/core
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### **2. Optimize application**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### **3. Set proper permissions**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

### **4. Generate application key (if needed)**
```bash
php artisan key:generate
```

---

## 📋 Verification Checklist

After completing the steps above, verify:

- [ ] `core/vendor` folder exists and has files
- [ ] `core/vendor/autoload.php` file exists
- [ ] `core/.env` file exists with correct settings
- [ ] `core/storage` folder has 755 permissions
- [ ] `core/bootstrap/cache` folder has 755 permissions
- [ ] Website loads without errors
- [ ] Admin login works at `/admin`

---

## 🚀 First Time Setup Tasks

After fixing the vendor issue:

### **1. Delete Install Folder (IMPORTANT)**
```bash
cd /home/hantscap/public_html/my.hantscapital.com
rm -rf install
# Or via File Manager: Delete the install folder
```

### **2. Configure Cron Jobs**

Add this cron job in cPanel:
```bash
# Run every minute
* * * * * cd /home/hantscap/public_html/my.hantscapital.com/core && php artisan schedule:run >> /dev/null 2>&1
```

**How to add in cPanel:**
1. cPanel → Advanced → Cron Jobs
2. Common Settings: Once Per Minute (* * * * *)
3. Command: `/usr/local/bin/php /home/hantscap/public_html/my.hantscapital.com/core/artisan schedule:run`
4. Click "Add New Cron Job"

### **3. Configure Queue Worker (Optional)**

If you want to process background jobs:
```bash
# Add to cron jobs
* * * * * cd /home/hantscap/public_html/my.hantscapital.com/core && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

### **4. Test Admin Login**

Visit: `https://my.hantscapital.com/admin`

**Default Credentials** (if you didn't change during install):
- Username: The username you entered during installation
- Email: The email you entered
- Password: The password you entered

### **5. Configure Email Settings**

1. Login to admin panel
2. Go to: Settings → Email Configuration
3. Add your SMTP details:
   - Host: smtp.gmail.com (or your provider)
   - Port: 587 (TLS) or 465 (SSL)
   - Username: your-email@domain.com
   - Password: your-email-password
   - Encryption: TLS or SSL

### **6. Configure Site Settings**

1. Admin Panel → General Settings
2. Update:
   - Site Name
   - Site Logo
   - Site Favicon
   - Timezone
   - Currency
   - Contact Information

---

## 🐛 Common Issues After Installation

### **Issue 1: "500 Internal Server Error"**

**Solution:**
```bash
cd /home/hantscap/public_html/my.hantscapital.com/core
chmod -R 755 storage bootstrap/cache
php artisan cache:clear
php artisan config:clear
```

### **Issue 2: "Page Not Found (404)"**

**Solution:**
Check `.htaccess` file exists in root directory:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### **Issue 3: "Class not found" errors**

**Solution:**
```bash
cd /home/hantscap/public_html/my.hantscapital.com/core
composer dump-autoload
php artisan cache:clear
php artisan config:cache
```

### **Issue 4: Database connection errors**

**Solution:**
1. Check `core/.env` file has correct database credentials
2. Verify database host (often `localhost` or `127.0.0.1`)
3. Test connection via phpMyAdmin
4. Clear config cache:
```bash
php artisan config:clear
php artisan config:cache
```

---

## 📞 Getting Help

### **Check Error Logs**
```bash
# Via SSH
tail -f /home/hantscap/public_html/my.hantscapital.com/core/storage/logs/laravel.log

# Via cPanel
File Manager → core/storage/logs/ → View laravel.log
```

### **Enable Debug Mode (Temporarily)**
Edit `core/.env`:
```env
APP_DEBUG=true
```
**Remember to set back to `false` in production!**

### **Test Composer**
```bash
composer --version
# If not found, contact your host to install Composer
```

### **Check PHP Version**
```bash
php -v
# Should be PHP 8.3 or higher
```

---

## 🎯 Quick Fix Script

Create a file `fix-installation.sh` and run it:

```bash
#!/bin/bash
cd /home/hantscap/public_html/my.hantscapital.com/core

echo "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod 644 .env

echo "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Done! Visit your website now."
```

**Run it:**
```bash
chmod +x fix-installation.sh
./fix-installation.sh
```

---

## ✅ Success Indicators

Your installation is complete when:
- ✅ Website loads without errors
- ✅ Admin panel accessible at `/admin`
- ✅ Login works correctly
- ✅ No PHP errors in error_log
- ✅ Install folder deleted
- ✅ Cron jobs configured
- ✅ Email sending works

---

## 🔒 Security Checklist

After installation:
- [ ] Delete `install` folder
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set proper file permissions (755 for folders, 644 for files)
- [ ] Secure `.env` file (chmod 600)
- [ ] Change default admin password
- [ ] Enable HTTPS/SSL
- [ ] Configure firewall rules
- [ ] Set up regular backups
- [ ] Update `APP_URL` in `.env` to use https://

---

**Need Help?**
- GitHub Issues: https://github.com/MayaOfLagos/TRB-Banking-Platform/issues
- WhatsApp: +234 812 332 6360
- Documentation: Check README.md in project root

**Last Updated:** October 4, 2025
