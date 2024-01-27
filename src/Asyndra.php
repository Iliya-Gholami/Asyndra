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

use \Asyndra\DelayTask;
use \Asyndra\UdelayTask;
use \Asyndra\AwaitTask;
use \Asyndra\Interface\Async;

/**
 * Run async tasks
 * 
 * @param Generator ... $tasks tasks
 * @return array
 */
function run(\Generator ... $tasks): array
{
    $results = [];
    $tasksCount = count($tasks);

    while( !empty($tasks) ) {

        foreach( $tasks as $id => $task ) {

            if( $task->valid() ) {

                $currentTask = $task->current();

                if( $currentTask instanceof Generator ) {

                    $tasks[] = $currentTask;
                    $task->next();

                } else if( $currentTask instanceof Async ) {

                    if( $currentTask->shouldRun() ) {

                        $currentTask->run($task);

                    }

                } else {

                    $task->send($currentTask);

                }

                unset($currentTask, $task, $id);

            } else {

                if( $tasksCount > $id ) {

                    $results[$id] = $task->getReturn();

                }

                unset($tasks[$id], $task, $id);

            }
        } 
    }

    return $results;
}

/**
 * Await
 * 
 * @param Generator $task task
 * @return AwaitTask
 */
function await(\Generator $task): AwaitTask
{
    return new AwaitTask($task);
}

/**
 * Delay
 * 
 * @param int $delay delay
 * @return DelayTask
 */
function delay(int $delay): DelayTask
{
    return new DelayTask($delay);
}

/**
 * Udelay
 * 
 * @param int $delay micro delay
 * @return UdelayTask
 */
function udelay(int $delay): UdelayTask
{
    return new UdelayTask($delay);
}