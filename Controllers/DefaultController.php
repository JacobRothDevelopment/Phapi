<?php

require_once(__DIR__ . "/../PhpApi/Controller.php");
require_once(__DIR__ . "/../Requests/IdRequest.php");

class DefaultController extends PhpApi\Controller
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

    // POST /Default/EchoId {id}
    public function EchoId(IdRequest $req)
    {
        $this->HttpPost();
        return $req;
    }

    // GET /Default/1
    public function GET(int $id)
    {
        $this->HttpGet();
        return [
            "success" => true,
            "id" => $id
        ];
    }

    // GET /Default/DoubleGet/1/2
    public function DoubleGet(int $id, ?int $idtwo)
    {
        $this->HttpGet();
        return [
            "id" => $id,
            "idtwo" => $idtwo
        ];
    }

    // POST /Default
    public function POST()
    {
        $this->HttpPost();
        $this->SetResponseCode(201);
        return [
            "success" => true
        ];
    }

    // GET /Default/Server
    public function Server()
    {
        $this->HttpGet();
        return $_SERVER;
    }

    // PATCH /Default/Throw401
    public function Throw401()
    {
        $this->HttpPatch();
        throw new PhpApi\ApiException(401);
    }
}
