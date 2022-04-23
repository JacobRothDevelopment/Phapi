<?php

namespace V2;

class SpecialController extends \Phapi\Controller
{
    public function DoNotKnowWhyYouWouldUseThisButHereItIs(int $id)
    {
        $this->HttpGet();
        return $id;
    }
}
