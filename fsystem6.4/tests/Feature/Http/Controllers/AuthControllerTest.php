<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Master\User;

class AuthControllerTest extends TestCase
{
    private $user;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->user = User::find('hashimoto');
    }

    /**
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get('/login');
        $response->assertSuccessful();

        $response = $this->get('/');
        $response->assertRedirect('/login');

        $response = $this->actingAs($this->user)
            ->get('/login');
        $response->assertRedirect('/');
    }

    /**
     * @return void
     */
    public function testLogin()
    {
        $response = $this->post('/login', [
            'user_code' => 'hashimoto',
            'password' => 'hogehoge'
        ]);
        $this->assertGuest();

        $response = $this->post('/login', [
            'user_code' => 'hashimoto',
            'password' => 'hashimoto.01'
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('/');

        $response = $this->actingAs($this->user)
            ->post('/login', [
                'user_code' => 'hashimoto',
                'password' => 'hogehoge'
            ]);
        $response->assertRedirect('/');
    }

    /**
     * @return void
     */
    public function testLogout()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['hoge' => 'fuga'])
            ->post('/logout');
        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionMissing('hoge');
    }
}
