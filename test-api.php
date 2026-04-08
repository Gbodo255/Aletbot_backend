<?php

/**
 * Manual API Testing Script
 * Test all endpoints according to requirements
 */

$baseUrl = 'http://127.0.0.1:8001/api/v1';

// Helper function to make API requests
function makeRequest($method, $endpoint, $data = [], $token = null) {
    $url = 'http://127.0.0.1:8001/api/v1' . $endpoint;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        $token ? 'Authorization: Bearer ' . $token : '',
    ]);
    
    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

echo "=== API User Management System Tests ===\n\n";

// Test 1: Register a new user
echo "1. Testing User Registration...\n";
$registerResponse = makeRequest('POST', '/auth/register', [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
]);

echo "Status: " . $registerResponse['status'] . "\n";
echo "Response: " . json_encode($registerResponse['body'], JSON_PRETTY_PRINT) . "\n\n";

$token = null;
if ($registerResponse['status'] === 201) {
    $token = $registerResponse['body']['token'];
    echo "✓ Registration successful! Token: {$token}\n\n";
} else {
    echo "✗ Registration failed!\n\n";
}

// Test 2: Login with the user
echo "2. Testing User Login...\n";
$loginResponse = makeRequest('POST', '/auth/login', [
    'email' => 'john@example.com',
    'password' => 'password123',
]);

echo "Status: " . $loginResponse['status'] . "\n";
echo "Response: " . json_encode($loginResponse['body'], JSON_PRETTY_PRINT) . "\n\n";

if ($loginResponse['status'] === 200) {
    $token = $loginResponse['body']['token'];
    echo "✓ Login successful!\n\n";
}

// Test 3: Get current user profile
echo "3. Testing Get Current User Profile...\n";
$profileResponse = makeRequest('GET', '/auth/me', [], $token);
echo "Status: " . $profileResponse['status'] . "\n";
echo "Response: " . json_encode($profileResponse['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Update user profile
echo "4. Testing Update User Profile...\n";
$updateResponse = makeRequest('PUT', '/profile', [
    'phone' => '+33612345678',
    'bio' => 'I am a test user',
], $token);

echo "Status: " . $updateResponse['status'] . "\n";
echo "Response: " . json_encode($updateResponse['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 5: Update notification preferences
echo "5. Testing Update Notification Preferences...\n";
$notifResponse = makeRequest('PUT', '/profile/notification-preferences', [
    'email_notifications' => true,
    'push_notifications' => false,
    'activity_alerts' => true,
], $token);

echo "Status: " . $notifResponse['status'] . "\n";
echo "Response: " . json_encode($notifResponse['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 6: Change password
echo "6. Testing Change Password...\n";
$changePassResponse = makeRequest('PUT', '/profile/change-password', [
    'current_password' => 'password123',
    'password' => 'newpassword123',
    'password_confirmation' => 'newpassword123',
], $token);

echo "Status: " . $changePassResponse['status'] . "\n";
echo "Response: " . json_encode($changePassResponse['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 7: View activity logs
echo "7. Testing View Activity Logs...\n";
$activityResponse = makeRequest('GET', '/activity-logs', [], $token);
echo "Status: " . $activityResponse['status'] . "\n";
echo "Response: " . json_encode($activityResponse['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 8: Logout
echo "8. Testing User Logout...\n";
$logoutResponse = makeRequest('POST', '/auth/logout', [], $token);
echo "Status: " . $logoutResponse['status'] . "\n";
echo "Response: " . json_encode($logoutResponse['body'], JSON_PRETTY_PRINT) . "\n\n";

echo "=== All Tests Completed ===\n";
?>
