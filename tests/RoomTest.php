<?php

namespace Chat;

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

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
        // $this->makeEntryFromLineMock = new PHPUnit_Extensions_MockStaticMethod('Entry::makeFromLine', $this->subject);
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
        $entry = $this->createMock(Entry::class);
        $this->subject->appendEntry($entry);
        $this->assertFalse($this->subject->isExpired());
    }

    public function testFindOneEntry(): void
    {
        $this->markTestSkipped();
        $entry = $this->createMock(Entry::class);
        $this->subject->appendEntry($entry);
        $this->assertCount(1, $this->subject->findEntries());
    }

    public function testFileExistsAfterAppendingEntry(): void
    {
        $entry = $this->createMock(Entry::class);
        $this->subject->appendEntry($entry);
        $this->assertFileExists(vfsStream::url('chat/foo.csv'));
    }

    public function testPurgingRemovesFile(): void
    {
        $entry = $this->createMock(Entry::class);
        $this->subject->appendEntry($entry);
        $this->subject->purge();
        $this->assertFileDoesNotExist(vfsStream::url('chat/foo.csv'));
    }
}
