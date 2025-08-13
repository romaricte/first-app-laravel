<?php

/**
 * Script de test pour l'API Laravel avec Swagger
 * 
 * Ce script démontre comment utiliser l'API documentée avec Swagger
 */

$baseUrl = 'http://localhost:8000/api';

// Configuration curl de base
function makeRequest($method, $endpoint, $data = null, $token = null) {
    global $baseUrl;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        $token ? 'Authorization: Bearer ' . $token : ''
    ]);
    
    switch($method) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'PUT':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

echo "🚀 Test de l'API Laravel avec Swagger\n";
echo "=====================================\n\n";

// Test 1: Inscription
echo "1️⃣ Test d'inscription...\n";
$registerData = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

$response = makeRequest('POST', '/auth/register', $registerData);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 201) {
    echo "✅ Inscription réussie!\n";
    $token = $response['data']['data']['token'];
    echo "Token: " . substr($token, 0, 20) . "...\n";
} else {
    echo "❌ Erreur d'inscription: " . json_encode($response['data']) . "\n";
    exit;
}
echo "\n";

// Test 2: Connexion
echo "2️⃣ Test de connexion...\n";
$loginData = [
    'email' => 'test@example.com',
    'password' => 'password123'
];

$response = makeRequest('POST', '/auth/login', $loginData);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 200) {
    echo "✅ Connexion réussie!\n";
    $token = $response['data']['data']['token'];
} else {
    echo "❌ Erreur de connexion: " . json_encode($response['data']) . "\n";
}
echo "\n";

// Test 3: Récupération du profil
echo "3️⃣ Test de récupération du profil...\n";
$response = makeRequest('GET', '/profile', null, $token);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 200) {
    echo "✅ Profil récupéré!\n";
    echo "Nom: " . $response['data']['data']['user']['name'] . "\n";
    echo "Email: " . $response['data']['data']['user']['email'] . "\n";
} else {
    echo "❌ Erreur de récupération du profil: " . json_encode($response['data']) . "\n";
}
echo "\n";

// Test 4: Liste des utilisateurs
echo "4️⃣ Test de liste des utilisateurs...\n";
$response = makeRequest('GET', '/users', null, $token);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 200) {
    echo "✅ Liste récupérée!\n";
    $users = $response['data']['data']['users']['data'];
    echo "Nombre d'utilisateurs: " . count($users) . "\n";
} else {
    echo "❌ Erreur de récupération de la liste: " . json_encode($response['data']) . "\n";
}
echo "\n";

// Test 5: Déconnexion
echo "5️⃣ Test de déconnexion...\n";
$response = makeRequest('POST', '/auth/logout', null, $token);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 200) {
    echo "✅ Déconnexion réussie!\n";
} else {
    echo "❌ Erreur de déconnexion: " . json_encode($response['data']) . "\n";
}
echo "\n";

echo "🎉 Tests terminés!\n";
echo "\n📖 Pour voir la documentation complète, visitez:\n";
echo "   http://localhost:8000/api/documentation\n";
