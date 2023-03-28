<?php

namespace Tests\Unit\Http\Requests\Master;

use Mockery as m;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\Master\SearchProductsRequest;
use App\Models\Master\Species;
use App\ValueObjects\String\ProductCode;

class SearchProductsRequestTest extends TestCase
{
    private $search_products_request;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $species = m::mock(Species::class);
        $species->shouldReceive('getTable')
            ->andReturn('species');

        $this->search_products_request = new SearchProductsRequest($species, new ProductCode());
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
    public function 商品検索用パラメータのバリデーション()
    {
        $params = ['species_code' => '0001-FL'];
        $validator = Validator::make($params, $this->search_products_request->rules());
        $this->assertTrue($validator->passes());

        $params = ['species_code' => '0002-FL'];
        $validator = Validator::make($params, $this->search_products_request->rules());
        $this->assertFalse($validator->passes());

        $params = ['product_code' => 'OLT0001'];
        $validator = Validator::make($params, $this->search_products_request->rules());
        $this->assertTrue($validator->passes());

        $params = ['product_code' => str_repeat('A', 16)];
        $validator = Validator::make($params, $this->search_products_request->rules());
        $this->assertFalse($validator->passes());

        $params = ['product_code' => 'OLT-0001'];
        $validator = Validator::make($params, $this->search_products_request->rules());
        $this->assertFalse($validator->passes());

        $params = ['product_name' => str_repeat('A', 40)];
        $validator = Validator::make($params, $this->search_products_request->rules());
        $this->assertTrue($validator->passes());

        $params = ['product_name' => str_repeat('A', 41)];
        $validator = Validator::make($params, $this->search_products_request->rules());
        $this->assertFalse($validator->passes());

        $this->assertTrue($this->search_products_request->authorize());

        $this->assertEquals(['species_code' => '品種'], $this->search_products_request->attributes());
    }
}
