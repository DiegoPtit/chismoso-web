<?php

namespace app\helpers;

class TimeHelper
{
    public static function getRelativeTime($datetime)
    {
        // Crear objeto DateTime con la zona horaria del servidor
        $datetime = new \DateTime($datetime, new \DateTimeZone(date_default_timezone_get()));
        $now = new \DateTime('now', new \DateTimeZone(date_default_timezone_get()));
        
        $diff = $now->diff($datetime);
        
        if ($diff->y > 0) {
            return $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
        }
        if ($diff->m > 0) {
            return $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
        }
        if ($diff->d > 0) {
            return $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
        }
        if ($diff->h > 0) {
            return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
        }
        if ($diff->i > 0) {
            return $diff->i . ' min';
        }
        return '> 1s';
    }
} 