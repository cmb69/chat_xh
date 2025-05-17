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

use Chat\Model\Entry;
use Chat\Model\Room;
use Plib\Request;
use Plib\Response;
use Plib\View;

class RoomController
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $conf;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(
        string $pluginFolder,
        array $conf,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
        $this->view = $view;
    }

    public function handle(string $roomname, ?int $purgeInterval, Request $request): Response
    {
        if (!Room::isValidName($roomname)) {
            return Response::create($this->view->message("fail", "error_room_name"));
        }
        if (!isset($purgeInterval)) {
            $purgeInterval = (int) $this->conf["interval_purge"];
        }
        $room = new Room($roomname, $purgeInterval);
        if ($room->isExpired()) {
            $room->purge();
        }
        if ($request->header("X-CMSimple-XH-Request") === "chat-{$roomname}") {
            return $this->handleAjaxRequest($request, $room);
        }
        if ($request->post("chat_room") === $roomname && $request->post("chat_message") !== null) {
            if (!$this->appendMessage($request, $room)) {
                return Response::create($this->view->message("fail", "error_save"));
            }
            return Response::redirect($request->url()->absolute());
        }
        return Response::create($this->mainView($request, $room));
    }

    private function handleAjaxRequest(Request $request, Room $room): Response
    {
        if ($request->post("chat_message") !== null) {
            $this->appendMessage($request, $room);
            // TODO handle failure to append
        }
        return Response::create($this->messagesView($request, $room))
            ->withContentType("Content-Type: text/html; charset=UTF-8");
    }

    /** @todo Handle Ajax submission errors. */
    private function appendMessage(Request $request, Room $room): bool
    {
        assert($request->post("chat_message") !== null);
        $entry = new Entry($request->time(), $request->username() ?? "", $request->post("chat_message"));
        return $room->appendEntry($entry);
    }

    /** @return object{class:string,user:string,text:string} */
    private function message(Request $request, Entry $entry)
    {
        if (!$entry->getUsername()) {
            $user = $this->view->plain("user_unknown");
            $class = '';
        } elseif ($entry->getUsername() == $request->username()) {
            $user = $this->view->plain("user_self");
            $class = 'chat_self';
        } else {
            $user = $entry->getUsername();
            $class = '';
        }
        $user = strtr($this->view->plain("format_user"), [
            "{USER}" => $user,
            "{DATE}" => date($this->view->plain("format_date"), $entry->getTimestamp()),
            "{TIME}" => date($this->view->plain("format_time"), $entry->getTimestamp()),
        ]);
        return (object) [
            'class' => $class,
            'user' => $user,
            'text' => $entry->getMessage(),
        ];
    }

    private function messagesView(Request $request, Room $room): string
    {
        $entries = $room->findEntries();
        $messages = array_map([$this, 'message'], array_fill(0, count($entries), $request), $entries);
        return $this->view->render('messages', compact('messages'));
    }

    private function mainView(Request $request, Room $room): string
    {
        return $this->view->render("chat", [
            "room" => $room->getName(),
            "url" => $request->url()->relative(),
            "messages" => $this->messagesView($request, $room),
            "script" => $this->pluginFolder . "chat.js",
            "config" => [
                "url" => $request->url()->relative(),
                "interval" => max(1, 1000 * (int) $this->conf["interval_poll"])
            ],
        ]);
    }
}
