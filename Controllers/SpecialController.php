<?php

class SpecialController extends Phapi\Controller
{
    public function DontKnowWhyYoudUseThisButHereItIs(int $id)
    {
        $this->HttpGet();
        return $id;
    }
}
