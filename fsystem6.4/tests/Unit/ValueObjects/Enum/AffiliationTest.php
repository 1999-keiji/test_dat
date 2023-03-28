<?php

namespace Tests\Unit\ValueObjects\Enum;

use Tests\TestCase;
use App\ValueObjects\Enum\Affiliation;

class AffiliationTest extends TestCase
{
    private $affiliation;

    private $vgen;

    private $vvf;

    private $factory;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->affiliation = new Affiliation();
        $this->vgen = new Affiliation(1);
        $this->vvf = new Affiliation(2);
        $this->factory = new Affiliation(3);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 規定値以外で初期化できない()
    {
        $enum = new Affiliation(0);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 型が合っていないと初期化できない()
    {
        $enum = new Affiliation('3');
    }

    /**
     * @test
     */
    public function 値を取得する()
    {
        $this->assertEquals(1, $this->vgen->value());
        $this->assertEquals(2, $this->vvf->value());
        $this->assertEquals(3, $this->factory->value());
    }

    /**
     * @test
     */
    public function ラベルを取得する()
    {
        $this->assertEquals('VGEN', $this->vgen->label());
        $this->assertEquals('VVF', $this->vvf->label());
        $this->assertEquals('工場', $this->factory->label());
    }

    /**
     * @test
     */
    public function 文字列に変換する()
    {
        $this->assertEquals('1', (string)$this->vgen);
        $this->assertEquals('2', (string)$this->vvf);
        $this->assertEquals('3', (string)$this->factory);
    }

    /**
     * @test
     */
    public function 規定値をすべて取得する()
    {
        $this->assertEquals([
            'VGEN' => 1,
            'VVF' => 2,
            '工場' => 3
        ], $this->affiliation->all());
    }

    /**
     * @test
     */
    public function 工場所属を示すかどうか判定する()
    {
        $this->assertFalse($this->vgen->belongsToFactory());
        $this->assertFalse($this->vvf->belongsToFactory());
        $this->assertTrue($this->factory->belongsToFactory());
    }
}
