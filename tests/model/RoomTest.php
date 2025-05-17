<?php

namespace Chat\Model;

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/** @small */
class RoomTest extends TestCase
{
    /** @var Room*/
    private $subject;

    public function setUp(): void
    {
        global $pth;

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('chat'));
        $pth = ['folder' => ['content' => vfsStream::url('')]];
        $this->subject = new Room('foo', 3600);
    }

    public function testDataFolder(): void
    {
        $this->assertEquals(vfsStream::url('chat/'), Room::dataFolder());
    }

    /** @dataProvider validNamesData */
    public function testValidNames(string $name, bool $expected): void
    {
        $this->assertSame($expected, Room::isValidName($name));
    }

    public function validNamesData(): array
    {
        return [
            [
                'chat-17', true,
                'under_score', false,
                'Capitalized', false
            ]
        ];
    }

    public function testIsNotExpired(): void
    {
        $this->subject->appendEntry($this->entry());
        $this->assertFalse($this->subject->isExpired());
    }

    public function testFindOneEntry(): void
    {
        $this->subject->appendEntry($this->entry());
        $this->assertCount(1, $this->subject->findEntries());
    }

    public function testFileExistsAfterAppendingEntry(): void
    {
        $this->subject->appendEntry($this->entry());
        $this->assertFileExists(vfsStream::url('chat/foo.csv'));
    }

    public function testPurgingRemovesFile(): void
    {
        $this->subject->appendEntry($this->entry());
        $this->subject->purge();
        $this->assertFileDoesNotExist(vfsStream::url('chat/foo.csv'));
    }

    private function entry(): Entry
    {
        return new Entry(1234567, "cmb", "blah blah");
    }
}
