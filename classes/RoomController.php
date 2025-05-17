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

use Plib\View;

class RoomController extends AbstractController
{
    /** @var View */
    private $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function handle(string $roomname, int $purgeInterval = null): string
    {
        global $plugin_cf;

        if (!Room::isValidName($roomname)) {
            return $this->view->message("fail", "error_room_name");
        }
        if (!isset($purgeInterval)) {
            $purgeInterval = $plugin_cf['chat']['interval_purge'];
        }
        $room = new Room($roomname, $purgeInterval);
        if (!$room->isWritable()) {
            return $this->reportUnwritability($room);
        }
        if (isset($_GET['chat_ajax']) && $_GET['chat_room'] == $room->getName()) {
            $this->handleAjaxRequest($room);
        }
        if ($room->isExpired()) {
            $room->purge();
        }
        if (isset($_GET['chat_room']) && $_GET['chat_room'] == $room->getName()) {
            $this->appendMessage($room);
        }
        $this->emitJS();
        return $this->mainView($room);
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

    private function handleAjaxRequest(Room $room): void
    {
        if ($room->isExpired()) {
            $room->purge();
        }
        switch ($_GET['chat_ajax']) {
            case 'write':
                $this->appendMessage($room);
                // FALLTHROUGH
            case 'read':
                header('Content-Type: text/html; charset=UTF-8');
                echo $this->messagesView($room);
                exit;
        }
    }

    private function currentUser(): string
    {
        if (session_id() == '') {
            session_start();
        }
        return isset($_SESSION['username'])
            ? $_SESSION['username'] // Register and Memberpages >= 3
            : (isset($_SESSION['Name'])
                ? $_SESSION['Name'] // Memberpages < 3
                : false);
    }

    private function emitJS(): void
    {
        global $pth, $sn, $su, $bjs, $plugin_cf;
        static $again = false;

        if (!$again) {
            $again = true;
            $config = array(
                'url' => $sn . '?' . $su,
                'interval' => max(1000 * $plugin_cf['chat']['interval_poll'], 1)
            );
            $bjs .= '<script type="text/javascript">var CHAT = '
                . json_encode($config) . ';</script>'
                . '<script type="text/javascript" src="'
                . $pth['folder']['plugins'] . 'chat/chat.js"></script>' . "\n";
        }
    }

    /** @todo Handle Ajax submission errors. */
    private function appendMessage(Room $room): void
    {
        if (empty($_POST['chat_message'])) {
            return;
        }
        $entry = new Entry();
        $entry->setTimestamp(time());
        $entry->setUsername($this->currentUser());
        $entry->setMessage(stsl($_POST['chat_message']));
        $room->appendEntry($entry);
    }

    private function message(Entry $entry): array
    {
        global $plugin_tx;

        $ptx = $plugin_tx['chat'];
        if (!$entry->getUsername()) {
            $user = $ptx['user_unknown'];
            $class = '';
        } elseif ($entry->getUsername() == $this->currentUser()) {
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
            'text' => XH_hsc($entry->getMessage())
        );
    }

    private function messagesView(Room $room): string
    {
        $messages = array_map(array($this, 'message'), $room->findEntries());
        return $this->view('messages', compact('messages'));
    }

    private function mainView(Room $room): string
    {
        global $sn, $su, $plugin_tx;

        $url = "$sn?$su&amp;chat_room=" . $room->getName();
        $inputs = tag('input type="text" name="chat_message"');
        $inputs .= tag(
            'input type="submit" class="submit" value="'
            . $plugin_tx['chat']['label_send'] . '"'
        );
        $bag = array(
            'room' => $room->getName(),
            'inputs' => $inputs,
            'url' => $url,
            'messages' => $this->messagesView($room)
        );
        return $this->view('chat', $bag);
    }
}
