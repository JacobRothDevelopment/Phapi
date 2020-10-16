<?php
require_once(__DIR__ . "/../PhpApi/Controller.php");
require_once(__DIR__ . "/../Requests/IdRequest.php");

class TestController extends PhpApi\Controller
{
    // GET /Default/Values
    public function Values()
    {
        $this->HttpGet();
        return [
            "1" => "value1",
            "2" => "value2"
        ];
    }

    // GET /Default/EchoId?id={id}
    public function EchoId(IdRequest $req)
    {
        $this->HttpGet();
        return $req;
    }

    // GET /Default
    public function GET()
    {
        $this->HttpGet();
        return [
            "success" => true
        ];
    }
}
