<?php

namespace common\components;

use yii\filters\auth\HttpBearerAuth;

class JWTAuth extends HttpBearerAuth
{
    public $access_key = 'access_token';

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $cookie = $_COOKIE;
        if ($access = $cookie[$this->access_key]) {
            $identity = $user->loginByAccessToken($access);
            if ($identity === null) {
                $this->handleFailure($response);
            }
            \Yii::$app->user->identity = $identity;
            return $identity;
        } else {
            $this->handleFailure($this->access_key);
        }
    }
}