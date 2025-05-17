<?php

namespace Chat\Model;

use PHPUnit\Framework\TestCase;

class EntryTest extends TestCase
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

    private function getLine(): string
    {
        return self::TIMESTAMP . "\t" . self::USERNAME . "\t" . self::MESSAGE;
    }
}
