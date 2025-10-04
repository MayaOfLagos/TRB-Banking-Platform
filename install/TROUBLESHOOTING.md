# Installation Troubleshooting Guide

## 🔧 Common Installation Issues & Solutions

### ❌ "Problem Occurred When Importing Database!"

**Causes:**
1. Database is not empty (contains tables from previous installation)
2. Database user lacks required privileges
3. DEFINER statement requires SUPER privileges
4. Large SQL file timeout

**Solutions:**

#### ✅ Solution 1: Empty Your Database
```sql
-- Option A: Drop all tables (via phpMyAdmin or SQL)
SET FOREIGN_KEY_CHECKS = 0;
-- Then drop each table manually

-- Option B: Drop and recreate database
DROP DATABASE your_database_name;
CREATE DATABASE your_database_name;
```

#### ✅ Solution 2: Grant Full Privileges
```sql
-- For local development (XAMPP/WAMP)
GRANT ALL PRIVILEGES ON database_name.* TO 'username'@'localhost';
FLUSH PRIVILEGES;

-- For shared hosting - contact your hosting provider
```

#### ✅ Solution 3: Use Updated Installer
The latest version (pushed to GitHub) automatically:
- ✅ Removes DEFINER statements
- ✅ Splits SQL into individual statements
- ✅ Handles large SQL files properly
- ✅ Provides transaction rollback on errors

---

## 📋 Pre-Installation Checklist

### Server Requirements
- [ ] PHP 8.3 or higher
- [ ] MySQL 8.0+ or MariaDB 10.6+
- [ ] PDO PHP Extension enabled
- [ ] Mbstring PHP Extension enabled
- [ ] Fileinfo PHP Extension enabled
- [ ] OpenSSL PHP Extension enabled
- [ ] JSON PHP Extension enabled
- [ ] cURL PHP Extension enabled

### Database Requirements
- [ ] Database created (empty, no tables)
- [ ] Database user with full privileges:
  - CREATE
  - DROP
  - ALTER
  - INSERT
  - UPDATE
  - DELETE
  - SELECT
  - INDEX
  - REFERENCES
- [ ] Correct database credentials

### File Permissions
- [ ] `/core/storage` directory writable (755 or 777)
- [ ] `/core/bootstrap/cache` directory writable (755 or 777)
- [ ] `/install` directory accessible

---

## 🐛 Specific Error Messages

### "Database Credential is Not Valid"
**Cause:** Wrong database host, name, username, or password

**Solution:**
1. Verify credentials in your hosting control panel
2. Common database hosts:
   - `localhost` (most common)
   - `127.0.0.1`
   - `mysql.yourdomain.com` (shared hosting)
3. Test connection using phpMyAdmin or MySQL Workbench first

---

### "MariaDB 10.6+ Or MySQL 8.0+ Required"
**Cause:** Your database version is too old

**Solution:**
1. Check version: `SELECT VERSION();` in phpMyAdmin
2. Upgrade database server (contact hosting provider)
3. Alternative: Use a different server/hosting that supports newer versions

---

### "There is a problem with creating the database"
**Cause:** cPanel credentials incorrect or API disabled

**Solution:**
1. Verify cPanel username and password
2. Use "Existing Database" option instead
3. Create database manually via cPanel > MySQL Databases
4. Ensure cPanel API is enabled (some hosts disable it)

---

### "Problem Occurred When Writing Environment File"
**Cause:** `/core/.env` file not writable

**Solution:**
```bash
# Via SSH or File Manager
chmod 644 core/.env

# Or make parent directory writable temporarily
chmod 755 core/
```

---

## 🔍 Debugging Steps

### Step 1: Test Database Connection
Create a test file `test_db.php` in install folder:
```php
<?php
$host = 'localhost';
$dbname = 'your_database';
$user = 'your_username';
$pass = 'your_password';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    echo "✅ Database connection successful!<br>";
    echo "Version: " . $db->query('SELECT VERSION()')->fetchColumn();
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
```

