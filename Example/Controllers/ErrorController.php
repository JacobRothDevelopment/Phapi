<?php

namespace Controllers;

use Phapi\HttpCode;
use \IdRequest;
use \Item;
use Phapi\ApiException;
use Phapi\HttpMethod;

class ErrorController extends \Phapi\Controller
{
    public function Get()
    {
        $this->HttpGet();
        throw new ApiException(HttpCode::BadRequest, (object) [
            "message" => "no longer logged in",
            "newCode" => (object) [
                "somethingElse" => "something else"
            ]
        ]);
    }

    public function StringResponse()
    {
        $this->HttpGet();
        throw new ApiException(HttpCode::BadRequest, "string");
    }
}
