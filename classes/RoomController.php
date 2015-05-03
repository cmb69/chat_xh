<?php

/**
 * The chat room controllers.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Chat
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Chat_XH
 */

/**
 * The chat room controllers.
 *
 * @category CMSimple_XH
 * @package  Chat
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chat_XH
 */
class Chat_RoomController extends Chat_AbstractController
{
    /**
     * Handles the chat room and returns its view.
     *
     * @param string $roomname A chat room name.
     *
     * @return string (X)HTML.
     *
     * @global array  The localization of the plugins.
     */
    public function handle($roomname)
    {
        global $plugin_tx;

        if (!Chat_Room::isValidName($roomname)) {
            return XH_message('fail', $plugin_tx['chat']['error_room_name']);
        }
        $room = new Chat_Room($roomname);
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
        return $this->mainView($room) . $this->emitJS($roomname);
    }

    /**
     * Returns an error message that a room is not writable.
     *
     * @param Chat_Room $room A chat room.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    protected function reportUnwritability(Chat_Room $room)
    {
        global $plugin_tx;

        return XH_message(
            'fail',
            sprintf(
                $plugin_tx['chat']['error_not_writable'],
                defined('XH_ADM') && XH_ADM ? $room->getFilename() : ''
            )
        );
    }

    /**
     * Handles Ajax requests.
     *
     * @param Chat_Room $room A chat room.
     *
     * @return void
     */
    protected function handleAjaxRequest(Chat_Room $room)
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

    /**
     * Returns the name of the currently logged in user, if any, false otherwise.
     *
     * This is meant to work with the Register and the Memberpages plugin.
     *
     * @return string
     */
    protected function currentUser()
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

    /**
     * Returns the necessary scripts to handle a chat room.
     * If $bjs is available, the scripts are appended to it,
     * and an empty string is returned.
     *
     * @param string $room A chat room name.
     *
     * @return string (X)HTML.
     *
     * @global array  The paths of system files and folders.
     * @global string The name of the site.
     * @global string The page URL.
     * @global string The scripts that should be written before the closing body tag.
     * @global array  The configuration of the plugins.
     *
     * @staticvar bool $again Whether the scripts have already been written.
     */
    protected function emitJS($room)
    {
        global $pth, $sn, $su, $bjs, $plugin_cf;
        static $again = false;

        $o = '';
        if (!$again) {
            $o .= '<script type="text/javascript" src="' . $pth['folder']['plugins']
                . 'chat/chat.js"></script>' . "\n";
            $again = true;
        }
        $url = $sn . '?' . $su;
        $interval = max(1000 * intval($plugin_cf['chat']['interval_poll']), 1);
        $o .= '<script type="text/javascript">'
            . "new CHAT.Widget('$room', '$url', $interval);"
            . "</script>\n";
        if (isset($bjs)) {
            $bjs .= $o;
            return '';
        } else {
            return $o;
        }
    }

    /**
     * Appends the posted message to the data file.
     *
     * @param Chat_Room $room A chat room.
     *
     * @return void
     *
     * @todo Handle Ajax submission errors.
     */
    protected function appendMessage(Chat_Room $room)
    {
        if (empty($_POST['chat_message'])) {
            return;
        }
        $entry = new Chat_Entry();
        $entry->setTimestamp(time());
        $entry->setUsername($this->currentUser());
        $entry->setMessage(stsl($_POST['chat_message']));
        $room->appendEntry($entry);
    }

    /**
     * Returns a message prepared as bag for the view.
     *
     * @param Chat_Entry $entry A chat entry.
     *
     * @return array
     *
     * @global array The localization of the plugins.
     */
    protected function message(Chat_Entry $entry)
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

    /**
     * Returns the view of the history of a chat room.
     *
     * @param Chat_Room $room A chat room.
     *
     * @return string (X)HTML.
     */
    protected function messagesView(Chat_Room $room)
    {
        $messages = array_map(array($this, 'message'), $room->findEntries());
        return $this->view('messages', compact('messages'));
    }

    /**
     * Returns the complete view of the chat room.
     *
     * @param Chat_Room $room A chat room.
     *
     * @return string (X)HTML.
     *
     * @global string The script name.
     * @global string The URL of the requested page.
     * @global array  The localization of the plugins.
     */
    protected function mainView(Chat_Room $room)
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

?>
