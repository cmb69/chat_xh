<?php

namespace Chat;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function testMakesRoomController(): void
    {
        $this->assertInstanceOf(RoomController::class, Dic::roomController());
    }

    public function testMakesInfoCommand(): void
    {
        $this->assertInstanceOf(InfoCommand::class, Dic::infoCommand());
    }
}
