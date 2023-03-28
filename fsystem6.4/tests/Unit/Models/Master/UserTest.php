<?php

namespace Tests\Unit\Models\Master;

use Tests\TestCase;
use App\Models\Master\User;
use App\Models\Master\Collections\FactoryCollection;
use App\ValueObjects\Enum\Affiliation;

class UserTest extends TestCase
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
     * @test
     */
    public function getRememberTokenの無効化()
    {
        $this->assertNull($this->vgen_user->getRememberToken());
    }

    /**
     * @test
     */
    public function getRememberTokenNameの無効化()
    {
        $this->assertNull($this->vgen_user->getRememberTokenName());
    }

    /**
     * @test
     */
    public function アクセス可能か判定()
    {
        $this->assertTrue($this->vgen_user->canAccess('/master/products'));
        $this->assertTrue($this->vvf_user->canAccess('/master/products'));
        $this->assertFalse($this->factory_user->canAccess('/master/products'));

        $this->assertFalse($this->vgen_user->canAccess('/master/hoge'));
    }

    /**
     * @test
     */
    public function データ登録可能か判定()
    {
        $this->assertTrue($this->vgen_user->canSave('/master/products/add'));
        $this->assertFalse($this->vvf_user->canSave('/master/products/add'));
        $this->assertFalse($this->factory_user->canSave('/master/products/add'));

        $this->assertFalse($this->vgen_user->canSave('/master/fuga'));
    }

    /**
     * @test
     */
    public function 所属カラムがValueObjectでラップされる()
    {
        $this->assertInstanceOf(Affiliation::class, $this->vgen_user->affiliation);
    }

    /**
     * @test
     */
    public function 工場所属ユーザかどうか判定()
    {
        $this->assertFalse($this->vgen_user->belongsToFactory());
        $this->assertFalse($this->vvf_user->belongsToFactory());
        $this->assertTrue($this->factory_user->belongsToFactory());
    }

    /**
     * @test
     */
    public function 所属する工場の情報を取得する()
    {
        $this->assertInstanceOf(FactoryCollection::class, $this->factory_user->getAffilicatedFactories());
        $this->assertEquals(1, $this->factory_user->getAffilicatedFactories()->count());
    }

    /**
     * @test
     * @expectedException BadMethodCallException
     */
    public function VGEN所属ユーザの所属工場情報は取得できない()
    {
        $this->vgen_user->getAffilicatedFactories();
    }

    /**
     * @test
     * @expectedException BadMethodCallException
     */
    public function VVF所属ユーザの所属工場情報は取得できない()
    {
        $this->vvf_user->getAffilicatedFactories();
    }
}
