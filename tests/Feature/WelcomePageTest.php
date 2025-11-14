<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_users_see_the_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertViewIs('welcome');
    }

    #[Test]
    public function authenticated_users_are_redirected_to_the_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('dashboard'));
    }
}

