<?php
$file = 'urls.txt';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$uri = trim($_GET['route'] ?? '', '/');

// Route POST /shorten
if ($method === 'POST' && $uri === 'shorten') {
  $data = json_decode(file_get_contents('php://input'), true);
  if (!isset($data['url']) || !filter_var($data['url'], FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or absent URL']);
    exit;
  }

  $url = $data['url'];
  $code = substr(md5($url . time()), 0, 6);
  file_put_contents($file, "$code|$url\n", FILE_APPEND);

  echo json_encode(['code' => $code]);
  exit;
}

// Route GET /{code}
if ($method === 'GET' && preg_match('#^[a-z0-9]{6}$#i', $uri)) {
  $code = $uri;
  if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      list($savedCode, $savedUrl) = explode('|', $line, 2);
      if ($savedCode === $code) {
        header("Location: $savedUrl", true, 302);
        exit;
      }
    }
  }
  http_response_code(404);
  echo json_encode(['error' => 'Code not found']);
  exit;
}

// Para outras rotas, 404 JSON
http_response_code(404);
echo json_encode(['error' => 'Not found route']);
