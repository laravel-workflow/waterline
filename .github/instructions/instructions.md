---
applyTo: '**'
---

# Waterline Development Quick Reference

## Prerequisites
- PHP 8.0.2+
- Node.js & npm
- Composer
- Chrome/Chromium (for browser debugging)

## Setup & Development
1. Install dependencies:
   ```bash
   composer install
   npm install
   ```
2. Build assets:
   ```bash
   npm run production
   ```
3. Publish assets to testbench:
   ```bash
   ./vendor/bin/testbench waterline:publish
   ```
4. Run migrations:
   ```bash
   ./vendor/bin/testbench workbench:create-sqlite-db
   ./vendor/bin/testbench migrate:fresh --database=sqlite
   ```
5. Start server:
   ```bash
   composer run serve
   ```
6. Access dashboard:
   - Local: http://localhost:8000/waterline
7. Create test workflow:
   ```bash
   ./vendor/bin/testbench workflow:create-test
   ```
8. Run queue worker:
   ```bash
   ./vendor/bin/testbench queue:work
   ```

## Asset Management
- Source: `resources/js/`, `resources/sass/`
- Build: `npm run production`
- Publish: `./vendor/bin/testbench waterline:publish`

## Key Commands
- Build workbench: `composer run build`
- Start server: `composer run serve`
- Publish assets: `./vendor/bin/testbench waterline:publish`
- Run tests: `composer test`
- Debug browser: `node .github/tools/browser.js`

## Troubleshooting
- Mix manifest not found: rebuild and publish assets
- Blank dashboard: check JS errors, publish assets, run debug script
- Connection issues: ensure server is running, check URL
- Puppeteer/browser errors: install Chrome/Chromium, check debug script
