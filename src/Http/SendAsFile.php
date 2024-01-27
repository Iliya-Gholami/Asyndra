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
 * class SendAsFile
 */
final class SendAsFile implements HttpUploadFile
{
    /**
     * @var string $postFileName post file name
     */
    private string $postFileName;

    /**
     * @var mixed $content content
     */
    private string $content;

    /**
     * Constructor
     * 
     * @param string $postFileName post file name
     * @param string $content content
     */
    public function __construct(string $postFileName, string $content)
    {
        $this->postFileName = $postFileName;
        $this->content = $content;
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
     * Get content
     * 
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}