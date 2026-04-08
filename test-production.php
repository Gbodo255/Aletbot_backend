#!/usr/bin/env php
<?php
/**
 * Remote API Tester
 * Tests the production API on Render
 */

$baseUrl = 'https://alertbot-api.onrender.com/api/v1';
$debug = true;

function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    global $debug;
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
        'Content-Type: application/json',
        'Accept: application/json'
    ], $headers));
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    curl_close($ch);
    
    if ($debug) {
        echo "DEBUG: URL: {$url}\n";
        echo "DEBUG: Method: {$method}\n";
        echo "DEBUG: HTTP Code: {$httpCode}\n";
        if ($curlError) echo "DEBUG: cURL Error: {$curlError}\n";
        echo "\n";
    }
    
    return [
        'status' => $httpCode,
        'body' => json_decode($response, true),
        'raw' => $response,
        'error' => $curlError
    ];
}

echo "=== Production API Tests ===\n\n";

// Test 1: Health Check
echo "1. Testing /health endpoint...\n";
$healthResponse = makeRequest("$baseUrl/health", 'GET');
echo "Status: {$healthResponse['status']}\n";
if ($healthResponse['body']) {
    echo "Response: " . json_encode($healthResponse['body'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Raw Response: " . $healthResponse['raw'] . "\n";
}
echo "\n";

// Test 2: Register
echo "2. Testing /auth/register...\n";
$registerData = [
    'name' => 'Test User ' . time(),
    'email' => 'test' . time() . '@example.com',
    'password' => 'testpass123',
    'password_confirmation' => 'testpass123'
];
$registerResponse = makeRequest("$baseUrl/auth/register", 'POST', $registerData);
echo "Status: {$registerResponse['status']}\n";
if ($registerResponse['body']) {
    echo "Response: " . json_encode($registerResponse['body'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Raw Response: " . substr($registerResponse['raw'], 0, 500) . "...\n";
}
echo "\n";

echo "=== Tests Complete ===\n";