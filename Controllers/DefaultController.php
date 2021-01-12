<?php

use Phapi\HttpCode;

require_once(__DIR__ . "/../Requests/IdRequest.php");

class DefaultController extends Phapi\Controller
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
        return [
            "id" => $req->id
        ];
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
    public function DoubleGet(int $id, ?bool $bool)
    {
        $this->HttpGet();
        return [
            "id" => $id,
            "bool" => $bool
        ];
    }

    public function Update(int $id, string $name)
    {
        $this->HttpPut();
        return [
            "id from url" => $id,
            "name from body" => $name
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
        throw new Phapi\ApiException(HttpCode::Unauthorized);
    }
}
