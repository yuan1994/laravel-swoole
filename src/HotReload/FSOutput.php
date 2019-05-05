<?php

namespace SwooleTW\Http\HotReload;

/**
 * Class FSOutput
 */
class FSOutput
{
    /**
     * @param \SwooleTW\Http\HotReload\FSEvent $event
     *
     * @return string
     */
    public static function format(FSEvent $event): string
    {
        $item = is_dir($event->getPath()) ? 'Directory' : 'File';
        $events = implode(', ', $event->getTypes());
        $time = $event->getWhen();

        return sprintf('%s: %s %s at %s', $item, $event->getPath(), $events, $time);
    }
}