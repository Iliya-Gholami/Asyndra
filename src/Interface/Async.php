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

namespace Asyndra\Interface;

/**
 * interface Async
 */
interface Async
{
    public function shouldRun(): bool;
    public function run(\Generator $task);
}