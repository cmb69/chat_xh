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

require_once './vendor/autoload.php';
require_once './classes/required_classes.php';

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
    /**
     * The test subject.
     *
     * @var Chat_Room
     */
    protected $subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     */
    public function setUp()
    {
        global $pth;

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('chat'));
        $pth = ['folder' => ['content' => vfsStream::url('')]];
        $this->subject = new Chat_Room('foo', 3600);
        $this->makeEntryFromLineMock = new PHPUnit_Extensions_MockStaticMethod(
            'Chat_Entry::makeFromLine', $this->subject
        );
    }

    /**
     * Tests the data folder.
     *
     * @return void
     */
    public function testDataFolder()
    {
        $this->assertEquals(vfsStream::url('chat/'), Chat_Room::dataFolder());
    }

    /**
     * Tests valid names.
     *
     * @param string $name     A name.
     * @param bool   $expected Whether the name is expected to be valid.
     *
     * @dataProvider validNamesData
     *
     * @return void
     */
    public function testValidNames($name, $expected)
    {
        $this->assertSame($expected, Chat_Room::isValidName($name));
    }

    /**
     * Returns data for valid name testing.
     *
     * @return array
     */
    public function validNamesData()
    {
        return [
            [
                'chat-17', true,
                'under_score', false,
                'Capitalized', false
            ]
        ];
    }

    /**
     * Tests that a room is not expired.
     *
     * @return void
     */
    public function testIsNotExpired()
    {
        $entry = $this->getMock('Chat_Entry');
        $this->subject->appendEntry($entry);
        $this->assertFalse($this->subject->isExpired());
    }

    /**
     * Tests finding one entry.
     *
     * @return void
     */
    public function testFindOneEntry()
    {
        $entry = $this->getMock('Chat_Entry');
        $this->subject->appendEntry($entry);
        $this->assertCount(1, $this->subject->findEntries());
    }

    /**
     * Tests that the file exists after appending an entry.
     *
     * @return void
     */
    public function testFileExistsAfterAppendingEntry()
    {
        $entry = $this->getMock('Chat_Entry');
        $this->subject->appendEntry($entry);
        $this->assertFileExists(vfsStream::url('chat/foo.csv'));
    }

    /**
     * Tests that purging removes the file.
     *
     * @return void
     */
    public function testPurgingRemovesFile()
    {
        $entry = $this->getMock('Chat_Entry');
        $this->subject->appendEntry($entry);
        $this->subject->purge();
        $this->assertFileNotExists(vfsStream::url('chat/foo.csv'));
    }
}

?>
