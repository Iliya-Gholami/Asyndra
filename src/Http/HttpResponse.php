<?php

/**
 *    _                    _ 
 *   /_\   ____  _ _ _  __| |_ _ __ _
 *  / _ \ (_-< || | ' \/ _` | '_/ _` |
 * /_/ \_\/__/\_, |_||_\__,_|_| \__,_|
 *            |__/
 *
 * @author Iliya Gholami 2023 - 2024 <https://t.me/Iliya_Gholami>
 * @copyright Iliya Gholami 2023 - 2024 <https://t.me/Iliya_Gholami>
 */
declare(strict_types = 1);

namespace Asyndra\Http;

/**
 * class HttpResponse
 */
final class HttpResponse
{
    /**
     * @var string $header header
     */
    public string $headers = "";

    /**
     * @var string $body body
     */
    public string $body = "";

    /**
     * @var array $cookies cookies
     */
    public array $cookies = [];

    /**
     * @var bool $areCookiesEmpty are cookies empty
     */
    public bool $areCookiesEmpty = false;

    /**
     * Constructor
     * 
     * @param string $response response
     */
    public function __construct(string $response = "")
    {

        if( empty($response) ) return;

        $explode = explode("\r\n\r\n", $response, 2);
        $this->headers = $explode[0];
        $this->body = $explode[1];

    }

    /**
     * __toString
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->body;
    }

    /**
     * Get cookies
     * 
     * @return array
     */
    public function getCookies(): array
    {
        if( empty($this->headers) or $this->areCookiesEmpty === true ) return [];
        if( !empty($this->cookies) ) return $this->cookies;

        $headers = explode("\r\n", $this->headers);
        $cookies = [];

        foreach($headers as $header) {

            $header = explode(": ", $header);

            if( $header[0] === 'Set-Cookie' ) {

                $cookies[] = $header[1];

            }

        }

        $this->cookies = $cookies;

        if( empty($cookies) ) {

            $this->areCookiesEmpty = true;
            return [];

        }

        return $cookies;
    }
}