<?php

namespace PhpApi;

// To be extended by all Controller classes
abstract class Controller
{
    protected function HttpGet(): void
    {
        $this->CompareHttpMethod(HttpMethod::Get);
    }

    protected function HttpHead(): void
    {
        $this->CompareHttpMethod(HttpMethod::Head);
    }

    protected function HttpPost(): void
    {
        $this->CompareHttpMethod(HttpMethod::Post);
    }

    protected function HttpPut(): void
    {
        $this->CompareHttpMethod(HttpMethod::Put);
    }

    protected function HttpDelete(): void
    {
        $this->CompareHttpMethod(HttpMethod::Delete);
    }

    protected function HttpConnect(): void
    {
        $this->CompareHttpMethod(HttpMethod::Connect);
    }

    protected function HttpOptions(): void
    {
        $this->CompareHttpMethod(HttpMethod::Options);
    }

    protected function HttpTrace(): void
    {
        $this->CompareHttpMethod(HttpMethod::Trace);
    }

    protected function HttpPatch(): void
    {
        $this->CompareHttpMethod(HttpMethod::Patch);
    }

    private function CompareHttpMethod($requiredMethod): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== $requiredMethod) {
            throw new ApiException(HttpCode::MethodNotAllowed, "Endpoint does not support " . $_SERVER['REQUEST_METHOD']);
        }
    }

    protected function SetResponseCode(int $Code): void
    {
        http_response_code($Code);
    }
}
