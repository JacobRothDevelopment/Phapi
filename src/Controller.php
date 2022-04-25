<?php

namespace Phapi;

/** To be extended by all Controllers */
abstract class Controller
{
    /** Forces endpoint as GET request */
    protected function HttpGet(): void
    {
        $this->CompareHttpMethod(HttpMethod::Get);
    }

    /** Forces endpoint as HEAD request */
    protected function HttpHead(): void
    {
        $this->CompareHttpMethod(HttpMethod::Head);
    }

    /** Forces endpoint as POST request */
    protected function HttpPost(): void
    {
        $this->CompareHttpMethod(HttpMethod::Post);
    }

    /** Forces endpoint as PUT request */
    protected function HttpPut(): void
    {
        $this->CompareHttpMethod(HttpMethod::Put);
    }

    /** Forces endpoint as DELETE request */
    protected function HttpDelete(): void
    {
        $this->CompareHttpMethod(HttpMethod::Delete);
    }

    /** Forces endpoint as CONNECT request */
    protected function HttpConnect(): void
    {
        $this->CompareHttpMethod(HttpMethod::Connect);
    }

    /** Forces endpoint as OPTIONS request */
    protected function HttpOptions(): void
    {
        $this->CompareHttpMethod(HttpMethod::Options);
    }

    /** Forces endpoint as TRACE request */
    protected function HttpTrace(): void
    {
        $this->CompareHttpMethod(HttpMethod::Trace);
    }

    /** Forces endpoint as PATCH request */
    protected function HttpPatch(): void
    {
        $this->CompareHttpMethod(HttpMethod::Patch);
    }

    /** Makes checking HTTP Methods a bit simpler */
    private function CompareHttpMethod($requiredMethod): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== $requiredMethod) {
            throw new ApiException(
                HttpCode::MethodNotAllowed,
                "Endpoint does not support " . $_SERVER['REQUEST_METHOD']
            );
        }
    }

    /** Sets Response HTTP Code */
    protected function SetResponseCode(int $Code): void
    {
        http_response_code($Code);
    }
}
