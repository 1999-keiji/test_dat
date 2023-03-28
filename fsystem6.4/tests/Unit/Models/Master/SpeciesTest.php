<?php

namespace Tests\Unit\Models\Master;

use Tests\TestCase;
use App\Models\Master\Collections\SpeciesCollection;
use App\Models\Master\Species;

class SpeciesTest extends TestCase
{
    /**
     * @test
     */
    public function allメソッドの返り値の型が正しい()
    {
        $this->assertInstanceOf(SpeciesCollection::class, Species::all());
    }

    /**
     * @test
     */
    public function view用のJSON文字列オプションに変更する()
    {
        $this->assertEquals(
            '[{"label":"\u30d5\u30ea\u30eb\u30ec\u30bf\u30b9","value":"0001-FL"},{"label":"\u30b0\u30ea\u30fc\u30f3\u30ea\u30fc\u30d5","value":"0002-GL"},{"label":"\u30d0\u30b8\u30eb","value":"0003-BA"}]',
            Species::all()->toJsonOptions()
        );
    }
}
