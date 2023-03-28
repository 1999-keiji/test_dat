<?php

namespace Tests\Unit\ValueObjects\Decimal;

use Tests\TestCase;
use App\ValueObjects\Decimal\ProductWeight;

class ProductWeightTest extends TestCase
{
    private $product_weight;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->product_weight = new ProductWeight(240.000);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 空文字で初期化できない()
    {
        $product_weight = new ProductWeight('');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function マイナス値で初期化できない()
    {
        $product_weight = new ProductWeight(-1);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 最大値を上回る値で初期化できない()
    {
        $product_weight = new ProductWeight(1000000);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 小数点以下の桁数が規定を上回る値で初期化できない()
    {
        $product_weight = new ProductWeight(999999.9999);
    }

    /**
     * @test
     */
    public function 値を取得する()
    {
        $this->assertEquals(240.000, $this->product_weight->value());
    }

    /**
     * @test
     */
    public function 文字列に変換する()
    {
        $this->assertEquals('240.000', (string)$this->product_weight);
    }

    /**
     * @test
     */
    public function 最大数を取得する()
    {
        $this->assertEquals(999999.999, $this->product_weight->getMaximumNum());
    }

    /**
     * @test
     */
    public function 最大文字数を取得する()
    {
        $this->assertEquals(10, $this->product_weight->getMaxLength());
    }

    /**
     * @test
     */
    public function 正規表現を取得する()
    {
        $this->assertEquals("/\A([1-9][0-9]{0,6}|0)(\.[0-9]{1,3})?\z/", $this->product_weight->getRegexPattern());
    }

    /**
     * @test
     */
    public function 小数点以下桁数を取得する()
    {
        $this->assertEquals(3, $this->product_weight->getDecimals());
    }

    /**
     * @test
     */
    public function ヘルプテキストを取得する()
    {
        $this->assertEquals(
            "小数点も含めて10桁以内の\n半角正数値が入力できます。\n小数点以下は3桁まで\n入力できます。",
            $this->product_weight->getHelpText()
        );
    }
}
