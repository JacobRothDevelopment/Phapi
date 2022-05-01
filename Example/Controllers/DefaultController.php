<?php

namespace Controllers;

use Phapi\HttpCode;
use \IdRequest;

class DefaultController extends \Phapi\Controller
{
    public function IdGet(int $id)
    {
        $this->HttpGet();
        return [
            "id" => $id
        ];
    }

    public function IdPut(int $id)
    {
        $this->HttpPut();
        return [
            "id" => $id
        ];
    }

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
    public function DoubleGet(int $id, ?string $string)
    {
        $this->HttpGet();
        return [
            "id" => $id,
            "string" => $string
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
        return (object) [
            "SERVER" => $_SERVER,
            "parse_url['path']" => parse_url($_SERVER["REQUEST_URI"])['path'],
            "explode(parse_url['path'])" => explode("/", parse_url($_SERVER["REQUEST_URI"])['path']),
        ];
    }

    // PATCH /Default/Throw401
    public function Throw401()
    {
        $this->HttpPatch();
        throw new \Phapi\ApiException(HttpCode::Unauthorized);
    }

    public function weirdObject(int $id, object $thing)
    {
        $thing->id = $id;

        foreach ($thing->items as $item) {
            $item->name = $item->name . " - altered";
        }

        return ($thing);
    }

    public function postArray(?array $a)
    {
        $this->HttpPost();
        return $a;
    }
    public function postNestedClass(?\MasterItem $mi)
    {
        $this->HttpPost();
        return $mi;
    }
}
