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

namespace Asyndra;

use \Asyndra\Interface\Async;

/**
  * class DelayTask
  */
final class DelayTask implements Async
{
    /**
     * @var float $runAt run at
     */
    private float $runAt;
  
    /**
     * Constructor
     * 
     * @param int $delay delay
     */
    public function __construct(int $delay)
    {
        $this->runAt = microtime(true) + $delay;
    }
  
    /**
     * Should run
     * 
     * @return bool
     */
    public function shouldRun(): bool
    {
        return $this->runAt <= microtime(true);
    }
  
    /**
     * Run
     *
     * @param Generator $task task
     * @return void
     */
    public function run(\Generator $task)
    {
        $task->next();
    }
}