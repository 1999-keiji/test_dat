<?php

namespace Tests\Unit\ValueObjects\Enum;

use Tests\TestCase;
use App\ValueObjects\Enum\Enum;

class EnumTest extends TestCase
{
    private $enum;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->enum = $this->getMockForAbstractClass(Enum::class);
    }

    /**
     * @test
     * @expectedException BadMethodCallException
     */
    public function __setメソッドを利用することで例外が発生する()
    {
        $this->enum->hoge = 'fuga';
    }
}
