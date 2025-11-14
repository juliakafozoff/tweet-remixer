<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.key', 'base64:Jj8XqVhtBn3ypvPZy2V99SLBf7q5NdQmPSYaOksYbog=');
        config()->set('app.cipher', 'AES-256-CBC');
    }
}
