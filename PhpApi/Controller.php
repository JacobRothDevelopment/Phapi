<?php

namespace PhpApi;

require_once(__DIR__ . "/ApiException.php");

// To be extended by all Controller classes
abstract class Controller
{
    protected function HttpGet(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== "GET") {
            throw new ApiException(405, "Endpoint does not support " . $_SERVER['REQUEST_METHOD']);
        }
    }

    protected function HttpPost(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== "POST") {
            throw new ApiException(405, "Endpoint does not support " . $_SERVER['REQUEST_METHOD']);
        }
    }

    protected function HttpPut(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== "PUT") {
            throw new ApiException(405, "Endpoint does not support " . $_SERVER['REQUEST_METHOD']);
        }
    }

    protected function HttpPatch(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== "PATCH") {
            throw new ApiException(405, "Endpoint does not support " . $_SERVER['REQUEST_METHOD']);
        }
    }

    protected function HttpDelete(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== "DELETE") {
            throw new ApiException(405, "Endpoint does not support " . $_SERVER['REQUEST_METHOD']);
        }
    }
}
