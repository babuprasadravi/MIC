<?php

function expand($time)
{
    $t = [
        "5AM" => "5AM-6AM",
        "6AM" => "6AM-7AM",
        "7AM" => "7AM-8AM",
        "8AM" => "8AM-9AM",
        "9AM" => "9AM-10AM",
        "10AM" => "10AM-11AM",
        "11AM" => "11AM-12PM",
        "12PM" => "12PM-1PM",
        "1PM" => "1PM-2PM",
        "2PM" => "2PM-3PM",
        "3PM" => "3PM-4PM",
        "4PM" => "4PM-5PM",
        "5PM" => "5PM-6PM",
        "6PM" => "6PM-7PM",
        "7PM" => "7PM-8PM",
        "8PM" => "8PM-9PM"
    ];

    $keys = array_keys($t);

    $from = explode("-", $time)[0];
    $to = explode("-", $time)[1];

    $range = [];

    $start = array_search($from, $keys);
    $end = array_search($to, $keys) - 1;

    for ($i = $start; $i <= $end; $i++) {
        $range[] = $t[$keys[$i]];
    }
    return $range;
}
