<?php

namespace Tests\Unit\Extension;

use Mockery as m;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class HelpersTest extends TestCase
{
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
    public function is_not_nullのテスト()
    {
        $this->assertTrue(is_not_null(''));
        $this->assertTrue(is_not_null(0));
        $this->assertTrue(is_not_null(false));
        $this->assertFalse(is_not_null(null));
    }

    /**
     * @test
     */
    public function subtract_category_from_pathのテスト()
    {
        $this->assertEquals('products', subtract_category_from_path('master/products'));
        $this->assertEquals('products', subtract_category_from_path('/master/products'));
    }

    /**
     * @test
     */
    public function route_relativelyのテスト()
    {
        $this->assertEquals('/master/products/add', route_relatively('master.products.add'));
        $this->assertEquals('/master/products/OLT0001', route_relatively('master.products.edit', 'OLT0001'));
    }

    /**
     * @test
     */
    public function has_errorのテスト()
    {
        $this->assertEquals('', has_error('hoge'));

        $error_bag = m::mock(ViewErrorBag::class);
        $error_bag->shouldReceive('has')
            ->andReturn(true, false);

        $this->session(['errors' => $error_bag]);

        $this->assertEquals('has-error', has_error('hoge'));
        $this->assertEquals('', has_error('fuga'));
    }

    /**
     * @test
     */
    public function is_selectedのテスト()
    {
        $this->assertEquals('selected', is_selected(1, 1));
        $this->assertEquals('selected', is_selected('2', '2'));
        $this->assertEquals('selected', is_selected(3, '3'));

        $this->assertEquals('', is_selected(1, 2));
        $this->assertEquals('', is_selected('', '0'));
        $this->assertEquals('', is_selected(null, '0'));
    }

    /**
     * @test
     */
    public function is_checkedのテスト()
    {
        $this->assertEquals('checked', is_checked(1, 1));
        $this->assertEquals('checked', is_checked('2', '2'));
        $this->assertEquals('checked', is_checked(3, '3'));
        $this->assertEquals('checked', is_checked(1, true));
        $this->assertEquals('checked', is_checked(0, false));

        $this->assertEquals('', is_checked(1, 2));
        $this->assertEquals('', is_checked('', '0'));
        $this->assertEquals('', is_checked(null, '0'));
        $this->assertEquals('', is_checked(0, true));
        $this->assertEquals('', is_checked(1, false));
    }

    /**
     * @test
     */
    public function replace_elのテスト()
    {
        $this->assertEquals('hoge<br>fuga', replace_el("hoge\nfuga"));
        $this->assertEquals('hoge<br>fuga', replace_el("hoge\r\nfuga"));
        $this->assertEquals('hoge<br>fuga<br>piyo', replace_el("hoge\nfuga\r\npiyo"));
    }
}
