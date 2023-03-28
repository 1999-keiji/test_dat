<?php

namespace Tests\Unit\ValueObjects\String;

use Tests\TestCase;
use App\ValueObjects\String\MasterCode;

class MasterCodeTest extends TestCase
{
    private $master_code;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->master_code = $this->getMockForAbstractClass(MasterCode::class);
    }

    /**
     * @test
     */
    public function 最小文字数を取得する()
    {
        $this->assertEquals(1, $this->master_code->getMinLength());
    }

    /**
     * @test
     */
    public function 最大文字数を取得する()
    {
        $this->assertEquals(15, $this->master_code->getMaxLength());
    }

    /**
     * @test
     */
    public function 正規表現を取得する()
    {
        $this->assertEquals("/\A[a-zA-Z0-9_-]+\z/", $this->master_code->getRegexPattern());
    }

    /**
     * @test
     */
    public function ヘルプテキストを取得する()
    {
        $this->assertEquals(
            "1文字以上15文字以内の\n半角英数字、ハイフン、\nアンダーバーが入力できます。",
            $this->master_code->getHelpText()
        );
    }

    /**
     * @test
     * @expectedException BadMethodCallException
     */
    public function __setメソッドを利用することで例外が発生する()
    {
        $this->master_code->hoge = 'fuga';
    }
}
