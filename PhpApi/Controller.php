<?php

namespace PhpApi;

// To be extended by all Controller classes
abstract class Controller
{
    protected function HttpGet(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== "GET") {
            throw new ApiException(HttpCode::MethodNotAllowed, "Endpoint does not support " . $_SERVER['REQUEST_METHOD']);
        }
    }

    protected function HttpPost(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== "POST") {
            throw new ApiException(HttpCode::MethodNotAllowed, "Endpoint does not support " . $_SERVER['REQUEST_METHOD']);
        }
    }

    protected function HttpPut(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== "PUT") {
            throw new ApiException(HttpCode::MethodNotAllowed, "Endpoint does not support " . $_SERVER['REQUEST_METHOD']);
        }
    }

    protected function HttpPatch(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== "PATCH") {
            throw new ApiException(HttpCode::MethodNotAllowed, "Endpoint does not support " . $_SERVER['REQUEST_METHOD']);
        }
    }

    protected function HttpDelete(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== "DELETE") {
            throw new ApiException(HttpCode::MethodNotAllowed, "Endpoint does not support " . $_SERVER['REQUEST_METHOD']);
        }
    }

    protected function SetResponseCode(int $Code): void
    {
        http_response_code($Code);
    }
}
