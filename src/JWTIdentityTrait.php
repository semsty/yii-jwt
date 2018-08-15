<?php

namespace common\core\traits;

use \Firebase\JWT\JWT;
use yii\web\NotFoundHttpException;

trait JWTIdentityTrait
{
    public static $id_key = 'uid';

    public static $decodedToken;

    protected static function getSecretKey()
    {
        return \Yii::$app->params['jwt_key'];
    }

    public static function getJWTId($token)
    {
        $secret = static::getSecretKey();
        try {
            $decoded = JWT::decode($token, $secret, [static::getAlg()]);
        } catch (\Exception $e) {
            return false;
        }
        static::$decodedToken = (array)$decoded;
        if (!isset(static::$decodedToken[static::$id_key])) {
            return false;
        }
        $id = static::$decodedToken[static::$id_key];
        return $id;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (empty($token)) {
            throw new NotFoundHttpException('empty token');
        }
        $id = static::getJWTId($token);
        if (!$id) {
            return false;
        }
        return static::findOne($id);
    }

    public static function getAlg()
    {
        return 'HS256';
    }

    public function getJTI()
    {
        return $this->getId();
    }

    public function getJWT()
    {
        $secret = static::getSecretKey();
        $currentTime = time();
        $token = [
            'iat' => $currentTime,
            static::$id_key =>  $this->getJTI()
        ];
        return JWT::encode($token, $secret, static::getAlg());
    }
}