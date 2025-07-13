const { chromium } = require('playwright');

async function captureBrowserErrors() {
    console.log('🔧 Attempting to launch browser with Playwright...');

    let browser;
    try {
        // Launch browser with Playwright (handles browser downloads automatically)
        browser = await chromium.launch({
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu',
                '--disable-web-security',
                '--disable-extensions',
                '--disable-plugins'
            ]
        });
        console.log('✅ Successfully launched browser with Playwright');
    } catch (error) {
        console.error('❌ Failed to launch browser:', error.message);
        console.log('💡 Try running: npx playwright install chromium');
        return;
    }

    try {
        const page = await browser.newPage();

        // Capture console messages
        const consoleMessages = [];
        page.on('console', msg => {
            consoleMessages.push({
                type: msg.type(),
                text: msg.text(),
                location: msg.location()
            });
        });

        // Capture page errors
        const pageErrors = [];
        page.on('pageerror', error => {
            pageErrors.push({
                message: error.message,
                stack: error.stack
            });
        });

        // Capture failed requests
        const failedRequests = [];
        page.on('requestfailed', request => {
            failedRequests.push({
                url: request.url(),
                method: request.method(),
                failure: request.failure()
            });
        });

        // Capture response errors
        const responseErrors = [];
        page.on('response', response => {
            if (!response.ok()) {
                responseErrors.push({
                    url: response.url(),
                    status: response.status(),
                    statusText: response.statusText()
                });
            }
        });

        console.log('🔍 Navigating to Waterline dashboard...');

        // Navigate to the page
        await page.goto('https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/waterline', {
            waitUntil: 'networkidle',
            timeout: 30000
        });

        // Wait a bit for any async operations
        await page.waitForTimeout(3000);

        // Try to evaluate if Vue is mounted
        const vueStatus = await page.evaluate(() => {
            const app = document.getElementById('waterline');
            return {
                elementExists: !!app,
                hasVueInstance: !!(app && app.__vue__),
                windowVue: typeof Vue !== 'undefined',
                windowWaterline: typeof window.Waterline !== 'undefined',
                waterlineConfig: window.Waterline || null,
                bodyHTML: document.body.innerHTML.length,
                vCloak: app ? app.hasAttribute('v-cloak') : null,
                innerHTML: app ? app.innerHTML.substring(0, 500) : null
            };
        });

        // Get page title and basic info
        const pageInfo = await page.evaluate(() => ({
            title: document.title,
            url: window.location.href,
            readyState: document.readyState,
            hasErrors: !!window.onerror
        }));

        // Take a screenshot for visual debugging
        await page.screenshot({
            path: '/var/www/html/debug-screenshot-playwright.png',
            fullPage: true
        });

        console.log('\n📊 DEBUGGING RESULTS (Playwright):');
        console.log('==================================');

        console.log('\n📄 Page Info:');
        console.log(JSON.stringify(pageInfo, null, 2));

        console.log('\n🎯 Vue/App Status:');
        console.log(JSON.stringify(vueStatus, null, 2));

        if (consoleMessages.length > 0) {
            console.log('\n📝 Console Messages:');
            consoleMessages.forEach((msg, index) => {
                console.log(`${index + 1}. [${msg.type.toUpperCase()}] ${msg.text}`);
                if (msg.location && msg.location.url) {
                    console.log(`   Location: ${msg.location.url}:${msg.location.lineNumber}`);
                }
            });
        } else {
            console.log('\n✅ No console messages captured');
        }

        if (pageErrors.length > 0) {
            console.log('\n❌ Page Errors:');
            pageErrors.forEach((error, index) => {
                console.log(`${index + 1}. ${error.message}`);
                if (error.stack) {
                    console.log(`   Stack: ${error.stack}`);
                }
            });
        } else {
            console.log('\n✅ No page errors detected');
        }

        if (failedRequests.length > 0) {
            console.log('\n🚫 Failed Requests:');
            failedRequests.forEach((req, index) => {
                console.log(`${index + 1}. ${req.method} ${req.url}`);
                console.log(`   Error: ${req.failure ? req.failure.errorText : 'Unknown error'}`);
            });
        } else {
            console.log('\n✅ No failed requests');
        }

        if (responseErrors.length > 0) {
            console.log('\n🔴 HTTP Error Responses:');
            responseErrors.forEach((resp, index) => {
                console.log(`${index + 1}. ${resp.status} ${resp.statusText} - ${resp.url}`);
            });
        } else {
            console.log('\n✅ No HTTP errors');
        }

        console.log('\n📸 Screenshot saved to: /var/www/html/debug-screenshot-playwright.png');

    } catch (error) {
        console.error('❌ Error during debugging:', error);
    } finally {
        await browser.close();
    }
}

// Run the debugging
captureBrowserErrors().catch(console.error);
