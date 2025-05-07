<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prompt = $_POST['prompt'] ?? '';
 
    if (empty($prompt)) {
        echo json_encode(['error' => 'Mesaj boş olamaz.']);
        exit;
    }
 
    $apiKey = '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';
 
    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.7
    ];
 
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
 
    $response = curl_exec($ch);
 
    if (curl_errno($ch)) {
        echo json_encode(['error' => 'Sunucu hatası: ' . curl_error($ch)]);
    } else {
        $decoded = json_decode($response, true);
        if (isset($decoded['choices'][0]['message']['content'])) {
            echo json_encode(['reply' => $decoded['choices'][0]['message']['content']]);
        } else {
            echo json_encode(['error' => 'Geçersiz yanıt.']);
        }
    }
 
    curl_close($ch);
}
 
?>