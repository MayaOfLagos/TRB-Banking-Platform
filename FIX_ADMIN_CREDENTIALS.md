# 🔐 Fix Admin Credentials After Installation

## Problem
After installation, the admin credentials you entered during setup didn't update the database. You're unable to login with your chosen credentials.

---

## 🎯 What Went Wrong

**In older installer versions (before Oct 4, 2025):**
- Database was imported using `mysqli`
- Admin credentials update tried to use `$db` (PDO) which was no longer available
- The UPDATE query silently failed
- You're stuck with default/unknown credentials

**This has been fixed in the latest version!** But if you already installed, here's how to fix it:

---

## ✅ Solution 1: Reset Admin Credentials Manually (Fastest)

### **Via phpMyAdmin:**

1. **Login to phpMyAdmin** (cPanel → Databases → phpMyAdmin)
2. Select your database: `trb_banking` (or your database name)
3. Click on **admins** table
4. Find the row with `id=1` (the main admin)
5. Click **Edit** (pencil icon)
6. Update these fields:
   - **username**: `your_desired_username`
   - **email**: `your_email@domain.com`
   - **password**: Leave this for now (we'll set it via command)
7. Click **Go** to save

### **Generate Password Hash:**

**Option A: Via Laravel Tinker (Best)**
```bash
cd /home/hantscap/public_html/my.hantscapital.com/core
php artisan tinker
# Then type:
echo password_hash('YourNewPassword123!', PASSWORD_DEFAULT);
# Copy the output hash
exit
```

**Option B: Via Online Tool**
- Go to: https://bcrypt-generator.com/
- Enter your password
- Rounds: 10
- Copy the generated hash (starts with `$2y$10$`)

**Option C: Via PHP Script**

Create file: `generate-password.php` in your root:
```php
<?php
if (isset($_GET['password'])) {
    echo password_hash($_GET['password'], PASSWORD_DEFAULT);
} else {
    echo "Usage: generate-password.php?password=YourPassword";
}
?>
```

Visit: `https://my.hantscapital.com/generate-password.php?password=YourNewPassword123`

**Delete the file after use!**

### **Update Password in Database:**

1. Copy the generated hash
2. Back to phpMyAdmin → admins table
3. Edit the admin row (id=1)
4. Paste hash in **password** field
5. Save

**Done!** Login with your new credentials at `/admin`

---

## ✅ Solution 2: Use SQL Query (Advanced)

**Via phpMyAdmin SQL tab:**

```sql
-- Update admin username, email, and password
-- Replace values with your desired credentials

UPDATE admins 
SET 
    username = 'yourusername',
    email = 'youremail@domain.com',
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE id = 1;

-- The password hash above is for: "password"
-- Generate your own hash using one of the methods above!
```

**Generate your password hash first, then replace the hash in the query!**

---

## ✅ Solution 3: Use SSH/Terminal Command

```bash
cd /home/hantscap/public_html/my.hantscapital.com/core

# Generate password hash
php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"

# Copy the output, then run this (replace values):
php artisan tinker

# In tinker, run:
$admin = App\Models\Admin::find(1);
$admin->username = 'yourusername';
$admin->email = 'youremail@domain.com';
$admin->password = bcrypt('YourNewPassword');
$admin->save();
exit
```

---

## ✅ Solution 4: Create Admin Reset Script

Create file: `reset-admin.php` in `/home/hantscap/public_html/my.hantscapital.com/`:

```php
<?php
/**
 * Admin Credentials Reset Script
 * DELETE THIS FILE AFTER USE!
 */

// Configuration
$db_host = 'localhost';
$db_name = 'your_database_name';  // Change this
$db_user = 'your_database_user';  // Change this
$db_pass = 'your_database_pass';  // Change this

// New admin credentials
$new_username = 'admin';          // Change this
$new_email = 'admin@yourdomain.com'; // Change this
$new_password = 'NewSecurePassword123!'; // Change this

try {
    // Connect to database
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($mysqli->connect_error) {
        die("❌ Connection failed: " . $mysqli->connect_error);
    }
    
    // Escape values
    $username = $mysqli->real_escape_string($new_username);
    $email = $mysqli->real_escape_string($new_email);
    $password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update admin
    $query = "UPDATE admins SET username='$username', email='$email', password='$password' WHERE id=1";
    
    if ($mysqli->query($query)) {
        echo "✅ <strong>Admin credentials updated successfully!</strong><br><br>";
        echo "<strong>Login Details:</strong><br>";
        echo "URL: <a href='/admin'>/admin</a><br>";
        echo "Username: <strong>$new_username</strong><br>";
        echo "Email: <strong>$new_email</strong><br>";
        echo "Password: <strong>$new_password</strong><br><br>";
        echo "⚠️ <strong>IMPORTANT:</strong> Delete this file immediately!<br>";
        echo "<code>rm reset-admin.php</code>";
    } else {
        echo "❌ Error updating admin: " . $mysqli->error;
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
```

**Usage:**
1. Edit the configuration values in the script
2. Upload to your server root
3. Visit: `https://my.hantscapital.com/reset-admin.php`
4. **DELETE the file immediately after use!**

---

## ✅ Solution 5: Reinstall (Fresh Start)

If nothing works, you can reinstall:

### **Step 1: Backup Database (Optional)**
```bash
mysqldump -u username -p database_name > backup.sql
```

### **Step 2: Drop All Tables**
Via phpMyAdmin:
1. Select database
2. Check "Check All"
3. "With selected" → Drop
4. Confirm

### **Step 3: Run Installer Again**
1. Delete: `core/.env`
2. Visit: `https://my.hantscapital.com/install/`
3. Follow installation steps
4. Enter your desired admin credentials

**The new installer version (Oct 4, 2025+) has this bug fixed!**

---

## 🔍 Verify Admin Update Worked

### **Check via phpMyAdmin:**
```sql
SELECT id, username, email, created_at, updated_at 
FROM admins 
WHERE id = 1;
```

Should show your new username and email.

### **Check via SSH:**
```bash
cd /home/hantscap/public_html/my.hantscapital.com/core
php artisan tinker
App\Models\Admin::find(1);
exit
```

Should display your admin details.

---

## 🚀 After Fixing Credentials

1. **Login to admin panel**: `https://my.hantscapital.com/admin`
2. **Change password via admin panel**: Profile → Security → Change Password
3. **Enable 2FA** (if available): Profile → Security → Two-Factor Authentication
4. **Delete install folder**:
   ```bash
   rm -rf /home/hantscap/public_html/my.hantscapital.com/install
   ```
5. **Delete any temporary files**:
   - `reset-admin.php`
   - `generate-password.php`
   - `test.php`

---

## 🔐 Password Security Tips

**Strong passwords should have:**
- ✅ At least 12 characters
- ✅ Uppercase letters (A-Z)
- ✅ Lowercase letters (a-z)
- ✅ Numbers (0-9)
- ✅ Special characters (!@#$%^&*)
- ❌ NO dictionary words
- ❌ NO personal information

**Example strong passwords:**
- `MyB@nk!2025#Secure`
- `Tr3@sur3B0x$2025!`
- `C@pital#Hantz99!`

---

## 🐛 Troubleshooting

### **Issue: "Table 'admins' doesn't exist"**
**Solution:** Database wasn't imported. Re-run installation.

### **Issue: "Invalid credentials" after password reset**
**Solution:** Clear Laravel cache:
```bash
php artisan cache:clear
php artisan config:clear
```

### **Issue: Password hash doesn't work**
**Solution:** Make sure hash starts with `$2y$10$` and is exactly 60 characters long.

### **Issue: "Admin not found"**
**Solution:** Check if admin exists:
```sql
SELECT * FROM admins WHERE id=1;
```
If no results, insert default admin:
```sql
INSERT INTO admins (id, name, email, username, password, status, created_at, updated_at) 
VALUES (1, 'Admin', 'admin@yourdomain.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW());
```
(Password is: "password" - change after login!)

---

## 📊 Prevention (For Future Installations)

**Always use the latest installer:**
1. Pull latest version from GitHub:
   ```bash
   git pull origin master
   ```
2. Or download latest release
3. Check `install/index.php` date: Should be Oct 4, 2025 or newer

**Verify during installation:**
- Wait for success message
- Check success message shows your credentials
- Test login immediately after installation
- Don't delete install folder until verified

---

## ✅ Verification Checklist

After fixing admin credentials:

- [ ] Can login at `/admin` with new username
- [ ] Can login with new password
- [ ] Email address is correct in profile
- [ ] Can access all admin panel sections
- [ ] Can create test user/transaction
- [ ] Cleared all caches
- [ ] Deleted install folder
- [ ] Deleted temporary scripts
- [ ] Changed to strong password
- [ ] Enabled 2FA (if available)

---

## 📞 Need Help?

If you're still having issues:

1. **Check error logs:**
   ```bash
   tail -f core/storage/logs/laravel.log
   ```

2. **Enable debug mode temporarily:**
   Edit `core/.env`:
   ```env
   APP_DEBUG=true
   ```
   Try logging in and check for errors.
   **Set back to false after!**

3. **Contact support:**
   - GitHub Issues: https://github.com/MayaOfLagos/TRB-Banking-Platform/issues
   - Provide: Database version, PHP version, error messages

---

**Quick Fix:** Use phpMyAdmin → admins table → Edit row with id=1 → Update username, email, and password hash

**Last Updated:** October 4, 2025  
**Fixed in:** Installer v2.1+
