# Waterline Development Agents Guide

This document provides guidance for AI agents working on the Waterline project, including setup procedures, debugging tools, and common troubleshooting steps.

## Project Overview

Waterline is an elegant UI for monitoring [Laravel Workflows](https://github.com/laravel-workflow/laravel-workflow). This is a Laravel package developed using Orchestra Testbench Workbench for package development.

## Environment Setup

### Prerequisites
- PHP 8.0.2+
- Node.js and npm
- Composer
- Chrome/Chromium browser (for debugging tools)

### Initial Setup

1. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Build Assets**
   ```bash
   npm run production
   ```

3. **Publish Assets to Testbench**
   ```bash
   ./vendor/bin/testbench waterline:publish
   ```

4. **Start Development Server**
   
   **Option A: Using Composer (Local development)**
   ```bash
   composer run serve
   ```
   Access at: `http://localhost:8000/waterline`
   
   **Option B: Using Docker (Codespaces/Container environment)**
   ```bash
   # Start the container
   docker-compose up -d
   
   # Connect to the container
   docker-compose exec laravel.test bash
   
   # Inside the container, run:
   composer install
   npm install
   npm run production
   ./vendor/bin/testbench waterline:publish
   composer serve -- --host=0.0.0.0 --port=80
   ```
   Access at: `http://localhost/waterline` or container's exposed port

5. **Access Dashboard**
   - **Local**: `http://localhost:8000/waterline`
   - **Docker/Codespaces**: `http://localhost/waterline` (or exposed port)

## Development Tools

### Browser Debugging Script

The project includes `debug-browser-errors.js` - a Puppeteer-based debugging tool that:

- Tests multiple browser configurations
- Captures console messages, page errors, and failed requests
- Verifies Vue.js application mounting
- Takes screenshots for visual debugging
- Validates Waterline configuration loading

**Usage:**
```bash
node debug-browser-errors.js
```

**Script Features:**
- ✅ Browser compatibility testing (bundled Chromium, system browsers)
- ✅ Vue.js application state validation
- ✅ Console error capture
- ✅ Network request monitoring
- ✅ Screenshot generation
- ✅ Waterline configuration verification

### Workbench Commands

Key commands for package development:

```bash
# Build workbench environment
composer run build

# Start development server (local)
composer run serve

# Start development server (Docker)
docker-compose up -d

# Publish assets to testbench
./vendor/bin/testbench waterline:publish

# Install waterline (for external apps)
./vendor/bin/testbench waterline:install
```

## Asset Management

### Build Process
- **Source:** `resources/js/app.js`, `resources/sass/app.scss`, `resources/sass/app-dark.scss`
- **Build Tool:** Laravel Mix (webpack)
- **Output:** `public/` directory
- **Config:** `webpack.mix.js`

### Asset Sync
Assets must be published to testbench after building:
```bash
npm run production
./vendor/bin/testbench waterline:publish
```

This copies built assets from `./public` to `@laravel/public/vendor/waterline`

## Common Issues & Solutions

### 1. Mix Manifest Not Found
**Error:** `Mix manifest not found at: .../vendor/waterline/mix-manifest.json`

**Solution:**
```bash
npm run production
./vendor/bin/testbench waterline:publish
```

### 2. Vue Application Not Mounting
**Symptoms:** 
- Page loads but dashboard is blank
- `elementExists: false` in debug output

**Debug Steps:**
1. Check browser console for JavaScript errors
2. Verify assets are published: `./vendor/bin/testbench waterline:publish`
3. Run debug script: `node debug-browser-errors.js`
4. Check Vue mounting in debug output

### 3. Server Connection Issues
**Error:** Connection refused or 404 errors

**Solution:**
1. Ensure server is running: `composer run serve`
2. Use correct URL: `http://localhost:8000/waterline`
3. Check server logs in terminal

### 4. Browser Automation Fails
**Error:** Puppeteer browser launch failures

**Solutions:**
- Install Chrome/Chromium: `sudo apt-get install chromium-browser`
- Use bundled Chromium (usually works by default)
- Check browser executable paths in debug script

## Development Workflow

### Making Changes

1. **Frontend Changes:**
   ```bash
   # Edit files in resources/js/ or resources/sass/
   npm run production
   ./vendor/bin/testbench waterline:publish
   # Refresh browser
   ```

2. **Backend Changes:**
   ```bash
   # Edit PHP files in app/
   # Server auto-reloads, just refresh browser
   ```

3. **Asset Changes:**
   ```bash
   # For development with file watching:
   npm run watch
   # In another terminal:
   ./vendor/bin/testbench waterline:publish  # Run when needed
   ```

### Testing

```bash
# Run all tests
composer test

# Run specific database tests
composer test-mysql
composer test-sqlite
composer test-mongo
composer test-pgsql
composer test-mssql

# Browser debugging
node debug-browser-errors.js
```

## Package Structure

```
waterline/
├── app/                    # Package source code
│   ├── Console/           # Artisan commands
│   ├── Http/              # Controllers, middleware
│   ├── Repositories/      # Data repositories
│   └── ...
├── config/                # Package configuration
├── resources/             # Frontend assets
│   ├── js/               # Vue.js components
│   ├── sass/             # Stylesheets
│   └── views/            # Blade templates
├── public/               # Built assets
├── routes/               # Package routes
├── tests/                # Test suite
├── workbench.yaml        # Workbench configuration
├── webpack.mix.js        # Build configuration
└── debug-browser-errors.js # Debugging tool
```

## Key Configuration Files

- **workbench.yaml**: Testbench configuration, asset sync, commands
- **webpack.mix.js**: Asset build configuration
- **composer.json**: Package dependencies and scripts
- **config/waterline.php**: Package configuration

## Debugging Checklist

When troubleshooting issues:

1. ✅ **Dependencies installed**: `composer install && npm install`
2. ✅ **Assets built**: `npm run production`
3. ✅ **Assets published**: `./vendor/bin/testbench waterline:publish`
4. ✅ **Server running**: `composer run serve`
5. ✅ **Correct URL**: `http://localhost:8000/waterline`
6. ✅ **Browser debug**: `node debug-browser-errors.js`

## Success Indicators

A working Waterline installation should show:

```json
{
  "title": "Waterline - Dashboard",
  "elementExists": true,
  "hasVueInstance": true,
  "windowWaterline": true,
  "waterlineConfig": {
    "path": "waterline",
    "basePath": "/waterline"
  }
}
```

## External Usage

For external Laravel applications:

```bash
composer require laravel-workflow/waterline
php artisan waterline:install
```

But for package development, always use the testbench workflow described above.

---

*This guide is maintained for AI agents and developers working on the Waterline package. Update as needed when workflows change.*
