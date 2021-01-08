<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function user_can_register()
    {
        $response = $this->post('/api/register', array_merge($this->data(), ['password_confirmation' => 'password']));

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
        $this->seeInDatabase('users', [
            'name' => 'Piotr',
            'email' => 'test@test.com',
        ]);
    }

    /**
     * @test
     */
    public function all_fields_are_required_in_registration()
    {
        $response = $this->post('/api/register', array_merge([
            'name' => '',
            'email' => '',
            'password' => ''
        ]));

        $this->assertEquals(422, $this->response->status());
        $this->seeJsonStructure([
            'name',
            'email',
            'password'
        ]);
    }

    // TODO only unauthenticated user can register
    // TODO only unauthenticated user can login

    /**
     * @test
     */
    public function can_login()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    /**
     * @test
     */
    public function all_fields_are_required_in_login()
    {
        $response = $this->post('/api/login', [
            'email' => '',
            'password' => '',
        ]);

        $this->assertEquals(422, $this->response->status());
        $this->seeJsonStructure([
            'email',
            'password'
        ]);
    }

    /**
     * @test
     */
    public function return_unauthorized_if_credentials_not_match()
    {
        $response = $this->post('/api/login', [
            'email' => 'email@email.com',
            'password' => 'password',
        ]);

        $this->assertEquals(401, $this->response->status());
        $this->seeJsonStructure([
            'error'
        ]);
        $this->seeJson([
            'error' => 'Unauthorized'
        ]);
    }

    /**
     * @test
     */
    public function should_return_user_data()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->get('/api/me');

        $this->assertEquals(200, $this->response->status());
        $this->seeJson([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    // TODO not autenticated user /api/me

    /**
     * @test
     */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->post('/api/logout');

        $this->assertEquals(200, $this->response->status());
        $this->seeJson(['message' => 'Successfully logged out']);

        $this->get('/api/me');

        $this->assertEquals(401, $this->response->status());
    }

    /**
     * @test
     */
    public function user_can_refresh_token()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->get('/api/refresh');

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    private function data()
    {
        return [
            'name' => 'Piotr',
            'email' => 'test@test.com',
            'password' => 'password',
        ];
    }
}
