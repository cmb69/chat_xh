<?php

namespace Chat;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Plib\View;

class RoomControllerTest extends TestCase
{
    /** @var View */
    private $view;

    public function setUp(): void
    {
        global $plugin_tx;
        vfsStream::setup();
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->view = new View("./views/", $plugin_tx["chat"]);
    }

    private function sut(): RoomController
    {
        return new RoomController($this->view);
    }

    public function testInvalidRoomNameReturnsFailureMessage(): void
    {
        $response = $this->sut()->handle('te$t');
        $this->assertStringContainsString("Invalid chat room name:", $response);
    }

    public function testIt(): void
    {
        $response = $this->sut()->handle("chat");
    }
}
