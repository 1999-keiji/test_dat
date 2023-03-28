<?php

namespace Tests\Unit\Http\Requests;

use ReflectionClass;
use Tests\TestCase;
use App\Http\Requests\FormRequest;

class FormRequestTest extends TestCase
{
    /**
     * @return void
     */
    public function testAllMethod()
    {
        $request = FormRequest::create('/', 'GET', ['name' => 'Taylor', 'age' => null]);
        $this->assertEquals(['name' => 'Taylor', 'age' => null, 'email' => null], $request->all('name', 'age', 'email'));
        $this->assertEquals(['name' => 'Taylor'], $request->all('name'));
        $this->assertEquals(['name' => 'Taylor', 'age' => null], $request->all());

        $request = FormRequest::create('/', 'GET', ['developer' => ['name' => 'Taylor', 'age' => null]]);
        $this->assertEquals(['developer' => ['name' => 'Taylor', 'skills' => null]], $request->all('developer.name', 'developer.skills'));
        $this->assertEquals(['developer' => ['name' => 'Taylor', 'skills' => null]], $request->all(['developer.name', 'developer.skills']));
        $this->assertEquals(['developer' => ['age' => null]], $request->all('developer.age'));
        $this->assertEquals(['developer' => ['skills' => null]], $request->all('developer.skills'));
        $this->assertEquals(['developer' => ['name' => 'Taylor', 'age' => null]], $request->all());

        $request = FormRequest::create('/', 'POST', ['hoge' => 'fuga', '_token' => csrf_token(), '_method' => 'POST']);
        $this->assertEquals(['hoge' => 'fuga'], $request->all());

        $request = FormRequest::create('/', 'POST', ['hoge' => 'fuga']);
        $request->on_off_checkboxes = ['foo'];
        $this->assertEquals(0, $request->input('foo'));
    }

    /**
     * @return void
     */
    public function testReservedRules()
    {
        $expected = [];
        foreach (range(1, 10) as $idx) {
            $expected["reserved_text{$idx}"] = ['bail', 'nullable', 'string', 'max:200'];
            $expected["reserved_number{$idx}"] = ['bail', 'nullable', "regex:/\A([-]?[1-9][0-9]{0,13}|0)(\.[0-9]{1,5})?\z/", 'min:-999999999999.99999', 'max:9999999999999.99999'];
        }

        $request = new FormRequest();
        $reflection = new ReflectionClass($request);

        $method = $reflection->getMethod('reservedRules');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invoke($request));
    }
}
