<?php

namespace semsty\jwt\tests;

use Firebase\JWT\JWT;

class JWTApiAuthTest extends TestCase
{
    protected function getJWTByData($data)
    {
        return JWT::encode($data, \Yii::$app->params['jwt_key']);
    }
    
    public function testAuthWithEmptyToken()
    {
        $jwt = '';

        try {
            User::findIdentityByAccessToken($jwt);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'empty token');
        }
    }

    public function testAuthWithInvalidToken()
    {
        $jwt = $this->getJWTByData(['uid' => 666]);
        $jwt_user = User::findIdentityByAccessToken($jwt);
        $this->assertEmpty($jwt_user);
    }

    public function testAuthWithValidToken()
    {
        $jwt = $this->getJWTByData(['uid' => 1]);
        $jwt_user = User::findIdentityByAccessToken($jwt);
        $this->assertNotEmpty($jwt_user);
    }
}