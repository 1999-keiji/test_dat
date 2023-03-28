<?php

namespace Tests\Unit\ValueObjects\Integer;

use Tests\TestCase;
use App\ValueObjects\Integer\SalesOrderUnitQuantity;

class SalesOrderUnitQuantityTest extends TestCase
{
    private $sales_order_unit_quantity;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->sales_order_unit_quantity = new SalesOrderUnitQuantity(1);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 空文字で初期化できない()
    {
        $sales_order_unit_quantity = new SalesOrderUnitQuantity('');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function マイナス値で初期化できない()
    {
        $sales_order_unit_quantity = new SalesOrderUnitQuantity(-1);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function 最大値を上回る値で初期化できない()
    {
        $sales_order_unit_quantity = new SalesOrderUnitQuantity(1000000000);
    }

    /**
     * @test
     */
    public function 値を取得する()
    {
        $this->assertEquals(1, $this->sales_order_unit_quantity->value());
    }

    /**
     * @test
     */
    public function 文字列に変換する()
    {
        $this->assertEquals('1', (string)$this->sales_order_unit_quantity);
    }

    /**
     * @test
     */
    public function 最大数を取得する()
    {
        $this->assertEquals(999999999, $this->sales_order_unit_quantity->getMaximumNum());
    }

    /**
     * @test
     */
    public function 最大文字数を取得する()
    {
        $this->assertEquals(9, $this->sales_order_unit_quantity->getMaxLength());
    }

    /**
     * @test
     */
    public function ヘルプテキストを取得する()
    {
        $this->assertEquals(
            "9桁以内の半角正整数が\n入力できます。",
            $this->sales_order_unit_quantity->getHelpText()
        );
    }
}
