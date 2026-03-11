<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Enums\UserRole;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::create([
            'email' => 'admin@email.com',
            'password' => bcrypt('123456'),
            'role' => UserRole::ADMIN,
            'name' => 'maycon'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@email.com'
        ]);
    }
}
