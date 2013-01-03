<?php

class UtilFunctions {

    public static function startsWith($haystack, $needle) {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    public static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    public static function udate($format, $utimestamp = null) {
        if (is_null($utimestamp))
            $utimestamp = microtime(true);

        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }

    public static function getTimeDiffString($datestart, $dateend) {
        try {
            $start_date = new DateTime($datestart, new DateTimeZone('GMT'));
            $end_date = new DateTime($dateend, new DateTimeZone('GMT'));
            $since_start = $start_date->diff($end_date);

            $result = null;
            if ($since_start->y > 0 && empty($result))
                $result = $since_start->y . 'y';
            if ($since_start->m > 0 && empty($result))
                $result = $since_start->m . 'mo';
            if ($since_start->d > 0 && empty($result))
                $result = $since_start->d . 'd';
            if ($since_start->h > 0 && empty($result))
                $result = $since_start->h . 'h';
            if ($since_start->m > 0 && empty($result))
                $result = $since_start->m . 'm';


            if (!empty($result)) {
                return $result;
            } else {
                return "~m";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return "~m";
        }
    }

}

?>
