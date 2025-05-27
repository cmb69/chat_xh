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

use Chat\Model\Message;
use Chat\Model\Room;
use Plib\DocumentStore;
use Plib\Request;
use Plib\Response;
use Plib\View;

class RoomController
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $conf;

    /** @var DocumentStore */
    private $store;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(
        string $pluginFolder,
        array $conf,
        DocumentStore $store,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
        $this->store = $store;
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
        $expiration = $request->time() - $purgeInterval;
        if ($this->posting($request, $roomname)) {
            return $this->create($request, $roomname, $expiration);
        }
        return $this->read($request, $roomname, $expiration);
    }

    private function posting(Request $request, string $roomname): bool
    {
        return $request->post("chat_message") !== null
            && ($request->header("X-CMSimple-XH-Request") === "chat-{$roomname}"
            || $request->post("chat_room") === $roomname);
    }

    private function read(Request $request, string $roomname, int $expiration): Response
    {
        $room = Room::retrieve($roomname, $this->store);
        $room->purgeIfExpired($expiration);
        if ($request->header("X-CMSimple-XH-Request") === "chat-$roomname") {
            return Response::create($this->mainView($request, $room))
                ->withContentType("Content-Type: text/html; charset=UTF-8");
        }
        return Response::create($this->mainView($request, $room));
    }

    private function create(Request $request, string $roomname, int $expiration): Response
    {
        assert($request->post("chat_message") !== null);
        $room = Room::update($roomname, $this->store);
        $room->purgeIfExpired($expiration);
        $errors = [];
        $message = $room->postMessage($request->time(), $request->username() ?? "", $request->post("chat_message"));
        if ($message === null) {
            $errors[] = $this->view->message("fail", "error_invalid_post");
            $this->store->rollback();
        }
        if (!$errors && !$this->store->commit()) {
            $errors[] = $this->view->message("fail", "error_save");
        }
        if ($request->header("X-CMSimple-XH-Request") !== null) {
            return Response::create($this->mainView($request, $room, $errors))
                ->withContentType("Content-Type: text/html; charset=UTF-8");
        }
        if ($errors) {
            return Response::create($this->mainView($request, $room, $errors));
        }
        return Response::redirect($request->url()->absolute());
    }

    /** @param list<string> $errors */
    private function mainView(Request $request, Room $room, array $errors = []): string
    {
        return $this->view->render("chat", [
            "room" => $room->name(),
            "url" => $request->url()->relative(),
            "errors" => $errors,
            "messages" => $this->messages($request, $room),
            "script" => $this->pluginFolder . "chat.js",
            "config" => [
                "url" => $request->url()->relative(),
                "interval" => max(1, 1000 * (int) $this->conf["interval_poll"])
            ],
        ]);
    }

    /** @return list<object{class:string,message:string}> */
    private function messages(Request $request, Room $room): array
    {
        $messages = [];
        foreach ($room->messages() as $message) {
            $messages[] = $this->message($request, $message);
        }
        return $messages;
    }

    /** @return object{class:string,message:string} */
    private function message(Request $request, Message $message)
    {
        if (!$message->username()) {
            $user = $this->view->text("user_unknown");
            $class = "";
        } elseif ($message->username() == $request->username()) {
            $user = $this->view->text("user_self");
            $class = "chat_self";
        } else {
            $user = $message->username();
            $class = "";
        }
        $date = $this->view->date("format_date", $message->timestamp());
        $time = $this->view->date("format_time", $message->timestamp());
        $text = $this->view->esc($message->text());
        return (object) [
            "class" => $class,
            "message" => strtr($this->view->plain("format_message"), [
                "{USER}" => "<span class=\"chat_user\">$user</span>",
                "{DATE}" => "<span class=\"chat_date\">$date</span>",
                "{TIME}" => "<span class=\"chat_time\">$time</span>",
                "{TEXT}" => "<span class=\"chat_text\">$text</span>",
            ]),
        ];
    }
}
