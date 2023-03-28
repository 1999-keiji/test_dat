<?php

namespace Tests\Unit\ValueObjects\String;

use Tests\TestCase;
use App\ValueObjects\String\CountryCode;

class CountryCodeTest extends TestCase
{
    private $country_code;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->country_code = new CountryCode('JP');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 最小文字数より少ない文字数で初期化できない()
    {
        $country_code = new CountryCode('J');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 最大文字数より多い文字数で初期化できない()
    {
        $country_code = new CountryCode(str_repeat('J', 3));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 記号を含む文字数で初期化できない()
    {
        $country_code = new CountryCode('J-');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 記号を含む文字数で初期化できない2()
    {
        $country_code = new CountryCode('_J');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 記号を含む文字数で初期化できない3()
    {
        $country_code = new CountryCode('J/');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 数字を含む文字数で初期化できない()
    {
        $country_code = new CountryCode('1J');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 半角英小文字を含む文字数で初期化できない()
    {
        $country_code = new CountryCode('Jp');
    }

    /**
     * @test
     */
    public function 値を取得する()
    {
        $this->assertEquals('JP', $this->country_code->value());
    }

    /**
     * @test
     */
    public function 文字列に変換する()
    {
        $this->assertEquals('JP', (string)$this->country_code);
    }

    /**
     * @test
     */
    public function 最小文字数を取得する()
    {
        $this->assertEquals(2, $this->country_code->getMinLength());
    }

    /**
     * @test
     */
    public function 最大文字数を取得する()
    {
        $this->assertEquals(2, $this->country_code->getMaxLength());
    }

    /**
     * @test
     */
    public function 正規表現を取得する()
    {
        $this->assertEquals("/\A[A-Z]+\z/", $this->country_code->getRegexPattern());
    }

    /**
     * @test
     */
    public function ヘルプテキストを取得する()
    {
        $this->assertEquals(
            "2文字ちょうどの半角英大文字が入力できます。",
            $this->country_code->getHelpText()
        );
    }
}
