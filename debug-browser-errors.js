const puppeteer = require('puppeteer');

async function captureBrowserErrors() {
    console.log('🔧 Attempting to launch browser...');

    // Try multiple browser configurations
    let browser;
    const launchConfigs = [
        // Try with bundled Chromium (default)
        {
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu',
                '--disable-features=VizDisplayCompositor',
                '--disable-web-security',
                '--disable-extensions',
                '--disable-plugins'
            ]
        },
        // Try with downloaded Chrome package
        {
            headless: true,
            executablePath: '/tmp/opt/google/chrome/chrome',
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu'
            ]
        },
        // Try with system Chromium (should work now)
        {
            headless: true,
            executablePath: '/usr/bin/chromium-browser',
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu'
            ]
        },
        // Try with Playwright's Chromium
        {
            headless: true,
            executablePath: '/home/sail/.cache/ms-playwright/chromium-1181/chrome-linux/chrome',
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu'
            ]
        }
    ];

    for (let i = 0; i < launchConfigs.length; i++) {
        try {
            console.log(`🚀 Trying browser configuration ${i + 1}...`);
            browser = await puppeteer.launch(launchConfigs[i]);
            console.log(`✅ Successfully launched browser with configuration ${i + 1}`);
            break;
        } catch (error) {
            console.log(`❌ Configuration ${i + 1} failed: ${error.message}`);
            if (i === launchConfigs.length - 1) {
                throw new Error('All browser configurations failed. Please install Chrome or Chromium.');
            }
        }
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
                errorText: request.failure()?.errorText,
                resourceType: request.resourceType()
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
            waitUntil: 'networkidle2',
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
                vCloak: app ? app.hasAttribute('v-cloak') : null
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
            path: '/var/www/html/debug-screenshot.png',
            fullPage: true
        });

        console.log('\n📊 DEBUGGING RESULTS:');
        console.log('===================');

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
                console.log(`   Error: ${req.errorText} (${req.resourceType})`);
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

        console.log('\n📸 Screenshot saved to: /var/www/html/debug-screenshot.png');

    } catch (error) {
        console.error('❌ Error during debugging:', error);
    } finally {
        await browser.close();
    }
}

// Run the debugging
captureBrowserErrors().catch(console.error);
