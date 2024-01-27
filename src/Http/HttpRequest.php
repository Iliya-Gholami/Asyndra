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

use \Asyndra\Interface\HttpUploadFile;

/**
 * class HttpRequest
 */
final class HttpRequest
{
    /**
     * @const array ACCEPTABLE_METHODS acceptable http methods
     */
    const ACCEPTABLE_METHODS = ["GET", "HEAD", "POST", "PUT", "DELETE", "PATCH", "TRACE", "CONNECT"];

    /**
     * @const string ENCRYPTION_IV iv
     */
    const ENCRYPTION_IV = "RgSkEvSaU2+FDhctAtvarw==";

    /**
     * @const string VALID_URL_REGEX regex to check url is valid or is not valid
     */
    const VALID_URL_REGEX = "/^(http|https):\/\/((?:\d{1,3}\.){3}\d{1,3}|localhost|[a-zA-Z0-9-\.]+\.[a-z]{2,})(:[0-9]+)?(\/.*)?$/";

    /**
     * @var array $settings request settings
     */
    private $settings;

    /**
     * Constructor
     * 
     * @param string $url url
     * @param string $method method
     * @param int $timeout timeout
     */
    public function __construct(string $url = "", string $method = "GET", int $timeout = 7)
    {

        if ( !empty($url) and preg_match(self::VALID_URL_REGEX, $url) !== 1 ) {

            throw new \Exception("Url format is invalid");
            return;

        }

        if( !in_array($method, self::ACCEPTABLE_METHODS) ) {

            throw new \Exception("HTTPRequest error: method($method) isn't valid, Acceptable methods: " . implode(", ", self::ACCEPTABLE_METHODS));
            return;

        }

        $this->settings = [

            "url" => $url,
            "method" => $method,
            "timeout" => $timeout,
            "body" => "",
            "headers" => "",
            "options" => [],
            "chunkSize" => null,
            "addressPool" => [],
            "keepAlive" => false,
            "contentType" => "application/x-www-form-urlencoded"

        ];
    }

    /**
     * Set url
     * 
     * @param string $url url
     * @return void
     */
    public function setUrl(string $url): void
    {
        if( empty($url) ) {

            throw new \Exception("HTTPRequest error: can't set empty url");
            return;

        } else if ( preg_match(self::VALID_URL_REGEX, $url) !== 1 ) {

            throw new \Exception("Url format is invalid");
            return;

        }

        $this->settings["url"]  = $url;
    }

    /**
     * Set method
     * 
     * @param string $method request method
     * @return void
     */
    public function setMethod(string $method): void
    {
        if( !in_array($method, self::ACCEPTABLE_METHODS) ) {

            throw new \Exception("HTTPRequest error: method($method) isn't valid, Acceptable methods: " . implode(", ", self::ACCEPTABLE_METHODS));
            return;

        }

        $this->settings['method'] = $method;
    }

    /**
     * Set timeout
     * 
     * @param int $timeout timeout
     * @return void
     */
    public function setTimeout(int $timeout): void
    {
        $this->settings["timeout"] = $timeout;
    }

    /**
     * Add header
     * 
     * @param string $key header key
     * @param string $value header value
     * 
     * @return void
     */
    public function addHeader(string $key, string $value): void
    {
        if( strtolower($key) === "content-type" ) return;

        $this->settings["headers"] .= "$key: $value\r\n";
    }


    /**
     * Set body
     * 
     * @param mixed $body body
     * @return void
     */
    public function setBody(mixed $body): void
    {
        $this->settings['body'] = $body;
    }

    /**
     * Set headers
     * 
     * @param array $headers headers
     * @return void
     */
    public function setHeaders(array $headers): void
    {
        $this->settings["headers"] = implode("\r\n", $headers);
    }

    /**
     * Add option
     * 
     * @param string $key key
     * @param string $option option
     * @param mixed $value option value
     * 
     * @return void
     */
    public function addOption(string $key, string $option, mixed $value): void
    {
        $this->settings["options"][$key][$option] = $value;
    }

    /**
     * Set read chunk size
     * 
     * @param int $size chunk size
     * @return void
     */
    public function setChunkSize(int $size): void
    {
        $this->settings['chunkSize'] = $size;
    }

    /**
     * Content type
     * 
     * @param string $contentType content type
     * @return void
     */
    public function setContentType(string $contentType): void
    {
        if( empty($contentType) ) {

            throw new \Exception("Content type can't be empty");
            return;

        }

        $this->settings["contentType"] = $contentType;
    }

    /**
     * Ssl verify peer
     * 
     * @param bool $verifyPeer verify peer
     * @return void
     */
    public function sslVerifyPeer(bool $verifyPeer): void
    {
        $this->addOption("ssl", "verify_peer", $verifyPeer);
        $this->addOption("ssl", "verify_peer_name", $verifyPeer);
    }

    /**
     * Keep alive
     * 
     * @return void
     */
    public function keepAlive(): void
    {
        $this->settings["keepAlive"] = true;
        $this->addOption("socket", "so_keepalive", true);
    }

    /**
     * Save cookie
     * 
     * @param string $fileName location to save cookie file
     * @return void
     */
    public function saveCookie(string $fileName): void
    {
        $this->settings["saveCookie"] = true;
        $this->settings["saveCookieLocation"] = $fileName;
    }

