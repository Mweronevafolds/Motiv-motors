<?php
header('Content-Type: text/plain');

// 1. PASTE YOUR API KEY HERE
$apiKey = 'AIzaSyA9RrBNxhWrdb-TyVOJvB-mcX7K3TS2ZxM'; 

// 2. CHECK v1beta MODELS
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=$apiKey";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Fix for local SSL issues
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    $data = json_decode($response, true);
    echo "=== SUCCESS: AVAILABLE MODELS ===\n";
    echo "Copy one of the 'Model Name' values below (without 'models/') into chat_api.php:\n\n";

    $count = 0;
    foreach ($data['models'] as $model) {
        if (in_array('generateContent', $model['supportedGenerationMethods'] ?? [])) {
            $fullName = $model['name'] ?? '';
            $shortName = $fullName ? str_replace('models/', '', $fullName) : 'N/A';
            echo "Model Name: $shortName\n";
            echo "Full Path: $fullName\n";
            echo "Description: " . ($model['description'] ?? 'No description provided') . "\n";
            echo "------------------------------------------------\n";
            $count++;
        }
    }

    if ($count === 0) {
        echo "No models with generateContent capability were returned.\n";
    }
} else {
    echo "=== ERROR CONNECTING TO GOOGLE ===\n";
    echo "HTTP Code: $httpCode\n";
    echo "Response: $response\n";
    echo "Check: Is your API Key correct? Do you have internet?\n";
}
