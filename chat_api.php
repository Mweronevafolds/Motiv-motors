<?php
header('Content-Type: application/json');

// 1. CONFIGURATION
// ---------------------------------------------------------
// PASTE YOUR KEY INSIDE THE QUOTES BELOW
$apiKey = 'AIzaSyA9RrBNxhWrdb-TyVOJvB-mcX7K3TS2ZxM'; 
// ---------------------------------------------------------

// 2. RECEIVE INPUT
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if (!$userMessage) {
    echo json_encode(['reply' => "Please ask me something about cars!"]);
    exit;
}

// 3. SYSTEM PROMPT (The AI Personality)
$systemPrompt = "You are 'Motiv AI', a sophisticated car sales consultant for 'Motiv Motors' in Nairobi, Kenya.
CONTEXT:
- Traffic: Nairobi traffic is heavy. Suggest automatics.
- Terrain: Potholes/Speed bumps are common. Suggest high ground clearance (Prado, X5, Land Cruiser).
- Fuel: Expensive (~200 KSh/L). Mention efficiency for smaller cars.
- Inventory: Land Cruiser V8, Range Rover Sport, Mercedes S500, Porsche Cayenne, Subaru WRX STI, Toyota Prado, BMW X5, Ford Mustang.
- Tone: Professional, concise (max 2 sentences), persuasive.
- Currency: KSh.

User: $userMessage";

// 4. PREPARE DATA FOR GOOGLE
$modelName = 'gemini-2.0-flash';
$modelEndpoints = [
    'v1beta' => "https://generativelanguage.googleapis.com/v1beta/models/$modelName:generateContent",
    'v1' => "https://generativelanguage.googleapis.com/v1/models/$modelName:generateContent",
];

$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $systemPrompt]
            ]
        ]
    ]
];

$jsonData = json_encode($data);

// 5. EXECUTE REQUEST (USING CURL FOR STABILITY)
$response = null;
$httpCode = null;

foreach ($modelEndpoints as $label => $endpoint) {
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-goog-api-key: ' . $apiKey
    ]);

    // --- CRITICAL FIX FOR LOCALHOST ---
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['reply' => 'Connection Error: ' . curl_error($ch)]);
        curl_close($ch);
        exit;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 404 && $label === 'v1beta') {
        // Try stable v1 endpoint if alias is unavailable on v1beta
        continue;
    }

    break;
}

// 6. PROCESS RESPONSE
$result = json_decode($response, true);

if ($httpCode !== 200) {
    // If Google returns an API error (like Invalid Key)
    $errorMessage = $result['error']['message'] ?? 'Unknown API Error';
    echo json_encode(['reply' => "API Error ($httpCode): $errorMessage"]);
    exit;
}

// Extract the actual text
if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    $reply = $result['candidates'][0]['content']['parts'][0]['text'];
} else {
    $reply = "I am online, but I didn't understand that. Could you rephrase?";
}

echo json_encode(['reply' => $reply]);
?>