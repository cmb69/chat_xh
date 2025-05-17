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
        if ($request->get("chat_ajax") && $request->get("chat_room") === $room->getName()) {
            return $this->handleAjaxRequest($request, $room);
        }
        if ($room->isExpired()) {
            $room->purge();
        }
        if ($request->get("chat_room") === $room->getName()) {
            if (!$this->appendMessage($request, $room)) {
                return Response::create($this->view->message("fail", "error_save"));
            }
        }
        $this->emitJS($request);
        return Response::create($this->mainView($request, $room));
    }

    private function handleAjaxRequest(Request $request, Room $room): Response
    {
        if ($room->isExpired()) {
            $room->purge();
        }
        if ($request->get("chat_ajax") === "write") {
            $this->appendMessage($request, $room);
            // TODO handle failure to append
        }
        return Response::create($this->messagesView($request, $room))
            ->withContentType("Content-Type: text/html; charset=UTF-8");
    }

    private function emitJS(Request $request): void
    {
        global $bjs;
        static $again = false;

        if (!$again) {
            $again = true;
            $config = [
                'url' => $request->url()->relative(),
                'interval' => max(1000 * (int) $this->conf["interval_poll"], 1)
            ];
            $bjs .= '<script type="text/javascript">var CHAT = '
                . json_encode($config) . ';</script>'
                . '<script type="text/javascript" src="'
                . $this->pluginFolder . 'chat.js"></script>' . "\n";
        }
    }

    /** @todo Handle Ajax submission errors. */
    private function appendMessage(Request $request, Room $room): bool
    {
        if ($request->post("chat_message") === null) {
            return true;
        }
        $entry = new Entry();
        $entry->setTimestamp($request->time());
        $entry->setUsername($request->username() ?? "");
        $entry->setMessage($request->post("chat_message"));
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
            "url" => $request->url()->with("chat_room", $room->getName())->relative(),
            "messages" => $this->messagesView($request, $room),
        ]);
    }
}
