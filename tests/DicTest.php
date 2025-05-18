<?php

namespace Chat;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_cf, $plugin_tx;
        $pth = ["folder" => ["content" => "", "plugins" => ""]];
        $plugin_cf = ["chat" => []];
        $plugin_tx = ["chat" => []];
    }

    public function testMakesRoomController(): void
    {
        $this->assertInstanceOf(RoomController::class, Dic::roomController());
    }

    public function testMakesInfoCommand(): void
    {
        $this->assertInstanceOf(InfoCommand::class, Dic::infoCommand());
    }
}
