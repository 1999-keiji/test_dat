<?php

namespace Tests\Unit\ValueObjects\Enum;

use Tests\TestCase;
use App\ValueObjects\Enum\CreatingType;

class CreatingTypeTest extends TestCase
{
    private $creating_type;

    private $base_plus_linked;

    private $manual_created;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->creating_type = new CreatingType();
        $this->base_plus_linked = new CreatingType(1);
        $this->manual_created = new CreatingType(2);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 規定値以外で初期化できない()
    {
        $enum = new CreatingType(3);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 型が合っていないと初期化できない()
    {
        $enum = new CreatingType('2');
    }

    /**
     * @test
     */
    public function 値を取得する()
    {
        $this->assertEquals(1, $this->base_plus_linked->value());
        $this->assertEquals(2, $this->manual_created->value());
    }

    /**
     * @test
     */
    public function ラベルを取得する()
    {
        $this->assertEquals('BASE+連携', $this->base_plus_linked->label());
        $this->assertEquals('手動登録', $this->manual_created->label());
    }

    /**
     * @test
     */
    public function 文字列に変換する()
    {
        $this->assertEquals('1', (string)$this->base_plus_linked);
        $this->assertEquals('2', (string)$this->manual_created);
    }

    /**
     * @test
     */
    public function 規定値をすべて取得する()
    {
        $this->assertEquals([
            'BASE+連携' => 1,
            '手動登録' => 2
        ], $this->creating_type->all());
    }

    /**
     * @test
     */
    public function 更新可能な作成種別を取得する()
    {
        $this->assertEquals([2], $this->creating_type->getUpdatableCreatingTypes());
    }

    /**
     * @test
     */
    public function 更新可能な作成種別かどうか判定する()
    {
        $this->assertFalse($this->base_plus_linked->isUpdatableCreatingType());
        $this->assertTrue($this->manual_created->isUpdatableCreatingType());
    }

    /**
     * @test
     */
    public function 削除可能な作成種別を取得する()
    {
        $this->assertEquals([2], $this->creating_type->getDeletableCreatingTypes());
    }

    /**
     * @test
     */
    public function 削除可能な作成種別かどうか判定する()
    {
        $this->assertFalse($this->base_plus_linked->isDeletableCreatingType());
        $this->assertTrue($this->manual_created->isDeletableCreatingType());
    }
}
