<?php

namespace Tests\Unit\Http\Requests\Master;

use Mockery as m;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ServerBag;
use Tests\TestCase;
use App\Http\Requests\Master\UpdateProductRequest;
use App\Models\Master\Product;
use App\Models\Master\Species;
use App\ValueObjects\Decimal\ProductSize;
use App\ValueObjects\Decimal\ProductWeight;
use App\ValueObjects\Enum\ProductClass;
use App\ValueObjects\Integer\SalesOrderUnitQuantity;
use App\ValueObjects\String\CategoryCode;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\ProductCode;

class UpdateProductRequestTest extends TestCase
{
    private $update_base_plus_linked_request;

    private $update_manual_created_request;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $product = m::mock(Product::class);
        $product->shouldReceive('getWillCastAsBoolean')
            ->andReturn([
                'custom_product_flag',
                'lot_target_flag',
                'export_target_flag'
            ]);

        $species = m::mock(Species::class);
        $species->shouldReceive('getTable')
            ->andReturn('species');

        $update_product_request = new UpdateProductRequest(
            $product,
            $species,
            new ProductCode(),
            new CategoryCode(),
            new ProductClass(),
            new SalesOrderUnitQuantity(),
            new ProductWeight(),
            new ProductSize(),
            new CountryCode()
        );

        $update_product_request->server = new ServerBag([]);
        $update_product_request->headers = new HeaderBag($update_product_request->server->getHeaders());

        $update_product_request->setRouteResolver(function () use ($update_product_request) {
            $route = new Route('', 'GET', []);
            $route->bind($update_product_request);
            $route->setParameter('product', factory(Product::class, 'BASE+連携')->make());

            return $route;
        });
        $this->update_base_plus_linked_request = $update_product_request;

        $update_product_request->setRouteResolver(function () use ($update_product_request) {
            $route = new Route('', 'GET', []);
            $route->bind($update_product_request);
            $route->setParameter('product', factory(Product::class, '手動登録')->make());

            return $route;
        });
        $this->update_manual_created_request = $update_product_request;
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        m::close();

