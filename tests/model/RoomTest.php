<?php

namespace Chat\Model;

use PHPUnit\Framework\TestCase;

/** @small */
class RoomTest extends TestCase
{
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

    public function testPurgesIfExpired(): void
    {
        $room = new Room("expired");
        $room->postMessage(strtotime("2025-05-18T09:09:28+00:00"), "cmb", "yada yada");
        $room->purgeIfExpired(strtotime("2025-05-18T09:09:29+00:00"));
        $this->assertEmpty($room->messages());
    }
}
