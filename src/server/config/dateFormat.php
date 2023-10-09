<?php
if (!function_exists("timeAgo")) {
    function timeAgo($timestamp): string
    {
        $timestamp = strtotime($timestamp);
        $currentTimestamp = strtotime(gmdate(DATE_RFC3339));
        $timeDifference = $currentTimestamp - $timestamp;

        $intervals = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second',
        );

        foreach ($intervals as $seconds => $label) {
            $quotient = $timeDifference / $seconds;
            if ($quotient >= 1) {
                $rounded = round($quotient);
                $plural = ($rounded > 1) ? 's' : '';
                return $rounded . ' ' . $label . $plural . ' ago';
            }
        }

        return 'just now';
    }
}

echo timeAgo($createdAt);