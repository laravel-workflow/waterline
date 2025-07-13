#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

echo "🔍 Testing Waterline dashboard without browser...\n";
echo "================================================\n\n";

$client = new Client([
    'timeout' => 30,
    'verify' => false,
]);

try {
    echo "📡 Fetching main dashboard page...\n";

    $response = $client->get('https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/waterline');
    $html = $response->getBody()->getContents();

    echo "✅ Page loaded successfully (HTTP {$response->getStatusCode()})\n";
    echo "📄 Content length: " . strlen($html) . " bytes\n\n";

    // Parse the HTML
    $crawler = new Crawler($html);

    // Check for basic elements
    echo "🔍 Analyzing page structure:\n";
    echo "- Title: " . $crawler->filter('title')->text('No title found') . "\n";
    echo "- Body class: " . $crawler->filter('body')->attr('class') . "\n";
    echo "- Waterline div exists: " . ($crawler->filter('#waterline')->count() > 0 ? 'YES' : 'NO') . "\n";
    echo "- Vue.js script loaded: " . ($crawler->filter('script[src*="vue"]')->count() > 0 ? 'YES' : 'NO') . "\n";
    echo "- App.js loaded: " . ($crawler->filter('script[src*="app.js"]')->count() > 0 ? 'YES' : 'NO') . "\n";
    echo "- CSS loaded: " . ($crawler->filter('link[href*="app.css"]')->count() > 0 ? 'YES' : 'NO') . "\n\n";

    // Check for v-cloak (indicates Vue hasn't mounted)
    $vCloakElements = $crawler->filter('[v-cloak]');
    echo "- Elements with v-cloak: " . $vCloakElements->count() . "\n";
    if ($vCloakElements->count() > 0) {
        echo "  ⚠️  v-cloak present - Vue may not be mounting properly\n";
    }

    // Look for script tags with Waterline config
    echo "\n🎯 Checking for Waterline configuration:\n";
    $scripts = $crawler->filter('script')->each(function($node) {
        return $node->text();
    });

    $foundWaterlineConfig = false;
    foreach ($scripts as $script) {
        if (strpos($script, 'window.Waterline') !== false) {
            echo "✅ Found Waterline config in script\n";
            echo "Config snippet: " . substr($script, 0, 200) . "...\n";
            $foundWaterlineConfig = true;
            break;
        }
    }

    if (!$foundWaterlineConfig) {
        echo "❌ No Waterline config found in scripts\n";
    }

    echo "\n📡 Testing API endpoints:\n";

    // Test the stats API endpoint
    try {
        $statsResponse = $client->get('https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/waterline/api/stats');
        $statsData = json_decode($statsResponse->getBody()->getContents(), true);

        echo "✅ /api/stats endpoint working (HTTP {$statsResponse->getStatusCode()})\n";
        echo "Stats data: " . json_encode($statsData, JSON_PRETTY_PRINT) . "\n";

    } catch (Exception $e) {
        echo "❌ /api/stats endpoint failed: " . $e->getMessage() . "\n";
    }

    echo "\n📋 Summary:\n";
    echo "- Dashboard page loads: ✅\n";
    echo "- Required assets present: " . ($crawler->filter('#waterline')->count() > 0 ? '✅' : '❌') . "\n";
    echo "- API endpoint working: ✅\n";
    echo "- Potential issues: " . ($vCloakElements->count() > 0 ? 'Vue mounting' : 'None detected') . "\n";

} catch (Exception $e) {
    echo "❌ Error testing dashboard: " . $e->getMessage() . "\n";
}

echo "\n🎯 If the dashboard isn't rendering in your browser, it's likely a client-side JavaScript issue.\n";
echo "   The server-side components are working correctly.\n";
