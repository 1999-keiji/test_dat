<?php

namespace Tests\Unit\Extension;

use Mockery as m;
use Tests\TestCase;
use App\Extension\Validator;

class ValidatorTest extends TestCase
{
    public function testValidateAlpha()
    {
        $trans = $this->getIlluminateArrayTranslator();
        $v = new Validator($trans, ['x' => 'aslsdlks'], ['x' => 'alpha']);
        $this->assertTrue($v->passes());

        $v = new Validator($trans, ['x' => 'ａｓｌｓｄｌｋｓ'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $trans = $this->getIlluminateArrayTranslator();
        $v = new Validator($trans, ['x' => 'aslsdlks

1
1'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'http://google.com'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'ユニコードを基盤技術と'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'ユニコード を基盤技術と'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'नमस्कार'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'आपका स्वागत है'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'Continuación'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'ofreció su dimisión'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => '❤'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => '123'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 123], ['x' => 'alpha']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'abc123'], ['x' => 'alpha']);
        $this->assertFalse($v->passes());
    }

    public function testValidateAlphaNum()
    {
        $trans = $this->getIlluminateArrayTranslator();
        $v = new Validator($trans, ['x' => 'asls13dlks'], ['x' => 'alpha_num']);
        $this->assertTrue($v->passes());

        $v = new Validator($trans, ['x' => 'ａｓｌｓ１３ｄｌｋｓ'], ['x' => 'alpha_num']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'http://g232oogle.com'], ['x' => 'alpha_num']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => '१२३'], ['x' => 'alpha_num']); // numbers in Hindi
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => '٧٨٩'], ['x' => 'alpha_num']); // eastern arabic numerals
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'नमस्कार'], ['x' => 'alpha_num']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => null], ['x' => 'alpha_num']);
        $this->assertFalse($v->passes());
    }

    public function testValidateAlphaDash()
    {
        $trans = $this->getIlluminateArrayTranslator();
        $v = new Validator($trans, ['x' => 'asls1-_3dlks'], ['x' => 'alpha_dash']);
        $this->assertTrue($v->passes());

        $trans = $this->getIlluminateArrayTranslator();
        $v = new Validator($trans, ['x' => 'ａｓｌｓ１－＿３ｄｌｋｓ'], ['x' => 'alpha_dash']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'http://-g232oogle.com'], ['x' => 'alpha_dash']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => 'नमस्कार-_'], ['x' => 'alpha_dash']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => '٧٨٩'], ['x' => 'alpha_dash']); // eastern arabic numerals
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['x' => null], ['x' => 'alpha_dash']); // eastern arabic numerals
        $this->assertFalse($v->passes());
    }

    public function testValidateImplicitEachWithAsterisksRequired()
    {
        $trans = $this->getIlluminateArrayTranslator();
        $v = new Validator($trans, ['foo' => null], ['foo' => 'required']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => '', 'bar' => ' '], ['foo' => 'required', 'bar' => 'required']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => '　'], ['foo' => 'required']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => [], 'bar' => collect([])], ['foo' => 'required', 'bar' => 'required']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => 'hoge'], ['foo' => 'required']);
        $this->assertTrue($v->passes());
    }

    public function testValidatePositiveInt()
    {
        $trans = $this->getIlluminateArrayTranslator();
        $v = new Validator($trans, ['foo' => null], ['foo' => 'positive_int']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => 0], ['foo' => 'positive_int']);
        $this->assertTrue($v->passes());

        $v = new Validator($trans, ['foo' => '1'], ['foo' => 'positive_int']);
        $this->assertTrue($v->passes());

        $v = new Validator($trans, ['foo' => -1], ['foo' => 'positive_int']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => '-2'], ['foo' => 'positive_int']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => []], ['foo' => 'positive_int']);
        $this->assertFalse($v->passes());
    }

    public function testValidateZenkana()
    {
        $trans = $this->getIlluminateArrayTranslator();
        $v = new Validator($trans, ['foo' => null], ['foo' => 'zenkana']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => 'ホゲホゲー　フガフガ・ピヨピヨ'], ['foo' => 'zenkana']);
        $this->assertTrue($v->passes());

        $v = new Validator($trans, ['foo' => 'ﾎｹﾞﾎｹﾞ ﾌｶﾞﾌｶﾞ'], ['foo' => 'zenkana']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => 'ほげほげ　ふがふが'], ['foo' => 'zenkana']);
        $this->assertFalse($v->passes());
    }

    public function testValidateHankana()
    {
        $trans = $this->getIlluminateArrayTranslator();
        $v = new Validator($trans, ['foo' => null], ['foo' => 'hankana']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => 'ﾎｹﾞﾎｹﾞｰ ﾌｶﾞﾌｶﾞ･ﾋﾟﾖﾋﾟﾖ'], ['foo' => 'hankana']);
        $this->assertTrue($v->passes());

        $v = new Validator($trans, ['foo' => 'ホゲホゲ　フガフガ'], ['foo' => 'hankana']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => 'ほげほげ　ふがふが'], ['foo' => 'hankana']);
        $this->assertFalse($v->passes());
    }

    public function testValidateZenkaku()
    {
        $trans = $this->getIlluminateArrayTranslator();
        $v = new Validator($trans, ['foo' => null], ['foo' => 'zenkaku']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => 'ホゲホゲー　フガフガ・ピヨピヨ！？'], ['foo' => 'zenkaku']);
        $this->assertTrue($v->passes());

        $v = new Validator($trans, ['foo' => 'ﾎｹﾞﾎｹﾞ ﾌｶﾞﾌｶﾞ'], ['foo' => 'zenkana']);
        $this->assertFalse($v->passes());
    }

    public function testValidateHankaku()
    {
        $trans = $this->getIlluminateArrayTranslator();
        $v = new Validator($trans, ['foo' => null], ['foo' => 'hankaku']);
        $this->assertFalse($v->passes());

        $v = new Validator($trans, ['foo' => 'ﾎｹﾞﾎｹﾞｰ ﾌｶﾞﾌｶﾞ･ﾋﾟﾖﾋﾟﾖ!?'], ['foo' => 'hankaku']);
        $this->assertTrue($v->passes());

        $v = new Validator($trans, ['foo' => 'ホゲホゲ　フガフガ'], ['foo' => 'hankaku']);
        $this->assertFalse($v->passes());
    }

    public function getIlluminateArrayTranslator()
    {
        return new \Illuminate\Translation\Translator(
            new \Illuminate\Translation\ArrayLoader, 'ja'
        );
    }
}