        parent::tearDown();
    }

    /**
     * @test
     */
    public function BASEPlus連携商品更新用パラメータのバリデーション()
    {
        $params = $this->getValidParams(1);
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['species_code'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['product_name'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['result_addup_code'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['result_addup_name'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['result_addup_abbreviation'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['product_large_category'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['product_middle_category'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['product_class'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['custom_product_flag'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['sales_order_unit'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['sales_order_unit_quantity'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['minimum_sales_order_unit_quantity'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['statement_of_delivery_name'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['pickup_slip_message'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['lot_target_flag'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['species_name'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['export_target_flag'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['net_weight'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['gross_weight'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['depth'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['width'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['height'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['country_of_origin'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['remark'] = '';
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['remark'] = str_repeat('A', 255);
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(1);
        $params['remark'] = str_repeat('A', 256);
        $validator = Validator::make($params, $this->update_base_plus_linked_request->rules());
        $this->assertFalse($validator->passes());

        $this->assertTrue($this->update_base_plus_linked_request->authorize());

        $this->assertEquals(['species_code' => '品種'], $this->update_base_plus_linked_request->attributes());
    }

    /**
     * @test
     */
    public function 手動登録商品更新用パラメータのバリデーション()
    {
        $params = $this->getValidParams(2);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['species_code'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['species_code'] = '0001FL';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_name'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_name'] = str_repeat('A', 40);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_name'] = str_repeat('A', 41);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['result_addup_code'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['result_addup_code'] = str_repeat('A', 10);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['result_addup_code'] = str_repeat('A', 11);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['result_addup_code'] = 'A1B2C3';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['result_addup_name'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['result_addup_name'] = str_repeat('A', 30);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['result_addup_name'] = str_repeat('A', 31);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['result_addup_abbreviation'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['result_addup_abbreviation'] = str_repeat('A', 10);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['result_addup_abbreviation'] = str_repeat('A', 11);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_large_category'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_large_category'] = str_repeat('A', 2);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_large_category'] = str_repeat('A', 3);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_large_category'] = str_repeat('A', 4);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_large_category'] = 'olt';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_middle_category'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_middle_category'] = str_repeat('A', 2);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_middle_category'] = str_repeat('A', 3);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_middle_category'] = str_repeat('A', 4);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_middle_category'] = 'o1t';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_class'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_class'] = '2';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['product_class'] = '3';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['custom_product_flag'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['custom_product_flag'] = '2';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['sales_order_unit'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['sales_order_unit'] = str_repeat('A', 3);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['sales_order_unit'] = str_repeat('A', 4);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['sales_order_unit_quantity'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['sales_order_unit_quantity'] = 'A';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['sales_order_unit_quantity'] = '-1';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['sales_order_unit_quantity'] = '0';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['sales_order_unit_quantity'] = '999999999';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['sales_order_unit_quantity'] = '1000000000';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['minimum_sales_order_unit_quantity'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['minimum_sales_order_unit_quantity'] = 'A';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['minimum_sales_order_unit_quantity'] = '-1';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['minimum_sales_order_unit_quantity'] = '0';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['minimum_sales_order_unit_quantity'] = '999999999';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['minimum_sales_order_unit_quantity'] = '1000000000';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['statement_of_delivery_name'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['statement_of_delivery_name'] = str_repeat('A', 50);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['statement_of_delivery_name'] = str_repeat('A', 51);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['pickup_slip_message'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['pickup_slip_message'] = str_repeat('A', 40);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['pickup_slip_message'] = str_repeat('A', 41);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['lot_target_flag'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['lot_target_flag'] = '2';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['species_name'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['species_name'] = str_repeat('A', 25);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['species_name'] = str_repeat('A', 26);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['export_target_flag'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['export_target_flag'] = '2';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['net_weight'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['net_weight'] = 'A';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['net_weight'] = '-1';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['net_weight'] = '0';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['net_weight'] = '999999.999';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['net_weight'] = '1000000';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['net_weight'] = '0.0001';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['gross_weight'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['gross_weight'] = 'A';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['gross_weight'] = '-1';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['gross_weight'] = '0';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['gross_weight'] = '999999.999';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['gross_weight'] = '1000000';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['gross_weight'] = '0.0001';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['depth'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['depth'] = 'A';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['depth'] = '-1';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['depth'] = '0';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['depth'] = '99999.99';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['depth'] = '100000';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['depth'] = '0.001';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['width'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['width'] = 'A';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['width'] = '-1';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['width'] = '0';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['width'] = '99999.99';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['width'] = '100000';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['width'] = '0.001';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['height'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['height'] = 'A';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['height'] = '-1';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['height'] = '0';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['height'] = '99999.99';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['height'] = '100000';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['height'] = '0.001';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['country_of_origin'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['country_of_origin'] = str_repeat('A', 1);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['country_of_origin'] = str_repeat('A', 2);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['country_of_origin'] = str_repeat('A', 3);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['country_of_origin'] = 'jp';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['remark'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['remark'] = str_repeat('A', 255);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams(2);
        $params['remark'] = str_repeat('A', 256);
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['updated_at'] = '';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['updated_at'] = 'A';
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams(2);
        $params['updated_at'] = date('YmdHis');
        $validator = Validator::make($params, $this->update_manual_created_request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * @param  int $creatig_type
     * @return array
     */
    private function getValidParams(int $creatig_type)
    {
        return [
            'creating_type' => $creatig_type,
            'species_code' => '0001-FL',
            'product_name' => 'テスト商品',
            'result_addup_code' => 'OLTOLTOLT',
            'result_addup_name' => 'テスト商品',
            'result_addup_abbreviation' => 'ﾃｽﾄｼｮｳﾋﾝ',
            'product_large_category' => 'OLT',
            'product_middle_category' => 'OLT',
            'product_class' => '1',
            'custom_product_flag' => '1',
            'sales_order_unit' => 'ｹｰｽ',
            'sales_order_unit_quantity' => '1',
            'minimum_sales_order_unit_quantity' => '1',
            'statement_of_delivery_name' => 'テストテスト',
            'pickup_slip_message' => 'テストテスト',
            'lot_target_flag' => '0',
            'species_name' => 'ﾌﾘﾙﾚﾀｽ',
            'export_target_flag' => '1',
            'net_weight' => '240.000',
            'gross_weight' => '250.000',
            'depth' => '100.00',
            'width' => '80.00',
            'height' => '40.00',
            'country_of_origin' => 'JP',
            'remark' => 'テストテスト',
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }
}
