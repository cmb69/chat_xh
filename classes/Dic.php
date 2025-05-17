<?php

namespace Chat;

class Dic
{
    public static function roomController(): RoomController
    {
        return new RoomController();
    }

    public static function infoCommand(): InfoCommand
    {
        return new InfoCommand();
    }
}
