<?php

namespace Tests\Unit\ValueObjects\String;

use Tests\TestCase;
use App\ValueObjects\String\CategoryCode;

class CategoryCodeTest extends TestCase
{
    private $category_code;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->category_code = new CategoryCode('OLT');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 最小文字数より少ない文字数で初期化できない()
    {
        $product_code = new CategoryCode(str_repeat('A', 2));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 最大文字数より多い文字数で初期化できない()
    {
        $product_code = new CategoryCode(str_repeat('A', 4));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 記号を含む文字数で初期化できない()
    {
        $product_code = new CategoryCode('A-A');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 記号を含む文字数で初期化できない2()
    {
        $product_code = new CategoryCode('A_A');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 記号を含む文字数で初期化できない3()
    {
        $product_code = new CategoryCode('A/A');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 数字を含む文字数で初期化できない()
    {
        $product_code = new CategoryCode('AA1');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 半角英小文字を含む文字数で初期化できない()
    {
        $product_code = new CategoryCode('olt');
    }

    /**
     * @test
     */
    public function 値を取得する()
    {
        $this->assertEquals('OLT', $this->category_code->value());
    }

    /**
     * @test
     */
    public function 文字列に変換する()
    {
        $this->assertEquals('OLT', (string)$this->category_code);
    }

    /**
     * @test
     */
    public function 最小文字数を取得する()
    {
        $this->assertEquals(3, $this->category_code->getMinLength());
    }

    /**
     * @test
     */
    public function 最大文字数を取得する()
    {
        $this->assertEquals(3, $this->category_code->getMaxLength());
    }

    /**
     * @test
     */
    public function 正規表現を取得する()
    {
        $this->assertEquals("/\A[A-Z]+\z/", $this->category_code->getRegexPattern());
    }

    /**
     * @test
     */
    public function ヘルプテキストを取得する()
    {
        $this->assertEquals(
            "3文字ちょうどの半角英大文字が入力できます。",
            $this->category_code->getHelpText()
        );
    }
}
