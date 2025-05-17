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

use Plib\Request;
use Plib\View;

class RoomController
{
    /** @var array<string,string> */
    private $conf;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(array $conf, View $view)
    {
        $this->conf = $conf;
        $this->view = $view;
    }

    public function handle(string $roomname, ?int $purgeInterval, Request $request): string
    {
        if (!Room::isValidName($roomname)) {
            return $this->view->message("fail", "error_room_name");
        }
        if (!isset($purgeInterval)) {
            $purgeInterval = $this->conf["interval_purge"];
        }
        $room = new Room($roomname, $purgeInterval);
        if (!$room->isWritable()) {
            return $this->reportUnwritability($room);
        }
        if (isset($_GET['chat_ajax']) && $_GET['chat_room'] == $room->getName()) {
            $this->handleAjaxRequest($request, $room);
        }
        if ($room->isExpired()) {
            $room->purge();
        }
        if (isset($_GET['chat_room']) && $_GET['chat_room'] == $room->getName()) {
            $this->appendMessage($request, $room);
        }
        $this->emitJS($request);
        return $this->mainView($request, $room);
    }

    private function reportUnwritability(Room $room): string
    {
        global $plugin_tx;

        return $this->view->message(
            'fail',
            sprintf(
                $plugin_tx['chat']['error_not_writable'],
                defined('XH_ADM') && XH_ADM ? $room->getFilename() : ''
            )
        );
    }

    private function handleAjaxRequest(Request $request, Room $room): void
    {
        if ($room->isExpired()) {
            $room->purge();
        }
        switch ($_GET['chat_ajax']) {
            case 'write':
                $this->appendMessage($request, $room);
                // FALLTHROUGH
            case 'read':
                header('Content-Type: text/html; charset=UTF-8');
                echo $this->messagesView($request, $room);
                exit;
        }
    }

    private function emitJS(Request $request): void
    {
        global $pth, $bjs;
        static $again = false;

        if (!$again) {
            $again = true;
            $config = array(
                'url' => $request->url()->relative(),
                'interval' => max(1000 * (int) $this->conf["interval_poll"], 1)
            );
            $bjs .= '<script type="text/javascript">var CHAT = '
                . json_encode($config) . ';</script>'
                . '<script type="text/javascript" src="'
                . $pth['folder']['plugins'] . 'chat/chat.js"></script>' . "\n";
        }
    }

    /** @todo Handle Ajax submission errors. */
    private function appendMessage(Request $request, Room $room): void
    {
        if (empty($_POST['chat_message'])) {
            return;
        }
        $entry = new Entry();
        $entry->setTimestamp(time());
        $entry->setUsername($request->username());
        $entry->setMessage($_POST['chat_message']);
        $room->appendEntry($entry);
    }

    /** @return array{class:string,user:string,text:string} */
    private function message(Request $request, Entry $entry): array
    {
        global $plugin_tx;

        $ptx = $plugin_tx['chat'];
        if (!$entry->getUsername()) {
            $user = $ptx['user_unknown'];
            $class = '';
        } elseif ($entry->getUsername() == $request->username()) {
            $user = $ptx['user_self'];
            $class = 'chat_self';
        } else {
            $user = $entry->getUsername();
            $class = '';
        }
        $trans = array(
            '{USER}' => $user,
            '{DATE}' => date($ptx['format_date'], $entry->getTimestamp()),
            '{TIME}' => date($ptx['format_time'], $entry->getTimestamp())
        );
        $user = strtr($ptx['format_user'], $trans);
        return array(
            'class' => $class,
            'user' => $user,
            'text' => $entry->getMessage(),
        );
    }

    private function messagesView(Request $request, Room $room): string
    {
        $entries = $room->findEntries();
        $messages = array_map(array($this, 'message'), array_fill(0, count($entries), $request), $entries);
        return $this->view->render('messages', compact('messages'));
    }

    private function mainView(Request $request, Room $room): string
    {
        $url = $request->url()->with("chat_room", $room->getName());
        $bag = array(
            'room' => $room->getName(),
            'url' => $url->relative(),
            'messages' => $this->messagesView($request, $room)
        );
        return $this->view->render('chat', $bag);
    }
}
