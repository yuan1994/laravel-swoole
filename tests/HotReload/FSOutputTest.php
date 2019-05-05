<?php

namespace SwooleTW\Http\Tests\HotReload;

use SwooleTW\Http\HotReload\FSEventParser;
use SwooleTW\Http\HotReload\FSOutput;
use SwooleTW\Http\Tests\TestCase;

/**
 * Class FSOutputTest
 */
class FSOutputTest extends TestCase
{
    public function testItFormatOutputCorrectly()
    {
        $buffer = '2019-05-05 15:44:27 /Some/Path/To/File/File.php Renamed OwnerModified IsFile';
        $events = FSEventParser::toEvent($buffer, ['Renamed']);

        $event = $events[0];

        $this->assertEquals(
            'File: /Some/Path/To/File/File.php Renamed at 2019-05-05 15:44:27',
            FSOutput::format($event)
        );
    }
}