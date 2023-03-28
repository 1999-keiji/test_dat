<?php

namespace Tests\Unit\Http\Requests\Master;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\LoginRequest;

class LoginRequestTest extends TestCase
{
    private $login_request;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->login_request = new LoginRequest();
    }

    /**
     * @test
     */
    public function ログイン用パラメータのバリデーション()
    {
        $params = $this->getValidParams();
        $validator = Validator::make($params, $this->login_request->rules());
        $this->assertTrue($validator->passes());

        $params = $this->getValidParams();
        unset($params['user_code']);
        $validator = Validator::make($params, $this->login_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['user_code'] = null;
        $validator = Validator::make($params, $this->login_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        unset($params['password']);
        $validator = Validator::make($params, $this->login_request->rules());
        $this->assertFalse($validator->passes());

        $params = $this->getValidParams();
        $params['password'] = '';
        $validator = Validator::make($params, $this->login_request->rules());
        $this->assertFalse($validator->passes());

        $this->assertTrue($this->login_request->authorize());
    }

    /**
     * @return array
     */
    private function getValidParams()
    {
        return [
            'user_code' => 'chishima',
            'password' => bcrypt('chishima.03')
        ];
    }
}
