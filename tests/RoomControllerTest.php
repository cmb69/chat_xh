<?php

namespace Chat;

use PHPUnit\Framework\TestCase;

class RoomControllerTest extends TestCase
{
    /** @var RoomController */
    protected $subject;

    /** @var object */
    protected $messageMock;

    public function setUp(): void
    {
        global $plugin_tx;
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->subject = new RoomController();
    }

    public function testInvalidRoomNameReturnsFailureMessage(): void
    {
        $response = $this->subject->handle('te$t');
        $this->assertStringContainsString("Invalid chat room name:", $response);
    }
}
