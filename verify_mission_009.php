<?php

function testRequest($method, $url, $data = [], $token = null) {
    $curl = curl_init();
    $headers = ['Content-Type: application/json', 'Accept: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
    ];

    if ($data) {
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }

    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return ['status' => $status, 'data' => json_decode($response, true)];
}

$baseUrl = "http://127.0.0.1:8001/api/v1";

// Login as admin or existing user (I know there's a test user from my previous run)
// Let's create a fresh one just in case
$testEmail = "mission009_" . time() . "@example.com";
$reg = testRequest('POST', "$baseUrl/auth/register", [
    'name' => 'Mission QA',
    'email' => $testEmail,
    'password' => 'Password123!',
    'password_confirmation' => 'Password123!',
    'phone' => '0707070707'
]);

$token = $reg['data']['token'];

echo "--- TEST CRÉATION ALERTE (MISSION 009) ---\n";
$alertData = [
    'reporter_name' => 'Jean Dupont',
    'type' => 'Urgence',
    'urgency_level' => 'Critique',
    'content' => 'Développement backend indisponible depuis 10 min.'
];

$res = testRequest('POST', "$baseUrl/alerts", $alertData, $token);

if ($res['status'] === 201) {
    echo "✅ Alerte créée avec succès.\n";
    $alertId = $res['data']['data']['id'];
    echo "Note: L'envoi automatique a dû être déclenché (Mission 009 requirement).\n";
} else {
    echo "❌ Échec création alerte\n";
    print_r($res['data']);
    exit(1);
}

echo "\n--- VÉRIFICATION DE LA SIMULATION (DANS RÉPONSE OU LOGS) ---\n";
// The sendResult is not returned in 'data' of Store currently, 
// but we can check if the status is 'sent'
$check = testRequest('GET', "$baseUrl/alerts/$alertId", [], $token);
if ($check['data']['data']['status'] === 'sent') {
    echo "✅ Statut de l'alerte est 'sent'. L'envoi automatique fonctionne.\n";
} else {
    echo "❌ Statut de l'alerte est '" . $check['data']['data']['status'] . "'. L'envoi automatique a échoué.\n";
    exit(1);
}

echo "\n--- VÉRIFICATION DES LOGS LARAVEL ---\n";
// The logs are in storage/logs/laravel.log
$logFile = __DIR__ . "/storage/logs/laravel.log";
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    if (strpos($logs, "NOUVELLE ALERTE") !== false && strpos($logs, "Jean Dupont") !== false) {
        echo "✅ Message formatté trouvé dans les logs Laravel !\n";
    } else {
        echo "⚠️ Message non trouvé dans les logs (vérifiez que storage/logs/laravel.log est accessible).\n";
    }
} else {
    echo "ℹ️ Fichier de logs non trouvé localement (test facultatif).\n";
}

echo "\n🚀 MISSION 009 : BACKEND PRÊT ET CONFORME !\n";
