<?php
$data = json_decode(file_get_contents('php://input'), true);
$prompt = $data['prompt'] ?? '';

$ch = curl_init('http://localhost:11434/api/generate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
  'model' => 'tinyllama',
  'prompt' => $prompt
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

//Ollama retorna múltiplos JSONs em streaming, um por linha
$lines = explode("\n", $response);
$text = '';

foreach ($lines as $line) {
    $json = json_decode($line, true);
    if (isset($json['response'])) {
        $text .= $json['response'];
    }
}

echo json_encode(['response' => $text ?: 'Erro']);
