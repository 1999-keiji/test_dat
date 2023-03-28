<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Master\User;

class IndexControllerTest extends TestCase
{
    private $vgen_user;

    private $vvf_user;

    private $factory_user;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->vgen_user = User::find('hashimoto');
        $this->vvf_user = User::find('takita');
        $this->factory_user = User::find('chishima');
    }

    /**
     * @return void
     */
    public function testIndex()
    {
        $response = $this->actingAs($this->vgen_user)
            ->get('/');
        $response->assertSuccessful();
        $response->assertSee('メインメニュー');
        $response->assertSee('マスタ管理メニュー');
        $response->assertSee('商品マスタ');
        $response->assertViewHas('categories', [
            'master' => ['products']
        ]);

        $response = $this->actingAs($this->vvf_user)
            ->get('/');
        $response->assertSee('商品マスタ');

        $response = $this->actingAs($this->factory_user)
            ->get('/');
        $response->assertDontSee('商品マスタ');
    }

    /**
     * @test
     */
    public function testClear()
    {
        $response = $this->actingAs($this->vgen_user)
            ->withSession(['master' => 'hoge'])
            ->post('/clear');

        $response->assertRedirect('/');
        $response->assertSessionMissing('master');
    }
}
