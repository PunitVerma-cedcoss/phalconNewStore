<?php

namespace App\Components;

use Phalcon\Di\Injectable;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Helper class to manage tokens
 * init(role,datetimeObject,userFirebase:bool)
 * validate(token)
 */
class JwtInit extends Injectable
{
    /**
     * return a jwt token based on role
     *
     * @param [string] $role
     * @param [datetime object] $now
     * @param boolean $useFireBaseJWT
     * @return string
     */
    public function init($role, $now, $useFireBaseJWT = false)
    {
        if (!$useFireBaseJWT) {
            // Defaults to 'sha512'
            $signer  = new Hmac();

            // Builder object
            $builder = new Builder($signer);

            $now        = $now;
            $issued     = $now->getTimestamp();
            $notBefore  = $now->modify('-1 minute')->getTimestamp();
            $expires    = $now->modify('+1 day')->getTimestamp();
            $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

            // Setup
            $builder
                ->setAudience('https://target.phalcon.io')  // aud
                ->setContentType('application/json')        // cty - header
                ->setExpirationTime($expires)               // exp 
                ->setId('abcd123456789')                    // JTI id 
                ->setIssuedAt($issued)                      // iat 
                ->setIssuer('https://phalcon.io')           // iss 
                ->setNotBefore($notBefore)                  // nbf
                ->setSubject($role)   // sub
                ->setPassphrase($passphrase)                // password 
            ;
            $tokenObject = $builder->getToken();

            echo $tokenObject->getToken();
            // The token
            return $tokenObject->getToken();
        } else {
            $now = $this->datetime;
            $key = "QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2";
            $payload = array(
                "iss" => "http://example.org",
                "aud" => "http://example.com",
                "email" => "admin@store.com",
                "password" => "12345",
                "sub" => "admin",
                "iat" => $now->getTimestamp(),
                "nbf" => $now->modify('-1 minute')->getTimestamp(),
                "exp" => $now->modify('+1 day')->getTimestamp()
            );
            $jwt = JWT::encode($payload, $key, 'HS512');
            $decoded = JWT::decode($jwt, new Key($key, 'HS512'));
            return $jwt;
        }
    }
    /**
     * validates a string jwt token
     *
     * @param [string] $tokenReceived
     * @return void
     */
    public function jwtValidate($tokenReceived)
    {
        $resp = '';
        try {
            $audience      = 'https://target.phalcon.io';
            $now           = $this->datetime;
            $issued        = $now->getTimestamp();
            $notBefore     = $now->modify('-1 minute')->getTimestamp();
            $expires       = $now->getTimestamp();
            $id            = 'abcd123456789';
            $issuer        = 'https://phalcon.io';

            $signer     = new Hmac();
            $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

            // Parse the token
            $parser      = new Parser();
            $tokenObject = $parser->parse($tokenReceived);
            $resp = $tokenObject->getClaims()->getPayload()['sub'];
            // Phalcon\Security\JWT\Validator object
            $validator = new Validator($tokenObject, 0);
            $validator
                ->validateExpiration($expires);
        } catch (\Exception $e) {
            echo "<pre>";
            echo "</pre>";
            echo $e->getMessage();
            $resp = "error";
            echo "<br>";
            die();
        }
        return $resp;
    }
    /**
     * validates a firebase jwt token
     *
     * @param [string] $tokenReceived
     * @return void
     */
    public function firebaseJwtValidate($tokenReceived)
    {
        try {
            $key = "QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2";
            $decoded = JWT::decode($tokenReceived, new Key($key, 'HS512'));
            if ($this->datetime->getTimestamp() < $decoded->exp) {
                if (isset($decoded->sub)) {
                } else {
                    die();
                }
            } else {
                $lang  = $this->request->getquery()['locale'] ?? 'en';
                echo "token has expired";
            }
        } catch (\Exception $e) {
            $lang  = $this->request->getquery()['locale'] ?? 'en';
            echo $e->getMessage();
            die();
        }
    }
}
