<?php

class SpecialController extends PhpApi\Controller
{
    public function DontKnowWhyYoudUseThisButHereItIs(int $id)
    {
        $this->HttpGet();
        return $id;
    }
}
