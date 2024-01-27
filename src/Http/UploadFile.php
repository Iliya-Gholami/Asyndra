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
 * class UploadFile
 */
final class UploadFile implements HttpUploadFile
{
    /**
     * @var string $fileName file name
     */
    private string $fileName;

    /**
     * @var string $postFileName post file name
     */
    private string $postFileName;

    /**
     * @var string $content content
     */
    private string $content = "";

    /**
     * Constructor
     * 
     * @param string $fileName file name
     */
    public function __construct(string $fileName, string $postFileName = "")
    {
        $this->fileName = $fileName;
        $this->postFileName = empty($postFileName) ? $fileName : $postFileName;
    }

    /**
     * Get post file name
     * 
     * @return string
     */
    public function getPostFileName(): string
    {
        return $this->postFileName;
    }

    /**
     * getContent
     * 
     * @return string
     */
    public function getContent(): string
    {
        if( empty($this->content) ) {

            if( !file_exists($this->fileName) ) {

                throw new \Error("UploadFile error: " . $this->fileName . " doesn't exist");
                return "";

            }

            $handle = fopen($this->fileName, "r");

            stream_set_blocking($handle, false);
            stream_set_chunk_size($handle, 1048576);

            $this->content = stream_get_contents($handle);
            fclose($handle);

        }

        return $this->content;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->content, $this->fileName, $this->postFileName);
    }
}