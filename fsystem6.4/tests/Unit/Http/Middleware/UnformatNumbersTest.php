<?php

namespace Tests\Unit\Http\Middleware;

use Illuminate\Http\Request;
use Tests\TestCase;
use App\Http\Middleware\UnformatNumbers;

class UnformatNumbersTest extends TestCase
{
    /**
     * @test
     */
    public function リクエストパラメータからカンマが除去される()
    {
        $middleware = new UnformatNumbers();
        $request = new Request([], [
            'sales_order_unit_quantity' => '1,000,000',
            'net_weight' => '2,000.555',
            'dummy1' => '3,000',
            'dummy2' => ['4,000', '5,000'],
        ]);

        $middleware->handle($request, function (Request $request) {
            $this->assertEquals('1000000', $request->get('sales_order_unit_quantity'));
            $this->assertEquals('2000.555', $request->get('net_weight'));
            $this->assertEquals('3,000', $request->get('dummy1'));
            $this->assertEquals(['4,000', '5,000'], $request->get('dummy2'));
        });
    }
}
