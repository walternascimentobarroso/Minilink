<?php

namespace MiniLink;

class MiniLink
{
    private string $file = 'urls.txt';

    public function handleRequest(): void
    {
        header('Content-Type: application/json');

        $method = $_SERVER['REQUEST_METHOD'];
        $uri = trim($_GET['route'] ?? '', '/');

        if ($method === 'POST' && $uri === 'shorten') {
            $this->handleShorten();
        } elseif ($method === 'GET' && preg_match('#^[a-z0-9]{6}$#i', $uri)) {
            $this->handleRedirect($uri);
        } else {
            $this->jsonResponse(['error' => 'Not found route'], 404);
        }
    }

    private function handleShorten(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['url']) || !filter_var($data['url'], FILTER_VALIDATE_URL)) {
            $this->jsonResponse(['error' => 'Invalid or absent URL'], 400);
        }

        $url = $data['url'];
        $code = substr(md5($url . time()), 0, 6);

        file_put_contents($this->file, "$code|$url\n", FILE_APPEND);
        $this->jsonResponse(['code' => $code]);
    }

    private function handleRedirect(string $code): void
    {
        if (!file_exists($this->file)) {
            $this->jsonResponse(['error' => 'Code not found'], 404);
        }

        $lines = file($this->file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            list($savedCode, $savedUrl) = explode('|', $line, 2);
            if ($savedCode === $code) {
                header("Location: $savedUrl", true, 302);
                exit;
            }
        }

        $this->jsonResponse(['error' => 'Code not found'], 404);
    }

    private function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
