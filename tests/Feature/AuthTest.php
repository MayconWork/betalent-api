<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\UserRole;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login()
    {
        $user = User::create([
            'email' => 'admin@test.com',
            'password' => bcrypt('123456'),
            'role' =>UserRole::ADMIN,
            'name' => 'maycon'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token'
            ]);
    }
}
