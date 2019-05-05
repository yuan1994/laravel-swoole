<?php

namespace SwooleTW\Http\HotReload;

use Carbon\Carbon;

/**
 * Class FSEventParser
 */
class FSEventParser
{
    protected const REGEX = '/^(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})\s+(\S+)\s+(.*)/m';

    /**
     * @param string $event
     * @param array $eventTypes
     *
     * @return \SwooleTW\Http\HotReload\FSEvent[]
     */
    public static function toEvent(string $event, array $eventTypes): ?array
    {
        if (preg_match_all(static::REGEX, $event, $matches)) {
            $fsEvents = [];
            foreach ($matches[3] as $idx => $match) {
                $events = explode(' ', $match);
                $events = array_intersect($eventTypes, $events);
                if (count($events) === 0) {
                    continue;
                }
                $date = $matches[1][$idx];
                $path = $matches[2][$idx];
                $fsEvents[] = new FSEvent($date, $path, $events);
            }
            if (empty($fsEvents)) {
                return null;
            }

            return $fsEvents;
        }

        return null;
    }
}