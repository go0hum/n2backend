<?php 

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Authorization 
{
    private $key = "serviciosCancun";

    public function init()
    {
        global $textApi, $lang;
        
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            Json(401, "error", $textApi[$lang]['unauthorized'], "INVALID_TOKEN");
        }
        
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        list($type, $token) = explode(' ', $authHeader);
        
        if ($type !== 'Bearer' || empty($token)) {
            Json(401, "error", $textApi[$lang]['unauthorized'], "INVALID_TOKEN");
        }
        
        if (!$this->verificarToken($token)) {
            Json(401, "error", $textApi[$lang]['invalid_token'], "INVALID_TOKEN");
        }
    }

    protected function verificarToken($token) 
    {
        try {
            JWT::decode($token, new Key($this->key, 'HS256'));
            return true; 
        } catch (Exception $e) {
            return false; 
        }
    }

    public function getUserInfo()
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        list($type, $token) = explode(' ', $authHeader);
        return JWT::decode($token, new Key($this->key, 'HS256'));
    }

    public function getToken($userId, $data)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;  
        $payload = array(
            'username' => $data->username,
            'user_id' => $userId,
            'iat' => $issuedAt,
            'exp' => $expirationTime
        );

        return JWT::encode($payload, $this->key, 'HS256');
    }
}