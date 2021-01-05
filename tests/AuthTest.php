<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    /**
     * @test
     */
    public function user_can_register()
    {
        $response = $this->post('/api/register', [
            'name' => 'Piotr',
            'email' => 'test@test.com',
            'password' => 'password'
        ]);

        $this->assertEquals(200, $this->response->status());
        // $this->seeJson([
        //     'access_token',
        //     'token_type',
        //     'expiers_in'
        // ]);

    }
}
