#!/bin/bash

echo "🔍 Testing Waterline Dashboard Connectivity and Basic Functionality"
echo "================================================================="

# Test 1: Basic HTTP connectivity
echo -e "\n1️⃣ Testing basic HTTP connectivity..."
curl -s -o /dev/null -w "HTTP Status: %{http_code}\nRedirect URL: %{redirect_url}\nTotal Time: %{time_total}s\n" \
  https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/waterline

echo -e "\n2️⃣ Fetching HTML content (first 1000 chars)..."
curl -s https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/waterline | head -c 1000
echo -e "\n... (truncated)"

echo -e "\n3️⃣ Testing API endpoints..."
echo "Stats API:"
curl -s https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/waterline/api/stats | head -c 200

echo -e "\n\n4️⃣ Testing asset loading..."
echo "CSS file:"
curl -s -o /dev/null -w "CSS Status: %{http_code} Size: %{size_download} bytes\n" \
  https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/vendor/waterline/app-dark.css

echo "JS file:"
curl -s -o /dev/null -w "JS Status: %{http_code} Size: %{size_download} bytes\n" \
  https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/vendor/waterline/app.js

echo "Mix manifest:"
curl -s -o /dev/null -w "Manifest Status: %{http_code}\n" \
  https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/vendor/waterline/mix-manifest.json

echo -e "\n5️⃣ Checking for Vue.js related content..."
if curl -s https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/waterline | grep -q "v-cloak"; then
    echo "✅ Vue directive (v-cloak) found in HTML"
else
    echo "❌ Vue directive (v-cloak) NOT found"
fi

if curl -s https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/waterline | grep -q "window.Waterline"; then
    echo "✅ Waterline config object found"
else
    echo "❌ Waterline config object NOT found"
fi

if curl -s https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/waterline | grep -q "router-view"; then
    echo "✅ Vue router-view found"
else
    echo "❌ Vue router-view NOT found"
fi

echo -e "\n6️⃣ Extracting Waterline config from page..."
curl -s https://obscure-eureka-5grq4j94px245q5-8000.app.github.dev/waterline | \
  grep -o "window.Waterline = .*" | head -1
