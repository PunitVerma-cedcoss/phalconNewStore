<?php

namespace App\Components;

use Exception;
use Phalcon\Di\Injectable;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtInit extends Injectable
{
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

            // Phalcon\Security\JWT\Token\Token object
            $tokenObject = $builder->getToken();

            echo $tokenObject->getToken();
            // die();

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
            // print_r($jwt);
            return $jwt;
        }
    }
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

            // Phalcon\Security\JWT\Token\Token object
            $tokenObject = $parser->parse($tokenReceived);

            // echo "<pre>";
            // print_r($tokenObject);
            // echo "</pre>";
            // echo $tokenObject->getClaims()->getPayload()['sub'];
            $resp = $tokenObject->getClaims()->getPayload()['sub'];
            // Phalcon\Security\JWT\Validator object
            $validator = new Validator($tokenObject, 0);
            $validator
                // ->validateAudience($audience)
                ->validateExpiration($expires);
            // ->validateId($id)
            // ->validateIssuedAt($issued)
            // ->validateIssuer($issuer)
            // ->validateNotBefore($notBefore)
            // ->validateSignature($signer, $passphrase);
            // echo "<br> token is valid <br>";
        } catch (\Exception $e) {
            echo "<pre>";
            echo "</pre>";
            echo $e->getMessage();
            $resp = "error";
            echo "<br>";
            die();
            // die("token has errors");
        }
        return $resp;
    }
    public function firebaseJwtValidate($tokenReceived)
    {
        try {
            $key = "QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2";
            $decoded = JWT::decode($tokenReceived, new Key($key, 'HS512'));
            // print_r($decoded);
            // echo $this->datetime->getTimestamp();
            if ($this->datetime->getTimestamp() < $decoded->exp) {
                // echo  "token is valid";
                if (isset($decoded->sub)) {
                    // echo "valid token";
                } else {
                    // echo "role not found";
                    die();
                }
            } else {
                $lang  = $this->request->getquery()['locale'] ?? 'en';
                echo $this->translator->getTranslator($lang)->_("token has expired");
                // echo "token has expired";
            }
        } catch (\Exception $e) {
            $lang  = $this->request->getquery()['locale'] ?? 'en';
            echo $this->translator->getTranslator($lang)->_($e->getMessage());
            die();
        }
        // die();
    }
}
