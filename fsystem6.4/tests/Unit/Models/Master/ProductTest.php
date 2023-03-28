<?php

namespace Tests\Unit\Models\Master;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Master\Collections\ProductCollection;
use App\Models\Master\FactoryProduct;
use App\Models\Master\Product;
use App\ValueObjects\Enum\CreatingType;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    private $base_plus_linked;

    private $manual_created;

    private $manual_created_has_factory_products;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->base_plus_linked = factory(Product::class, 'BASE+連携')->create();
        $this->manual_created = factory(Product::class, '手動登録')->create();
        $this->manual_created_has_factory_products = factory(Product::class, '手動登録_工場商品登録済用')->create();
        $this->manual_created_has_factory_products
            ->factory_products()->saveMany(factory(FactoryProduct::class, 1)->make());
    }

    /**
     * @test
     */
    public function 削除可能か判定する()
    {
        $this->assertFalse($this->base_plus_linked->isDeletable());
        $this->assertTrue($this->manual_created->isDeletable());
        $this->assertFalse($this->manual_created_has_factory_products->isDeletable());
    }

    /**
     * @test
     */
    public function allメソッドの返り値の型が正しい()
    {
        $this->assertInstanceOf(ProductCollection::class, $this->base_plus_linked->all());
    }

    /**
     * @test
     */
    public function 紐づく品種マスタの情報を参照できる()
    {
        $this->assertEquals('フリルレタス', $this->base_plus_linked->species->species_name);
    }

    /**
     * @test
     */
    public function 紐づく工場取扱商品の情報を参照できる()
    {
        $this->assertTrue($this->manual_created->factory_products->isEmpty());
        $this->assertTrue($this->manual_created_has_factory_products->factory_products->isNotEmpty());
    }

    /**
     * @test
     */
    public function 作成種別カラムがValueObjectでラップされる()
    {
        $this->assertInstanceOf(CreatingType::class, $this->base_plus_linked->creating_type);
    }

    /**
     * @test
     */
    public function BASEプラスから連携される項目を取得する()
    {
        $this->assertEquals([
            'product_code',
            'species_code',
            'product_name',
            'result_addup_code',
            'result_addup_name',
            'result_addup_abbreviation',
            'product_large_category',
            'product_middle_category',
            'product_class',
            'custom_product_flag',
            'sales_order_unit',
            'sales_order_unit_quantity',
            'minimum_sales_order_unit_quantity',
            'statement_of_delivery_name',
            'pickup_slip_message',
            'lot_target_flag',
            'species_name',
            'export_target_flag',
            'net_weight',
            'gross_weight',
            'depth',
            'width',
            'height',
            'country_of_origin',
            'itm_class2',
            'itm_class3',
            'itm_class4',
            'itm_class5',
            'itm_flag1',
            'itm_flag2',
            'itm_flag3',
            'itm_flag4',
            'itm_flag5',
        ], $this->base_plus_linked->getLinkedColumns());
    }

    /**
     * @test
     */
    public function 更新可能な項目か判定する()
    {
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('product_code'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('product_code'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('species_code'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('species_code'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('product_name'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('product_name'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('result_addup_code'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('result_addup_code'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('result_addup_name'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('result_addup_name'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('result_addup_abbreviation'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('result_addup_abbreviation'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('product_large_category'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('product_large_category'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('product_middle_category'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('product_middle_category'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('product_class'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('product_class'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('custom_product_flag'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('custom_product_flag'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('sales_order_unit'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('sales_order_unit'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('sales_order_unit_quantity'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('sales_order_unit_quantity'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('minimum_sales_order_unit_quantity'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('minimum_sales_order_unit_quantity'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('statement_of_delivery_name'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('statement_of_delivery_name'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('pickup_slip_message'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('pickup_slip_message'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('lot_target_flag'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('lot_target_flag'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('species_name'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('species_name'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('export_target_flag'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('export_target_flag'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('net_weight'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('net_weight'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('gross_weight'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('gross_weight'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('depth'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('depth'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('width'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('width'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('height'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('height'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('country_of_origin'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('country_of_origin'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('itm_class2'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('itm_class2'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('itm_class3'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('itm_class3'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('itm_class4'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('itm_class4'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('itm_class5'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('itm_class5'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('itm_flag1'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('itm_flag1'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('itm_flag2'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('itm_flag2'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('itm_flag3'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('itm_flag3'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('itm_flag4'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('itm_flag4'));
        $this->assertTrue($this->base_plus_linked->isDisabledToUpdate('itm_flag5'));
        $this->assertEquals('disabled', $this->base_plus_linked->addDisabledProp('itm_flag5'));
        $this->assertFalse($this->base_plus_linked->isDisabledToUpdate('remark'));
        $this->assertEquals('', $this->base_plus_linked->addDisabledProp('remark'));

        $this->assertFalse($this->manual_created->isDisabledToUpdate('product_code'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('product_code'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('species_code'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('species_code'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('product_name'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('product_name'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('result_addup_code'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('result_addup_code'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('result_addup_name'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('result_addup_name'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('result_addup_abbreviation'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('result_addup_abbreviation'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('product_large_category'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('product_large_category'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('product_middle_category'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('product_middle_category'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('product_class'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('product_class'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('custom_product_flag'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('custom_product_flag'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('sales_order_unit'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('sales_order_unit'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('sales_order_unit_quantity'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('sales_order_unit_quantity'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('minimum_sales_order_unit_quantity'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('minimum_sales_order_unit_quantity'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('statement_of_delivery_name'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('statement_of_delivery_name'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('pickup_slip_message'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('pickup_slip_message'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('lot_target_flag'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('lot_target_flag'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('species_name'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('species_name'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('export_target_flag'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('export_target_flag'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('net_weight'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('net_weight'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('gross_weight'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('gross_weight'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('depth'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('depth'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('width'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('width'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('height'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('height'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('country_of_origin'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('country_of_origin'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('itm_class2'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('itm_class2'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('itm_class3'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('itm_class3'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('itm_class4'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('itm_class4'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('itm_class5'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('itm_class5'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('itm_flag1'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('itm_flag1'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('itm_flag2'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('itm_flag2'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('itm_flag3'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('itm_flag3'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('itm_flag4'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('itm_flag4'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('itm_flag5'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('itm_flag5'));
        $this->assertFalse($this->manual_created->isDisabledToUpdate('remark'));
        $this->assertEquals('', $this->manual_created->addDisabledProp('remark'));
    }
}
