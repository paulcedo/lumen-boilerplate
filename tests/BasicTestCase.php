<?php

namespace Tests;

use TestCase;

class BasicTestCase extends TestCase
{
    protected $header;

    public function setUp():void
    {
        parent::setUp();

        $this->header = [
            // 'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
    }
}
