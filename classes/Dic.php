<?php

namespace Chat;

use Plib\View;

class Dic
{
    public static function roomController(): RoomController
    {
        return new RoomController();
    }

    public static function infoCommand(): InfoCommand
    {
        return new InfoCommand(self::view());
    }

    private static function view(): View
    {
        global $pth, $plugin_tx;
        return new View($pth["folder"]["plugins"] . "chat/views/", $plugin_tx["chat"]);
    }
}
