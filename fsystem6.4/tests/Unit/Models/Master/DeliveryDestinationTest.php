<?php

namespace Tests\Unit\Models\Master;

use Tests\TestCase;
use App\Models\Master\Collections\DeliveryDestinationCollection;
use App\Models\Master\DeliveryDestination;

class DeliveryDestinationTest extends TestCase
{
    /**
     * @test
     */
    public function allメソッドの返り値の型が正しい()
    {
        $this->assertInstanceOf(DeliveryDestinationCollection::class, DeliveryDestination::all());
    }

    /**
     * @test
     */
    public function view用のJSON文字列オプションに変更する()
    {
        $this->assertEquals(
            [
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
            ],
            DeliveryDestination::all()->toResponseForSearchingApi()
        );
    }
}
