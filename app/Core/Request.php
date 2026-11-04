<?php
declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private ?array $jsonBody = null;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;

        $contentType = $this->server["CONTENT_TYPE"] ?? "";
        if (str_contains($contentType, "application/json")) {
            $rawInput = file_get_contents("php://input");
            if (!empty($rawInput)) {
                $decoded = json_decode($rawInput, true);
                if (is_array($decoded)) {
                    $this->jsonBody = $decoded;
                }
            }
        }
    }

    public function getMethod(): string
    {
        return strtoupper($this->server["REQUEST_METHOD"] ?? "GET");
    }

    public function getUri(): string
    {
        $uri = $this->server["REQUEST_URI"] ?? "/";
        $position = strpos($uri, "?");
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }

        // Remover prefijos comunes como /pre o /pre/public
        $scriptName = $this->server["SCRIPT_NAME"] ?? "";
        $baseDir = dirname($scriptName);
        $baseDir = str_replace("\\", "/", $baseDir);

        if ($baseDir !== "/" && $baseDir !== "" && str_starts_with($uri, $baseDir)) {
            $uri = substr($uri, strlen($baseDir));
        }

        $uri = "/" . trim($uri, "/");
        return $uri === "" ? "/" : $uri;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        if ($this->jsonBody !== null && array_key_exists($key, $this->jsonBody)) {
            return $this->jsonBody[$key];
        }

        if (array_key_exists($key, $this->post)) {
            return $this->post[$key];
        }

        if (array_key_exists($key, $this->get)) {
            return $this->get[$key];
        }

        return $default;
    }

    public function all(): array
    {
        if ($this->jsonBody !== null) {
            return array_merge($this->get, $this->jsonBody);
        }
        return array_merge($this->get, $this->post);
    }

    public function isPost(): bool
    {
        return $this->getMethod() === "POST";
    }

    public function isGet(): bool
    {
        return $this->getMethod() === "GET";
    }

    public function isAjax(): bool
    {
        return !empty($this->server["HTTP_X_REQUESTED_WITH"]) &&
            strtolower($this->server["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest";
    }

    public function validateCSRF(): bool
    {
        $token = $this->input("csrf_token") ?? $this->server["HTTP_X_CSRF_TOKEN"] ?? null;
        return CSRF::validate($token);
    }
}

