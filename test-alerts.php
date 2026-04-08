<?php
/**
 * Test script for Alerts API
 * Run with: php test-alerts.php
 */

echo "=== TESTS API ALERTES ===\n\n";

// Configuration
$baseUrl = 'http://127.0.0.1:8001/api/v1';
$token = ''; // Will be set after login

// Test data
$testUser = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

$testAlert = [
    'name' => 'Alerte de test',
    'content' => 'Ceci est une alerte de test automatique',
    'urgency_level' => 'medium',
    'channels' => ['telegram']
];

// Helper function to make HTTP requests
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $headers[] = 'Content-Type: application/json';
    }

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return [$httpCode, json_decode($response, true)];
}

// Test 1: Register user
echo "1. Création d'utilisateur de test...\n";
list($code, $response) = makeRequest("$baseUrl/auth/register", 'POST', $testUser);

if ($code === 201) {
    echo "✅ Utilisateur créé avec succès\n";
    $token = $response['data']['token'] ?? '';
} else {
    echo "❌ Erreur création utilisateur: " . ($response['message'] ?? 'Unknown error') . "\n";
    exit(1);
}

// Test 2: Login
echo "\n2. Connexion...\n";
list($code, $response) = makeRequest("$baseUrl/auth/login", 'POST', [
    'email' => $testUser['email'],
    'password' => $testUser['password']
]);

if ($code === 200) {
    echo "✅ Connexion réussie\n";
    $token = $response['data']['token'];
} else {
    echo "❌ Erreur connexion: " . ($response['message'] ?? 'Unknown error') . "\n";
    exit(1);
}

// Test 3: Create alert
echo "\n3. Création d'alerte...\n";
$headers = ["Authorization: Bearer $token"];
list($code, $response) = makeRequest("$baseUrl/alerts", 'POST', $testAlert, $headers);

if ($code === 201) {
    echo "✅ Alerte créée avec succès\n";
    $alertId = $response['data']['id'];
} else {
    echo "❌ Erreur création alerte: " . ($response['message'] ?? 'Unknown error') . "\n";
    print_r($response);
    exit(1);
}

// Test 4: Send alert
echo "\n4. Envoi d'alerte...\n";
list($code, $response) = makeRequest("$baseUrl/alerts/$alertId/send", 'POST', null, $headers);

if ($code === 200 && $response['status'] === 'success') {
    echo "✅ Alerte envoyée avec succès (simulation)\n";
    echo "   Message: " . $response['message'] . "\n";
} else {
    echo "❌ Erreur envoi alerte: " . ($response['message'] ?? 'Unknown error') . "\n";
    print_r($response);
}

// Test 5: Get alerts list
echo "\n5. Récupération de la liste des alertes...\n";
list($code, $response) = makeRequest("$baseUrl/alerts", 'GET', null, $headers);

if ($code === 200) {
    echo "✅ Liste des alertes récupérée\n";
    echo "   Nombre d'alertes: " . count($response['data']) . "\n";
} else {
    echo "❌ Erreur récupération liste: " . ($response['message'] ?? 'Unknown error') . "\n";
}

// Test 6: Get alerts history
echo "\n6. Récupération de l'historique...\n";
list($code, $response) = makeRequest("$baseUrl/alerts/history", 'GET', null, $headers);

if ($code === 200) {
    echo "✅ Historique récupéré\n";
    echo "   Nombre d'alertes dans l'historique: " . count($response['data']) . "\n";
} else {
    echo "❌ Erreur récupération historique: " . ($response['message'] ?? 'Unknown error') . "\n";
}

echo "\n=== TESTS TERMINÉS ===\n";
echo "\nVérifiez les logs Laravel pour voir les entrées d'alertes:\n";
echo "tail -f storage/logs/laravel.log\n";
?>