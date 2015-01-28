<?php

/**
 * Testing the chat entries.
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
require_once './classes/Entry.php';

/**
 * Testing the chat class.
 *
 * @category Testing
 * @package  Chat
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chat_XH
 */
class EntryTest extends PHPUnit_Framework_TestCase
{
    /**
     * The test timestamp.
     */
    const TIMESTAMP = 1234567;

    /**
     * The test username.
     */
    const USERNAME = 'cmb';

    /**
     * The test message.
     */
    const MESSAGE = 'blah blah';

    /**
     * Tests that ::makeFromLine() sets the timestamp.
     *
     * @return void
     */
    public function testMakeFromLineSetsTimestamp()
    {
        $entry = Chat_Entry::makeFromLine($this->getLine());
        $this->assertEquals(self::TIMESTAMP, $entry->getTimestamp());
    }

    /**
     * Tests that ::makeFromLine() sets the username.
     *
     * @return void
     */
    public function testMakeFromLineSetsUsername()
    {
        $entry = Chat_Entry::makeFromLine($this->getLine());
        $this->assertEquals(self::USERNAME, $entry->getUsername());
    }

    /**
     * Tests that ::makeFromLine() sets the message.
     *
     * @return void
     */
    public function testMakeFromLineSetsMessage()
    {
        $entry = Chat_Entry::makeFromLine($this->getLine());
        $this->assertEquals(self::MESSAGE, $entry->getMessage());
    }

    /**
     * Tests that the line is correct.
     *
     * @return void
     */
    public function testLineIsCorrect()
    {
        $entry = new Chat_Entry();
        $entry->setTimestamp(self::TIMESTAMP);
        $entry->setUsername(self::USERNAME);
        $entry->setMessage(self::MESSAGE);
        $this->assertEquals($this->getLine(), $entry->getLine());
    }

    /**
     * Returns the CSV line.
     *
     * @return string
     */
    protected function getLine()
    {
        return self::TIMESTAMP . "\t" . self::USERNAME . "\t" . self::MESSAGE;
    }
}

?>
