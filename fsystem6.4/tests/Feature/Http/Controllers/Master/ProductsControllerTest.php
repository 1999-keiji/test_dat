<?php

namespace Tests\Feature\Http\Controllers\Master;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Exceptions\PageOverException;
use App\Models\Master\Product;
use App\Models\Master\User;

class ProductsControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $vgen_user;

    private $vvf_user;

    private $factory_user;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->vgen_user = User::find('hashimoto');
        $this->vvf_user = User::find('takita');
        $this->factory_user = User::find('chishima');
    }

    /**
     * @return void
     */
    public function testIndex()
    {
        $response = $this->actingAs($this->vgen_user)
            ->get('/master/products');
        $response->assertSuccessful();
        $response->assertViewHasAll(['products', 'species']);

        $response = $this->actingAs($this->vgen_user)
            ->withSession(['master' => [
                'products' => [
                    'search' => [
                        'species_code' => null,
                        'product_code' => null,
                        'product_name' => null
                    ]
                ]
            ]])
            ->get('/master/products?page=1');
        $response->assertSuccessful();

        $response = $this->actingAs($this->vgen_user)
            ->withSession(['master' => [
                'products' => [
                    'search' => [
                        'species_code' => null,
                        'product_code' => null,
                        'product_name' => null
                    ]
                ]
            ]])
            ->get('/master/products?page=2');
        $response->assertRedirect('/master/products');

        $response = $this->actingAs($this->vvf_user)
            ->get('/master/products');
        $response->assertSuccessful();
        $response->assertDontSee('追加');
        $response->assertDontSee('削除');

        $response = $this->actingAs($this->factory_user)
            ->get('/master/products');
        $response->assertStatus(302);
    }

    /**
     * @return void
     */
    public function testSearch()
    {
        $params = [
            'species_name' => '0001-FL',
            'product_code' => 'OLT0001',
            'product_name' => 'ﾌﾘﾙﾚﾀｽ 70g 12入',
        ];

        $response = $this->actingAs($this->vgen_user)
            ->post('/master/products/search', $params);
        $response->assertRedirect('/master/products');
        $response->assertSessionHas('master.products.search', $params);

        $response = $this->actingAs($this->vvf_user)
            ->post('/master/products/search', $params);
        $response->assertRedirect('/master/products');
        $response->assertSessionHas('master.products.search', $params);

        $response = $this->actingAs($this->factory_user)
            ->post('/master/products/search', $params);
        $response->assertStatus(302);
    }

    /**
     * @return void
     */
    public function testAdd()
    {
        $response = $this->actingAs($this->vgen_user)
            ->get('/master/products/add');
        $response->assertSuccessful();
        $response->assertViewHasAll([
            'product_code',
            'creating_type',
            'species',
            'category_code',
            'product_class_list',
            'sales_order_unit_quantity',
            'product_weight',
            'product_size',
            'country_code'
        ]);

        $response = $this->actingAs($this->vvf_user)
            ->get('/master/products/add');
        $response->assertStatus(302);

        $response = $this->actingAs($this->factory_user)
            ->get('/master/products/add');
        $response->assertStatus(302);
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $params = [
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

        $response = $this->actingAs($this->vgen_user)
            ->post('/master/products', $params);
        $response->assertRedirect('/master/products/OLT0002');
        $this->assertDatabaseHas('products', $params);

        $response = $this->actingAs($this->vvf_user)
            ->post('/master/products', $params);
        $response->assertStatus(302);

        $response = $this->actingAs($this->factory_user)
            ->post('/master/products', $params);
        $response->assertStatus(302);
    }

    /**
     * @return void
     */
    public function testEdit()
    {
        $response = $this->actingAs($this->vgen_user)
            ->get('/master/products/OLT0001');
        $response->assertSuccessful();
        $response->assertViewHasAll([
            'product',
            'species',
            'category_code',
            'product_class_list',
            'sales_order_unit_quantity',
            'product_weight',
            'product_size',
            'country_code'
        ]);

        $response = $this->actingAs($this->vvf_user)
            ->get('/master/products/OLT0001');
        $response->assertSuccessful();
        $response->assertDontSee('保存');

        $response = $this->actingAs($this->factory_user)
            ->get('/master/products/OLT0001');
        $response->assertStatus(302);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $product = Product::find('OLT0001');
        $params = [
            'creating_type' => $product->creating_type,
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
            'updated_at' => $product->updated_at->format('Y-m-d H:i:s')
        ];

        $response = $this->actingAs($this->vgen_user)
            ->patch('/master/products/OLT0001', $params);
        $response->assertRedirect('/master/products/OLT0001');
        $this->assertDatabaseMissing('products', array_except($params, ['updated_at']));

        $product = Product::find('OLT0049');
        $params['creating_type'] = $product->creating_type;
        $params['updated_at'] = $product->updated_at->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->vgen_user)
            ->patch('/master/products/OLT0049', $params);
        $response->assertRedirect('/master/products/OLT0049');
        $this->assertDatabaseHas('products', array_except($params, ['updated_at']));

        $response = $this->actingAs($this->vvf_user)
            ->patch('/master/products/OLT0001', $params);
        $response->assertStatus(302);

        $response = $this->actingAs($this->factory_user)
            ->patch('/master/products/OLT0001', $params);
        $response->assertStatus(302);
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        $response = $this->actingAs($this->vgen_user)
            ->delete('/master/products/OLT0001');
        $this->assertDatabaseHas('products', ['product_code' => 'OLT0001']);

        $response = $this->actingAs($this->vgen_user)
            ->delete('/master/products/OLT0049');
        $this->assertDatabaseMissing('products', ['product_code' => 'OLT0049']);

        $response = $this->actingAs($this->vvf_user)
            ->delete('/master/products/OLT0001');
        $response->assertStatus(302);

        $response = $this->actingAs($this->factory_user)
            ->delete('/master/products/OLT0001');
        $response->assertStatus(302);
    }
}
