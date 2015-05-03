<?php

/**
 * The chat controllers.
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
 * The chat controllers.
 *
 * @category CMSimple_XH
 * @package  Chat
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chat_XH
 */
class Chat_Controller
{
    /**
     * Dispatches on plugin related requests.
     *
     * @return void
     */
    public function dispatch()
    {
        if (XH_ADM) {
            if (function_exists('XH_registerStandardPluginMenuItems')) {
                XH_registerStandardPluginMenuItems(false);
            }
            if ($this->wantsPluginAdministration()) {
                $this->handleAdministration();
            }
        }
    }

    /**
     * Returns whether the plugin administration is requested.
     *
     * @return bool
     *
     * @global string Whether the chat administration is requested.
     */
    protected function wantsPluginAdministration()
    {
        global $chat;

        return function_exists('XH_wantsPluginAdministration')
            && XH_wantsPluginAdministration('chat')
            || isset($chat) && $chat == 'true';
    }

    /**
     * Handle the plugin administration.
     *
     * @return void
     *
     * @global string The (X)HTML of the contents area.
     * @global string The value of the admin GP parameter.
     * @global string The value of the action GP parameter.
     */
    protected function handleAdministration()
    {
        global $o, $admin, $action;

        $o .= print_plugin_admin('off');
        switch($admin) {
        case '':
            $o .= $this->aboutView() . tag('hr') . $this->systemCheck();
            break;
        default:
            $o .= plugin_admin_common($action, $admin, 'chat');
        }
    }

    /**
     * Returns the plugin's about view.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.     *
     */
    protected function aboutView()
    {
        global $pth, $plugin_tx;

        $icon = tag(
            'img class="chat_logo" src="' . $pth['folder']['plugins']
            . 'chat/chat.png" alt="' . $plugin_tx['chat']['alt_logo'] . '"'
        );
        $bag = array(
            'heading' => 'Chat &ndash; Info',
            'icon' => $icon,
            'version' => CHAT_VERSION
        );
        return $this->view('about', $bag);
    }

    /**
     * Returns the requirements information view.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the core.
     * @global array The localization of the plugins.
     */
    protected function systemCheck()
    {
        global $pth, $tx, $plugin_tx;

        $phpVersion = '5.1.2';
        $ptx = $plugin_tx['chat'];
        $imgdir = $pth['folder']['plugins'] . 'chat/images/';
        $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
        $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
        $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
        $o = '<h4>' . $ptx['syscheck_title'] . '</h4>'
            . (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
            . tag('br');
        foreach (array('pcre', 'session') as $ext) {
            $o .= (extension_loaded($ext) ? $ok : $fail)
                . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext)
                . tag('br');
        }
        $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
            . '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes'] . tag('br');
        $o .= tag('br')
            . (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
            . '&nbsp;&nbsp;' . $ptx['syscheck_encoding'] . tag('br');
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'chat/' . $folder;
        }
        $folders[] = Chat_Room::dataFolder();
        foreach ($folders as $folder) {
            $o .= (is_writable($folder) ? $ok : $warn)
                . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
                . tag('br');
        }
        return $o;
    }

    /**
     * Handles the chat room and returns its view.
     *
     * @param string $roomname A chat room name.
     *
     * @return string (X)HTML.
     *
     * @global array  The localization of the plugins.
     */
    public function main($roomname)
    {
        global $plugin_tx;

        if (!Chat_Room::isValidName($roomname)) {
            return XH_message('fail', $plugin_tx['chat']['error_room_name']);
        }
        $room = new Chat_Room($roomname);
        if (!$room->isWritable()) {
            return XH_message(
                'fail',
                sprintf(
                    $plugin_tx['chat']['error_not_writable'],
                    XH_ADM ? $room->getFilename() : ''
                )
            );
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

    /**
     * Returns the view of an instantiated template.
     *
     * @param string $template The name of the template.
     * @param array  $bag      The data for the view.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     */
    protected function view($template, $bag)
    {
        global $pth;

        extract($bag);
        ob_start();
        include $pth['folder']['plugins'] . 'chat/views/' . $template . '.htm';
        return ob_get_clean();
    }
}

?>
