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
 * class AwaitTask
 */
final class AwaitTask implements Async
{
    /**
     * @var Generator $task $task
     */
    private Generator $task;

    /**
     * Constructor
     * 
     * @param Generator $task task
     */
    public function __construct(\Generator $task)
    {
        $this->task = $task;
    }

    /**
     * Should run
     * 
     * @return bool
     */
    public function shouldRun(): bool
    {
        return true;
    }

    /**
     * Run
     * 
     * @param Generator $task task
     * @return void
     */
    public function run(Generator $task): void
    {
        $task->send(run($this->task)[0]);
    }
}