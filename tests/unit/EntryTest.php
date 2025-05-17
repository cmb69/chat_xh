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

namespace Chat;

class EntryTest extends PHPUnit_Framework_TestCase
{
    const TIMESTAMP = 1234567;

    const USERNAME = 'cmb';

    const MESSAGE = 'blah blah';

    public function testMakeFromLineSetsTimestamp(): void
    {
        $entry = Entry::makeFromLine($this->getLine());
        $this->assertEquals(self::TIMESTAMP, $entry->getTimestamp());
    }

    public function testMakeFromLineSetsUsername(): void
    {
        $entry = Entry::makeFromLine($this->getLine());
        $this->assertEquals(self::USERNAME, $entry->getUsername());
    }

    public function testMakeFromLineSetsMessage(): void
    {
        $entry = Entry::makeFromLine($this->getLine());
        $this->assertEquals(self::MESSAGE, $entry->getMessage());
    }

    public function testLineIsCorrect(): void
    {
        $entry = new Entry();
        $entry->setTimestamp(self::TIMESTAMP);
        $entry->setUsername(self::USERNAME);
        $entry->setMessage(self::MESSAGE);
        $this->assertEquals($this->getLine(), $entry->getLine());
    }

    protected function getLine(): string
    {
        return self::TIMESTAMP . "\t" . self::USERNAME . "\t" . self::MESSAGE;
    }
}
