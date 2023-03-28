<?php

namespace Tests\Unit\ValueObjects\Integer;

use Tests\TestCase;
use App\ValueObjects\Integer\PositiveInteger;

class PositiveIntegerTest extends TestCase
{
    private $positive_int;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->positive_int = $this->getMockForAbstractClass(PositiveInteger::class);
    }

    /**
     * @test
     */
    public function 最小数を取得する()
    {
        $this->assertEquals(0, $this->positive_int->getMinimumNum());
    }

    /**
     * @test
     */
    public function 最大数を取得する()
    {
        $this->assertEquals(999999999999999999, $this->positive_int->getMaximumNum());
    }

    /**
     * @test
     */
    public function 最大文字数を取得する()
    {
        $this->assertEquals(18, $this->positive_int->getMaxLength());
    }

    /**
     * @test
     */
    public function 小数点以下桁数を取得する()
    {
        $this->assertEquals(0, $this->positive_int->getDecimals());
    }

    /**
     * @test
     */
    public function ヘルプテキストを取得する()
    {
        $this->assertEquals(
            "18桁以内の半角正整数が\n入力できます。",
            $this->positive_int->getHelpText()
        );
    }

    /**
     * @test
     * @expectedException BadMethodCallException
     */
    public function __setメソッドを利用することで例外が発生する()
    {
        $this->positive_int->hoge = 'fuga';
    }
}
