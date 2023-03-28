<?php

namespace Tests\Unit\ValueObjects\Enum;

use Tests\TestCase;
use App\ValueObjects\Enum\Permission;

class PermissionTest extends TestCase
{
    private $permission;

    private $forbidden;

    private $readable;

    private $writable;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->permission = new Permission();
        $this->forbidden = new Permission(0);
        $this->readable = new Permission(1);
        $this->writable = new Permission(2);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 規定値以外で初期化できない()
    {
        $enum = new Permission(3);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 型が合っていないと初期化できない()
    {
        $enum = new Permission('0');
    }

    /**
     * @test
     */
    public function 値を取得する()
    {
        $this->assertEquals(0, $this->forbidden->value());
        $this->assertEquals(1, $this->readable->value());
        $this->assertEquals(2, $this->writable->value());
    }

    /**
     * @test
     */
    public function ラベルを取得する()
    {
        $this->assertEquals('権限なし', $this->forbidden->label());
        $this->assertEquals('参照のみ', $this->readable->label());
        $this->assertEquals('登録可', $this->writable->label());
    }

    /**
     * @test
     */
    public function 文字列に変換する()
    {
        $this->assertEquals('0', (string)$this->forbidden);
        $this->assertEquals('1', (string)$this->readable);
        $this->assertEquals('2', (string)$this->writable);
    }

    /**
     * @test
     */
    public function 規定値をすべて取得する()
    {
        $this->assertEquals([
            '権限なし' => 0,
            '参照のみ' => 1,
            '登録可' => 2
        ], $this->permission->all());
    }

    /**
     * @test
     */
    public function アクセス権限の有無を判定する()
    {
        $this->assertFalse($this->forbidden->canAccess());
        $this->assertTrue($this->readable->canAccess());
        $this->assertTrue($this->writable->canAccess());
    }

    /**
     * @test
     */
    public function データ登録権限の有無を判定する()
    {
        $this->assertFalse($this->forbidden->canSave());
        $this->assertFalse($this->readable->canSave());
        $this->assertTrue($this->writable->canSave());
    }
}
