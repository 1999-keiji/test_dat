<?php

namespace Tests\Unit\Http\Requests\Master;

use Mockery as m;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\Master\CreateProductRequest;
use App\Models\Master\Product;
use App\Models\Master\Species;
use App\ValueObjects\Decimal\ProductSize;
use App\ValueObjects\Decimal\ProductWeight;
use App\ValueObjects\Enum\ProductClass;
use App\ValueObjects\Integer\SalesOrderUnitQuantity;
use App\ValueObjects\String\CategoryCode;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\ProductCode;

class CreateProductRequestTest extends TestCase
{
    private $create_product_request;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $product = m::mock(Product::class);
        $product->shouldReceive('getTable')
            ->andReturn('products');
        $product->shouldReceive('getWillCastAsBoolean')
            ->andReturn([
                'custom_product_flag',
                'lot_target_flag',
                'export_target_flag'
            ]);

        $species = m::mock(Species::class);
        $species->shouldReceive('getTable')
            ->andReturn('species');

        $this->create_product_request = new CreateProductRequest(
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
    public function 商品登録用パラメータのバリデーション()
    {
        $params = $this->getValidParams();
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        unset($params['product_code']);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_code'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_code'] = 'OLT0001';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_code'] = str_repeat('A', 15);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['product_code'] = str_repeat('A', 16);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_code'] = 'OLT-0002';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        unset($params['species_code']);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['species_code'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['species_code'] = '0001FL';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_name'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_name'] = str_repeat('A', 40);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['product_name'] = str_repeat('A', 41);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['result_addup_code'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['result_addup_code'] = str_repeat('A', 10);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['result_addup_code'] = str_repeat('A', 11);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['result_addup_code'] = 'A1B2C3';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['result_addup_name'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['result_addup_name'] = str_repeat('A', 30);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['result_addup_name'] = str_repeat('A', 31);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['result_addup_abbreviation'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['result_addup_abbreviation'] = str_repeat('A', 10);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['result_addup_abbreviation'] = str_repeat('A', 11);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_large_category'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['product_large_category'] = str_repeat('A', 2);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_large_category'] = str_repeat('A', 3);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['product_large_category'] = str_repeat('A', 4);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_large_category'] = 'olt';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_middle_category'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['product_middle_category'] = str_repeat('A', 2);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_middle_category'] = str_repeat('A', 3);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['product_middle_category'] = str_repeat('A', 4);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_middle_category'] = 'o1t';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_class'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['product_class'] = '2';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['product_class'] = '3';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['custom_product_flag'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['custom_product_flag'] = '2';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['sales_order_unit'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['sales_order_unit'] = str_repeat('A', 3);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['sales_order_unit'] = str_repeat('A', 4);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['sales_order_unit_quantity'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['sales_order_unit_quantity'] = 'A';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['sales_order_unit_quantity'] = '-1';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['sales_order_unit_quantity'] = '0';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['sales_order_unit_quantity'] = '999999999';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['sales_order_unit_quantity'] = '1000000000';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['minimum_sales_order_unit_quantity'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['minimum_sales_order_unit_quantity'] = 'A';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['minimum_sales_order_unit_quantity'] = '-1';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['minimum_sales_order_unit_quantity'] = '0';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['minimum_sales_order_unit_quantity'] = '999999999';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['minimum_sales_order_unit_quantity'] = '1000000000';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['statement_of_delivery_name'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['statement_of_delivery_name'] = str_repeat('A', 50);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['statement_of_delivery_name'] = str_repeat('A', 51);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['pickup_slip_message'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['pickup_slip_message'] = str_repeat('A', 40);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['pickup_slip_message'] = str_repeat('A', 41);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['lot_target_flag'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['lot_target_flag'] = '2';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['species_name'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['species_name'] = str_repeat('A', 25);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['species_name'] = str_repeat('A', 26);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['export_target_flag'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['export_target_flag'] = '2';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['net_weight'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['net_weight'] = 'A';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['net_weight'] = '-1';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['net_weight'] = '0';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['net_weight'] = '999999.999';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['net_weight'] = '1000000';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['net_weight'] = '0.0001';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['gross_weight'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['gross_weight'] = 'A';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['gross_weight'] = '-1';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['gross_weight'] = '0';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['gross_weight'] = '999999.999';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['gross_weight'] = '1000000';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['gross_weight'] = '0.0001';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['depth'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['depth'] = 'A';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['depth'] = '-1';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['depth'] = '0';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['depth'] = '99999.99';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['depth'] = '100000';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['depth'] = '0.001';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['width'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['width'] = 'A';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['width'] = '-1';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['width'] = '0';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['width'] = '99999.99';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['width'] = '100000';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['width'] = '0.001';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['height'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['height'] = 'A';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['height'] = '-1';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['height'] = '0';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['height'] = '99999.99';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['height'] = '100000';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['height'] = '0.001';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['country_of_origin'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['country_of_origin'] = str_repeat('A', 1);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['country_of_origin'] = str_repeat('A', 2);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['country_of_origin'] = str_repeat('A', 3);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['country_of_origin'] = 'jp';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['remark'] = '';
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['remark'] = str_repeat('A', 255);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        $params['remark'] = str_repeat('A', 256);
        $validator = Validator::make($params, $this->create_product_request->rules());
        $this->assertFalse($validator->passes());

        $this->assertTrue($this->create_product_request->authorize());

        $this->assertEquals(['species_code' => '品種'], $this->create_product_request->attributes());
    }

    /**
     * @return array
     */
    private function getValidParams()
    {
        return [
            'product_code' => 'OLT0002',
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
            'remark' => 'テストテスト'
        ];
    }
}
