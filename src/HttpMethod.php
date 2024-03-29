<?php

namespace Phapi;

/** https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods */
class HttpMethod
{
    public const Get = "GET";
    public const Head = "HEAD";
    public const Post = "POST";
    public const Put = "PUT";
    public const Delete = "DELETE";
    public const Connect = "CONNECT";
    public const Options = "OPTIONS";
    public const Trace = "TRACE";
    public const Patch = "PATCH";

    public const All = [
        HttpMethod::Get,
        HttpMethod::Head,
        HttpMethod::Post,
        HttpMethod::Put,
        HttpMethod::Delete,
        HttpMethod::Connect,
        HttpMethod::Options,
        HttpMethod::Trace,
        HttpMethod::Patch,
    ];
}