    /**
     * Use cookie
     * 
     * @param string $fileName cookie file name
     * @return void
     */
    public function useCookie(string $fileName): void
    {
        $this->settings["useCookie"] = true;
        $this->settings["cookieFileName"] = $fileName;
    }

    /**
     * Enable cookie file encryption
     * 
     * @param string $password password
     * @return void
     */
    public function cookieFileEncryption(string $password): void
    {
        $this->settings['cookieFileEncryption'] = true;
        $this->settings['cookieFilePassword'] = hash("sha256", $password . '-Asyndra@tps');
    }

    /**
     * Clear pool
     * 
     * @void
     */
    public function clearPool(): void
    {
        $this->settings["addressPool"] = [];
    }

    /**
     * Execute
     * 
     * @param bool $getBody get body
     * @return HttpResponse
     */
    public function execute(bool $getBody = true): HttpResponse
    {

        if( !isset($this->settings["addressPool"][$this->settings["url"]]) ) {

            if( empty($this->settings["url"]) ) {
    
                throw new \Exception("HTTPRequest execution error: url is empty");
                return new HttpResponse();
    
            }
    
            $parsedUrl = parse_url($this->settings["url"]);
    
            $scheme = $parsedUrl["scheme"];
            $port = $parsedUrl["port"] ?? ($scheme === "https" ? 443 : 80);
            $protocol = $scheme === "https" ? "tls://" : "tcp://";
    
            $this->settings["addressPool"][$this->settings["url"]] = [
    
                "host" => $parsedUrl["host"],
                "path" => $parsedUrl["path"] ?? "/",
                "address" => $protocol . $parsedUrl["host"] . ":" . $port
    
            ];

            unset($parsedUrl, $scheme, $port, $protocol);

        }

        $method = $this->settings["method"];
        $pool = $this->settings["addressPool"][$this->settings["url"]];
        $path = $pool["path"];
        $host = $pool["host"];

        if( is_array($this->settings["body"]) ) {

            $body = "";
            $boundary = md5(uniqid() . microtime(true));
            $contentType = "multipart/form-data; boundary=$boundary";
            $boundary = "--" . $boundary;

            foreach($this->settings["body"] as $key => $value) {

                $body .= $boundary . "\r\n";

                if( $value instanceof HttpUploadFile ) {

                    $fileName = $value->getPostFileName();
                    $body .= "Content-Disposition: form-data; name=\"$key\"; filename=\"$fileName\"\r\n";
                    $body .= "Content-Type: application/octet-stream\r\n\r\n";
                    $body .= $value->getContent() . "\r\n";

                    unset($fileName, $value);

                } else {

                    $body .= "Content-Disposition: form-data; name=\"$key\"\r\n\r\n";
                    $body .= $value . "\r\n";

                }

            }

            $body .= $boundary . "--\r\n";

        } else {

            $body = $this->settings["body"];
            $contentType = $this->settings["contentType"];

        }

        $request = "$method $path HTTP/1.1\r\n";
        $request .= "Host: $host\r\n";
        $request .= "Content-Type: $contentType\r\n";
        $request .= "Content-Length: " . strlen($body) . "\r\n";

        if( isset($this->settings["useCookie"]) and file_exists($this->settings["cookieFileName"]) and filesize( $this->settings["cookieFileName"]) > 0 ) {

            $handle = fopen($this->settings["cookieFileName"], 'r');

            stream_set_blocking($handle, false);
            stream_set_read_buffer($handle, 0);

            $cookies = stream_get_contents($handle);

            fclose($handle);

            if( isset($this->settings['cookieFileEncryption']) ) {

                $cookies = openssl_decrypt($cookies, 'AES256', $this->settings['cookieFilePassword'], 0, base64_decode(self::ENCRYPTION_IV));

            }

            @$cookies = json_decode($cookies, true);

            if( !empty($cookies) and !is_null($cookies) ) {

                $request .= "Cookie: " . implode(";", $cookies) . "\r\n";

            }

        }

        $request .= "Connection: " . ($this->settings["keepAlive"] === true ? "keep-alive" : "close") . "\r\n\r\n";
        $request .= $body;

        $socket = stream_socket_client(

            $pool["address"],
            $errorCode,
            $errorMessage,
            $this->settings["timeout"],
            STREAM_CLIENT_ASYNC_CONNECT,
            stream_context_create($this->settings["options"])

        );

        if( !$socket ) {

            throw new \Exception("HTTPRequest execution error: socket error code($errorCode) $errorMessage");
            return new HttpResponse();

        }

        stream_set_blocking($socket, false);

        fwrite($socket, $request);

        if( $getBody === false ) {

            $this->settings["socket"] = $socket;
            fclose($socket);

            return new HttpResponse();

        }

        $response = "";

        while( !feof($socket) ) {

            $response .= fgets($socket, $this->settings['chunkSize']);

        }

        fclose($socket);

        $response = new HttpResponse($response);

        if( isset($this->settings['saveCookie']) ) {

            $cookies = json_encode($response->getCookies());

            if( isset($this->settings['cookieFileEncryption']) ) {

                $cookies = openssl_encrypt($cookies, 'AES256', $this->settings['cookieFilePassword'], 0, base64_decode(self::ENCRYPTION_IV));

            }

            $handle = fopen($this->settings['saveCookieLocation'], 'w');

            stream_set_blocking($handle, false);
            stream_set_write_buffer($handle, 0);

            fwrite($handle, $cookies);

            fclose($handle);

            chmod($this->settings['saveCookieLocation'], 0600);

        }

        return $response;
    }
}