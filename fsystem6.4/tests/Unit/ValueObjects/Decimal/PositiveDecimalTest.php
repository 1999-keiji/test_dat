<?php

namespace Tests\Unit\ValueObjects\Decimal;

use Tests\TestCase;
use App\ValueObjects\Decimal\PositiveDecimal;

class PositiveDecimalTest extends TestCase
{
    private $positive_decimal;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->positive_decimal = $this->getMockForAbstractClass(PositiveDecimal::class);
    }

    /**
     * @test
     */
    public function 最小数を取得する()
    {
        $this->assertEquals(0, $this->positive_decimal->getMinimumNum());
    }

    /**
     * @test
     */
    public function 最大数を取得する()
    {
        $this->assertEquals(999999999.99999, $this->positive_decimal->getMaximumNum());
    }

    /**
     * @test
     */
    public function 最大文字数を取得する()
    {
        $this->assertEquals(15, $this->positive_decimal->getMaxLength());
    }

    /**
     * @test
     */
    public function 正規表現を取得する()
    {
        $this->assertEquals("/\A([1-9][0-9]{0,9}|0)(\.[0-9]{1,5})?\z/", $this->positive_decimal->getRegexPattern());
    }

    /**
     * @test
     */
    public function 小数点以下桁数を取得する()
    {
        $this->assertEquals(5, $this->positive_decimal->getDecimals());
    }

    /**
     * @test
     */
    public function ヘルプテキストを取得する()
    {
        $this->assertEquals(
            "小数点も含めて15桁以内の\n半角正数値が入力できます。\n小数点以下は5桁まで\n入力できます。",
            $this->positive_decimal->getHelpText()
        );
    }

    /**
     * @test
     * @expectedException BadMethodCallException
     */
    public function __setメソッドを利用することで例外が発生する()
    {
        $this->positive_decimal->hoge = 'fuga';
    }
}
