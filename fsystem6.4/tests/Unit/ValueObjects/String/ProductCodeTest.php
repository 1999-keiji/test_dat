<?php

namespace Tests\Unit\ValueObjects\String;

use Tests\TestCase;
use App\ValueObjects\String\ProductCode;

class ProductCodeTest extends TestCase
{
    private $product_code;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->product_code = new ProductCode('OLT0001');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 空文字で初期化できない()
    {
        $product_code = new ProductCode('');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 最大文字数より多い文字数で初期化できない()
    {
        $product_code = new ProductCode(str_repeat('A', 16));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 記号を含む文字数で初期化できない()
    {
        $product_code = new ProductCode('AAA-0001');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 半角英小文字を含む文字数で初期化できない()
    {
        $product_code = new ProductCode('aaa0001');
    }

    /**
     * @test
     */
    public function 値を取得する()
    {
        $this->assertEquals('OLT0001', $this->product_code->value());
    }

    /**
     * @test
     */
    public function 文字列に変換する()
    {
        $this->assertEquals('OLT0001', (string)$this->product_code);
    }

    /**
     * @test
     */
    public function 正規表現を取得する()
    {
        $this->assertEquals("/\A[A-Z0-9]+\z/", $this->product_code->getRegexPattern());
    }

    /**
     * @test
     */
    public function ヘルプテキストを取得する()
    {
        $this->assertEquals(
            "15文字以内の半角英大文字、\n半角数字が入力できます。",
            $this->product_code->getHelpText()
        );
    }
}
