<?php

namespace Tests\Unit\ValueObjects\Decimal;

use Tests\TestCase;
use App\ValueObjects\Decimal\ProductSize;

class ProductSizeTest extends TestCase
{
    private $product_size;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->product_size = new ProductSize(100.05);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 空文字で初期化できない()
    {
        $product_size = new ProductSize('');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function マイナス値で初期化できない()
    {
        $product_size = new ProductSize(-1);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 最大値を上回る値で初期化できない()
    {
        $product_size = new ProductSize(100000);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 小数点以下の桁数が規定を上回る値で初期化できない()
    {
        $product_size = new ProductSize(99999.999);
    }

    /**
     * @test
     */
    public function 値を取得する()
    {
        $this->assertEquals(100.05, $this->product_size->value());
    }

    /**
     * @test
     */
    public function 文字列に変換する()
    {
        $this->assertEquals('100.05', (string)$this->product_size);
    }

    /**
     * @test
     */
    public function 最大数を取得する()
    {
        $this->assertEquals(99999.99, $this->product_size->getMaximumNum());
    }

    /**
     * @test
     */
    public function 最大文字数を取得する()
    {
        $this->assertEquals(8, $this->product_size->getMaxLength());
    }

    /**
     * @test
     */
    public function 正規表現を取得する()
    {
        $this->assertEquals("/\A([1-9][0-9]{0,5}|0)(\.[0-9]{1,2})?\z/", $this->product_size->getRegexPattern());
    }

    /**
     * @test
     */
    public function 小数点以下桁数を取得する()
    {
        $this->assertEquals(2, $this->product_size->getDecimals());
    }

    /**
     * @test
     */
    public function ヘルプテキストを取得する()
    {
        $this->assertEquals(
            "小数点も含めて8桁以内の\n半角正数値が入力できます。\n小数点以下は2桁まで\n入力できます。",
            $this->product_size->getHelpText()
        );
    }
}
