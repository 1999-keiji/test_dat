<?php

namespace Tests\Unit\ValueObjects\Enum;

use Tests\TestCase;
use App\ValueObjects\Enum\ProductClass;

class ProductClassTest extends TestCase
{
    private $product_class;

    private $product;

    private $not_product;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->product_class = new ProductClass();
        $this->product = new ProductClass('1');
        $this->not_product = new ProductClass('2');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 規定値以外で初期化できない()
    {
        $enum = new ProductClass('3');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 型が合っていないと初期化できない()
    {
        $enum = new ProductClass(2);
    }

    /**
     * @test
     */
    public function 値を取得する()
    {
        $this->assertEquals('1', $this->product->value());
        $this->assertEquals('2', $this->not_product->value());
    }

    /**
     * @test
     */
    public function ラベルを取得する()
    {
        $this->assertEquals('製品', $this->product->label());
        $this->assertEquals('製品外', $this->not_product->label());
    }

    /**
     * @test
     */
    public function 文字列に変換する()
    {
        $this->assertEquals('1', (string)$this->product);
        $this->assertEquals('2', (string)$this->not_product);
    }

    /**
     * @test
     */
    public function 規定値をすべて取得する()
    {
        $this->assertEquals([
            '製品' => '1',
            '製品外' => '2'
        ], $this->product_class->all());
    }
}
