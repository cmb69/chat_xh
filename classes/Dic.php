<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Chat_XH.
 *
 * Chat_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Chat_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Chat_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Chat;

use Plib\CsrfProtector;
use Plib\DocumentStore;
use Plib\SystemChecker;
use Plib\View;

class Dic
{
    public const VERSION = "2.1-dev";

    public static function roomController(): RoomController
    {
        global $pth, $plugin_cf;
        return new RoomController(
            $pth["folder"]["plugins"] . "chat/",
            $plugin_cf["chat"],
            new DocumentStore(self::contentFolder()),
            new CsrfProtector(),
            self::view()
        );
    }

    public static function infoCommand(): InfoCommand
    {
        global $pth;
        return new InfoCommand(
            $pth["folder"]["plugins"] . "chat/",
            new SystemChecker(),
            new DocumentStore(self::contentFolder()),
            self::view()
        );
    }

    private static function contentFolder(): string
    {
        global $pth;
        return $pth["folder"]["content"] . "chat/";
    }

    private static function view(): View
    {
        global $pth, $plugin_tx;
        return new View($pth["folder"]["plugins"] . "chat/views/", $plugin_tx["chat"]);
    }
}
