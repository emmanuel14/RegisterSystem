<?php

namespace Helpers;

/**
 * Calendar integration — .ics file generation and Google Calendar links.
 */
class Calendar
{
    public static function googleUrl(array $event): string
    {
        $title   = urlencode($event['title']);
        $start   = self::formatGoogleDate($event['start_date']);
        $end     = self::formatGoogleDate($event['end_date']);
        $details = urlencode(strip_tags($event['description'] ?? ''));
        $location = urlencode(trim(($event['venue'] ?? '') . ', ' . ($event['city'] ?? ''), ', '));

        return "https://calendar.google.com/calendar/render?action=TEMPLATE"
            . "&text={$title}&dates={$start}/{$end}&details={$details}&location={$location}";
    }

    public static function generateIcs(array $event, string $registrationCode = ''): string
    {
        $uid     = 'ems-' . ($event['id'] ?? uniqid()) . '@' . ($_SERVER['HTTP_HOST'] ?? 'church.local');
        $start   = self::formatIcsDate($event['start_date']);
        $end     = self::formatIcsDate($event['end_date']);
        $now     = gmdate('Ymd\THis\Z');
        $summary = self::escapeIcs($event['title']);
        $desc    = self::escapeIcs($event['description'] ?? '');
        if ($registrationCode) {
            $desc .= "\\nRegistration: {$registrationCode}";
        }
        $location = self::escapeIcs(trim(($event['venue'] ?? '') . ', ' . ($event['city'] ?? ''), ', '));

        return implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//EMS Church Platform//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            "UID:{$uid}",
            "DTSTAMP:{$now}",
            "DTSTART:{$start}",
            "DTEND:{$end}",
            "SUMMARY:{$summary}",
            "DESCRIPTION:{$desc}",
            "LOCATION:{$location}",
            'END:VEVENT',
            'END:VCALENDAR',
        ]);
    }

    private static function formatGoogleDate(string $datetime): string
    {
        return gmdate('Ymd\THis\Z', strtotime($datetime));
    }

    private static function formatIcsDate(string $datetime): string
    {
        return gmdate('Ymd\THis\Z', strtotime($datetime));
    }

    private static function escapeIcs(string $text): string
    {
        return str_replace(["\r\n", "\n", "\r", ',', ';'], ['\\n', '\\n', '\\n', '\\,', '\\;'], $text);
    }
}
