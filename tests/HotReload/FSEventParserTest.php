<?php

namespace SwooleTW\Http\Tests\HotReload;

use Carbon\Carbon;
use SwooleTW\Http\HotReload\FSEvent;
use SwooleTW\Http\HotReload\FSEventParser;
use SwooleTW\Http\Tests\TestCase;

/**
 * Class FSEventParserTest
 */
class FSEventParserTest extends TestCase
{
    public function testItCanCreateObjectAfterParse()
    {
        $buffer = "2019-05-05 15:44:27 /Some/Path/To/File/File.php Renamed OwnerModified IsFile\n"
            . "2019-05-05 15:44:30 /Some/Path/To/File/File2.php Created\n"
            . "2019-05-05 15:44:31 /Some/Path/To/File/File3.php IsFile";
        $eventTypes = ['Renamed', 'Created'];
        $events = FSEventParser::toEvent($buffer, $eventTypes);

        $this->assertEquals(2, count($events));

        $event = $events[0];

        $this->assertInstanceOf(FSEvent::class, $event);

        $this->assertTrue(array_diff($event->getTypes(), [FSEvent::Renamed, FSEvent::OwnerModified]) === []);
        $this->assertEquals('2019-05-05 15:44:27', $event->getWhen());
        $this->assertEquals('/Some/Path/To/File/File.php', $event->getPath());
        $this->assertTrue($event->isType(FSEvent::Renamed));
        $this->assertFalse($event->isType(FSEvent::OwnerModified));
    }
}