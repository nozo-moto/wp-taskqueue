<?php
use SuperClosure\Serializer;

const TASKQUEUE_INTERVAL = 10;

function wp_task_queue_add(callable $callback, array $args = [], $timestamp = 0, $repete_times = - 1)
{
    $serializer = new Serializer();

    return wp_schedule_single_event($timestamp ?: time() + 3, 'wp_taskqueu_queues_trigger', array(
        $serializer->serialize($callback),
        $args,
        $repete_times,
        uniqid()
    ));
}

add_action('wp_taskqueu_queues_trigger', 'wp_task_queue_run', 10, 4);

function woo_nextengin_task_queue_run($serializedCallback, $args, $repete_times, $uniqid)
{
    $serializer = new Serializer();
    $callback   = $serializer->unserialize($serializedCallback);

    error_log("NEXT ENGINE TASK QUEUE !!!  " . $repete_times);

    try {
        $callback($args);
    } catch (Exception $e) {
        if ($repete_times - 1 <= 0) {
        } else {
            wp_schedule_single_event(time() + 60, 'wp_taskqueu_queues_trigger', array(
                $serializedCallback,
                $args,
                $repete_times - 1,
                uniqid()
            ));
        }
    }
}
