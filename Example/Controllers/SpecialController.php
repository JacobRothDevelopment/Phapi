<?php

namespace V2;

class SpecialController extends \Phapi\Controller
{
    public function DontKnowWhyYoudUseThisButHereItIs(int $id)
    {
        $this->HttpGet();
        return $id;
    }
}
