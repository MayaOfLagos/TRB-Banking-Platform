# GitHub Actions CI/CD Setup Guide

## 📋 Overview

This repository has **3 automated workflows** configured:

1. **Laravel CI** - Runs tests and validates code on every push
2. **Code Quality** - Checks code quality and security
3. **Deploy to Production** - Automatically deploys to your server (optional)

---

## 🚀 Workflows Explained

### 1️⃣ Laravel CI (`laravel-ci.yml`)

**Triggers:** Every push to `master` or `develop` branch

**What it does:**
- ✅ Sets up PHP 8.3 environment
- ✅ Installs all Composer dependencies
- ✅ Sets up MySQL database for testing
- ✅ Runs database migrations
- ✅ Executes Laravel tests
- ✅ Checks PHP syntax errors
- ✅ Validates directory permissions

**When to use:**
- Automatically runs when you push code
- Ensures code doesn't break the application
- Catches errors before deployment

---

### 2️⃣ Code Quality (`code-quality.yml`)

**Triggers:** Every push to `master` or `develop` branch

**What it does:**
- ✅ Checks PHP syntax across all files
- ✅ Scans for security vulnerabilities
- ✅ Validates file permissions
- ✅ Ensures `.env` file is not committed
- ✅ Runs Composer security audit

**Benefits:**
- Maintains code quality standards
- Prevents security vulnerabilities
- Enforces best practices

---

### 3️⃣ Deploy to Production (`deploy-production.yml`)

**Triggers:** 
- Automatic: When pushing to `master` branch
- Manual: Via GitHub Actions UI

**What it does:**
- ✅ Connects to your server via SSH
- ✅ Pulls latest code from GitHub
- ✅ Installs/updates dependencies
- ✅ Runs database migrations
- ✅ Clears and optimizes caches
- ✅ Sets proper file permissions
- ✅ Restarts queue workers

**⚠️ Setup Required:**

You need to add these **secrets** in your GitHub repository settings:

**How to add secrets:**
1. Go to your repository on GitHub
2. Click **Settings** → **Secrets and variables** → **Actions**
3. Click **New repository secret**
4. Add the following secrets:

| Secret Name | Description | Example |
|------------|-------------|---------|
| `SERVER_HOST` | Your server IP or domain | `123.45.67.89` or `yourserver.com` |
| `SERVER_USERNAME` | SSH username | `root` or `ubuntu` |
| `SSH_PRIVATE_KEY` | Your SSH private key | Contents of `~/.ssh/id_rsa` |
| `SERVER_PORT` | SSH port (usually 22) | `22` |
| `DEPLOY_PATH` | Path to application on server | `/var/www/viserbank` |

**How to get SSH private key:**
```bash
# On your local machine or server
cat ~/.ssh/id_rsa

# Copy the entire output (including BEGIN and END lines)
```

---

## 📊 Viewing Workflow Results

### **On GitHub:**
1. Go to your repository: https://github.com/MayaOfLagos/TRB-Banking-Platform
2. Click the **Actions** tab
3. You'll see all workflow runs with their status:
   - ✅ Green checkmark = Success
   - ❌ Red X = Failed
   - 🟡 Yellow dot = In progress

### **Build Status Badges:**

Add these to your README.md to show build status:

```markdown
![Laravel CI](https://github.com/MayaOfLagos/TRB-Banking-Platform/workflows/Laravel%20CI/badge.svg)
![Code Quality](https://github.com/MayaOfLagos/TRB-Banking-Platform/workflows/Code%20Quality/badge.svg)
```

---

## 🔧 Customizing Workflows

### **Change PHP version:**
Edit `.github/workflows/laravel-ci.yml`:
```yaml
php-version: [8.3]  # Change to your preferred version
```

### **Change MySQL version:**
Edit `.github/workflows/laravel-ci.yml`:
```yaml
image: mysql:8.0  # Change to mysql:5.7 if needed
```

### **Add more tests:**
Edit your `core/tests/` directory and the workflows will automatically run them.

### **Disable a workflow:**
Rename the file extension:
```bash
.github/workflows/deploy-production.yml.disabled
```

---

## 🎯 Workflow Triggers

### **Automatic triggers:**
- `push` - Runs when you push code
- `pull_request` - Runs when you create a PR

### **Manual triggers:**
Some workflows have `workflow_dispatch` which allows manual triggering:
1. Go to **Actions** tab
2. Select the workflow
3. Click **Run workflow** button

---

## 📝 Common Commands

### **Force run a workflow:**
```bash
git commit --allow-empty -m "Trigger workflow"
git push
```

### **Check workflow locally (before pushing):**
```bash
cd core
composer install
php artisan test
php artisan migrate --env=testing
```

---

## 🐛 Troubleshooting

### **Workflow fails with "Composer dependencies" error:**
- Check if `composer.lock` is committed
- Ensure PHP version matches in workflow

### **Database migration fails:**
- Check if database credentials are correct
- Verify migrations are in `core/database/migrations/`

### **Deployment fails:**
- Verify SSH secrets are added correctly
- Check if SSH key has proper permissions
- Ensure server path is correct

### **Tests fail:**
- Check if tests exist in `core/tests/`
- Run tests locally first: `php artisan test`

---

## 🔒 Security Best Practices

1. ✅ **Never commit `.env` file** (Code Quality workflow checks this)
2. ✅ **Use GitHub Secrets** for sensitive data
3. ✅ **Rotate SSH keys regularly**
4. ✅ **Use deploy keys** instead of personal SSH keys
5. ✅ **Review workflow runs** for suspicious activity

---

## 📚 Resources

- **GitHub Actions Docs:** https://docs.github.com/en/actions
- **Laravel Testing:** https://laravel.com/docs/testing
- **Laravel Deployment:** https://laravel.com/docs/deployment

---

## 🎉 Benefits of CI/CD

✅ **Catch bugs early** - Before they reach production
✅ **Faster development** - Automated testing saves time
✅ **Consistent deployments** - Same process every time
✅ **Team collaboration** - Everyone sees build status
✅ **Quality assurance** - Code standards enforced
✅ **Peace of mind** - Know your code works before deployment

---

## 🚦 Getting Started

1. **Check Actions Tab:** https://github.com/MayaOfLagos/TRB-Banking-Platform/actions
2. **Make a small change and push** to see workflows in action
3. **Add deployment secrets** if you want auto-deployment
4. **Watch the magic happen!** ✨

Your CI/CD pipeline is now ready to use! 🎊
