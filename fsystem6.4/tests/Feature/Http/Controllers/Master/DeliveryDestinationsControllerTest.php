<?php

namespace Tests\Feature\Http\Controllers\Master;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeliveryDestinationsControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testSearchDeliveryDestinations()
    {
        $response = $this->get('/api/search-delivery-destinations');
        $response->assertStatus(404);

        $response = $this->json('GET', '/api/search-delivery-destinations', [], ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertExactJson([
                [
                    "code" => "1200400J01",
                    "name" => "サトー商会",
                    "address" => "宮城県仙台市宮城野区扇町5-6-22",
                    "phone_number" => "022-236-5600",
                ],
                [
                    "code" => "HANE000037",
                    "name" => "阪栄フーズ",
                    "address" => "福岡県福岡市中央区那の津3-9-8",
                    "phone_number" => "092-401-1530",
                ],
        ]);

        $response = $this->json('GET', '/api/search-delivery-destinations', ['master_code' => '1200400J01'], ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertExactJson([
            [
                "code" => "1200400J01",
                "name" => "サトー商会",
                "address" => "宮城県仙台市宮城野区扇町5-6-22",
                "phone_number" => "022-236-5600",
            ]
        ]);

        $response = $this->json('GET', '/api/search-delivery-destinations', ['master_name' => '阪栄'], ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertExactJson([
            [
                "code" => "HANE000037",
                "name" => "阪栄フーズ",
                "address" => "福岡県福岡市中央区那の津3-9-8",
                "phone_number" => "092-401-1530",
            ]
        ]);
    }
}
