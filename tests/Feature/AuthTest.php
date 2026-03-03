<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_as_buyer()
    {
        $response = $this->postJson('/api/register', [
            'name'     => 'Test Buyer',
            'email'    => 'buyer@test.com',
            'phone'    => '081234567890',
            'address'  => 'Jl. Test No. 123',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id', 'name', 'email', 'roles'
                    ]
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'buyer@test.com'
        ]);

        $this->assertDatabaseHas('buyers', [
            'name'  => 'Test Buyer',
            'phone' => '081234567890'
        ]);
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'buyerlogin@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'buyerlogin@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user',
                    'token'
                ]
            ]);
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'email'    => 'wrongpass@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'wrongpass@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }
}
