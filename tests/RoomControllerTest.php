<?php

namespace Chat;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Plib\View;

class RoomControllerTest extends TestCase
{
    /** @var array<string,string> */
    private $conf;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        global $pth, $plugin_tx;
        vfsStream::setup();
        $pth = ["folder" => ["content" => vfsStream::url("root/"), "plugins" => "../"]];
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["chat"];
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->view = new View("./views/", $plugin_tx["chat"]);
    }

    private function sut(): RoomController
    {
        return new RoomController($this->conf, $this->view);
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