### Step 2: Check PHP Extensions
Create `test_extensions.php`:
```php
<?php
$required = ['pdo', 'pdo_mysql', 'mbstring', 'fileinfo', 'openssl', 'json', 'curl'];
foreach ($required as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "$status $ext<br>";
}
?>
```

### Step 3: Test File Permissions
```php
<?php
$paths = [
    '../core/storage',
    '../core/bootstrap/cache',
    '../core'
];

foreach ($paths as $path) {
    $writable = is_writable($path) ? '✅ Writable' : '❌ Not writable';
    echo "$path - $writable<br>";
}
?>
```

---

## 💡 Best Practices

### For Local Development (XAMPP/WAMP/MAMP)
1. Use `root` user with no password (default)
2. Database host: `localhost` or `127.0.0.1`
3. Ensure MySQL service is running
4. Use phpMyAdmin to create empty database first

### For Shared Hosting
1. Create database via cPanel/Plesk
2. Note the automatically prefixed database name (e.g., `cpanel_dbname`)
3. Create database user with full privileges
4. Use provided database host (often not localhost)
5. Test credentials in phpMyAdmin before installation

### For VPS/Dedicated Servers
1. Ensure MySQL is installed and running
2. Create database and user with full privileges:
```sql
CREATE DATABASE viserbank;
CREATE USER 'viserbank_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON viserbank.* TO 'viserbank_user'@'localhost';
FLUSH PRIVILEGES;
```
3. Configure firewall if needed
4. Set proper file permissions (755 for directories, 644 for files)

---

## 🚨 Emergency Database Reset

If installation fails multiple times, completely reset:

### Via phpMyAdmin
1. Select your database
2. Check "Check All" tables
3. Click "Drop" from dropdown
4. Confirm deletion
5. Run installer again

### Via SQL
```sql
-- Drop all tables
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS admins, admin_notifications, users, transactions, 
/* ... list all tables ... */;
SET FOREIGN_KEY_CHECKS = 1;

-- Or drop entire database and recreate
DROP DATABASE your_database_name;
CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Via Command Line
```bash
# Backup first (optional)
mysqldump -u username -p database_name > backup.sql

# Drop and recreate
mysql -u username -p -e "DROP DATABASE database_name;"
mysql -u username -p -e "CREATE DATABASE database_name;"
```

---

## 📞 Still Having Issues?

### Collect This Information:
1. **Server Info:**
   - PHP Version: `<?php echo PHP_VERSION; ?>`
   - MySQL Version: `SELECT VERSION();`
   - Server OS: Linux/Windows
   - Hosting Type: Shared/VPS/Local

2. **Error Details:**
   - Exact error message
   - Installation step where it failed
   - Browser console errors (F12)

3. **Database Info:**
   - Database size (empty/has tables)
   - User privileges list
   - Connection test result

### Get Help:
- **GitHub Issues:** https://github.com/MayaOfLagos/TRB-Banking-Platform/issues
- **Documentation:** Check README.md and setup guides
- **Community Support:** ViserLab support portal

---

## ✅ Successful Installation Checklist

After successful installation:
- [ ] Admin login works at `/admin`
- [ ] `.env` file created in `/core` directory
- [ ] Database contains 50+ tables
- [ ] Application URL is correct
- [ ] File permissions are secure (755/644)
- [ ] **Delete or rename `/install` folder** for security
- [ ] Change default admin credentials
- [ ] Configure email settings
- [ ] Set up cron jobs for automation
- [ ] Test user registration and login

---

## 🔒 Post-Installation Security

```bash
# 1. Remove or rename install folder
mv install install_backup
# or
rm -rf install

# 2. Secure .env file
chmod 600 core/.env

# 3. Set proper permissions
find core/storage -type d -exec chmod 755 {} \;
find core/storage -type f -exec chmod 644 {} \;
find core/bootstrap/cache -type d -exec chmod 755 {} \;

# 4. Disable directory listing (add to .htaccess)
Options -Indexes
```

---

**Last Updated:** October 4, 2025  
**Installer Version:** 2.0 (with DEFINER fix and split SQL execution)
