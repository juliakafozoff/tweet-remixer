<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    #[Test]
    public function login_screen_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
