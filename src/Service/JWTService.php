<?php

namespace App\Service;

use DateTimeImmutable;

class JWTService
{
    /**
     * generate JWT
     *
     * @param  array $header
     * @param  array $payload
     * @param  string $secret
     * @param  int $validity
     * @return string
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        if ($validity > 0) {
            $now = new DateTimeImmutable();
            $exp = ($now->getTimestamp() + $validity);

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        // encode base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // strip (+, / and =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // generate signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        // the token
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

        return $jwt;
    }

    /**
     * is the token valid
     *
     * @param  string $token
     * @return bool
     */
    public function isValid(string $token): bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }

    /**
     * get token Header
     *
     * @param  string $token
     * @return array
     */
    public function getHeader(string $token): array
    {
        // explode the token
        $array = explode('.', $token);

        // decode the payload
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    /**
     * get token Payload
     *
     * @param  string $token
     * @return array
     */
    public function getPayload(string $token): array
    {
        // explode the token
        $array = explode('.', $token);

        // decode the payload
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    /**
     * is the token Expired
     *
     * @param  string $token
     * @return bool
     */
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    /**
     * check the token
     *
     * @param  string $token
     * @param  string $secret
     * @return void
     */
    public function check(string $token, string $secret)
    {
        // header and payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // generate token
        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;
    }
}