<?php

/**
 * Testing the chat rooms.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Chat
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2014-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Chat_XH
 */

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

/**
 * Testing the chat rooms.
 *
 * @category Testing
 * @package  Chat
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chat_XH
 */
class RoomTest extends PHPUnit_Framework_TestCase
{
    /** @var Room*/
    protected $subject;

    public function setUp(): void
    {
        global $pth;

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('chat'));
        $pth = ['folder' => ['content' => vfsStream::url('')]];
        $this->subject = new Room('foo', 3600);
        $this->makeEntryFromLineMock = new PHPUnit_Extensions_MockStaticMethod(
            'Entry::makeFromLine', $this->subject
        );
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
        $entry = $this->getMock('Entry');
        $this->subject->appendEntry($entry);
        $this->assertFalse($this->subject->isExpired());
    }

    public function testFindOneEntry(): void
    {
        $entry = $this->getMock('Entry');
        $this->subject->appendEntry($entry);
        $this->assertCount(1, $this->subject->findEntries());
    }

    public function testFileExistsAfterAppendingEntry(): void
    {
        $entry = $this->getMock('Entry');
        $this->subject->appendEntry($entry);
        $this->assertFileExists(vfsStream::url('chat/foo.csv'));
    }

    public function testPurgingRemovesFile(): void
    {
        $entry = $this->getMock('Entry');
        $this->subject->appendEntry($entry);
        $this->subject->purge();
        $this->assertFileNotExists(vfsStream::url('chat/foo.csv'));
    }
}
