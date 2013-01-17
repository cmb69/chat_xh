<?php

/**
 * Front-end of Chat_XH.
 *
 * @package	Chat
 * @copyright	Copyright (c) 2012-2013 Christoph M. Becker <http://3-magi.net/>
 * @license	http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version     $Id$
 * @link	http://3-magi.net/?CMSimple_XH/Chat_XH
 */


/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * The version of the plugin.
 */
define('CHAT_VERSION', '1beta2');


/**
 * Start session.
 */
if (session_id() == '') {
    session_start();
}


/**
 * Returns the path of the data folder.
 *
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the plugins.
 * @return string
 */
function Chat_dataFolder()
{
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['chat'];

    if ($pcf['folder_data'] == '') {
	$fn = $pth['folder']['plugins'] . 'chat/data/';
    } else {
	$fn = $pth['folder']['base'] . $pcf['folder_data'];
    }
    if (substr($fn, -1) != '/') {
	$fn .= '/';
    }
    if (file_exists($fn)) {
	if (!is_dir($fn)) {
	    e('cntopen', 'folder', $fn);
	}
    } else {
	// $recursive parameter only since PHP 5.0.0;
	// should do no harm for older versions, however.
	if (!mkdir($fn, 0777, true)) {
	    e('cntwriteto', 'folder', $fn);
	}
    }
    return $fn;
}


/**
 * Returns the path of a chat room data file.
 *
 * @param $room  The name of the chat room.
 * @return string
 */
function Chat_dataFile($room)
{
    return Chat_dataFolder() . $room . '.dat';
}


/**
 * Returns the name of the currently logged in user, if any, false otherwise.
 *
 * @return string
 */
function Chat_currentUser()
{
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
 * @global array  The paths of system files and folders.
 * @global string  The name of the site.
 * @global string  The query string of the current page.
 * @global string  The scripts that should be written before the closing body tag.
 * @global array  The configuration of the plugins.
 * @param  string $room  The name of the chat room.
 * @return string
 */
function Chat_JS($room)
{
    global $pth, $sn, $su, $bjs, $plugin_cf;
    static $again = false;

    $o = '';
    if (!$again) {
	$o .= '<script type="text/javascript" src="' . $pth['folder']['plugins']
	    . 'chat/chat.min.js"></script>' . "\n";
	$again = true;
    }
    $url = $sn.'?'.$su;
    $interval = max(1000 * intval($plugin_cf['chat']['interval_poll']), 1);
    $o .= '<script type="text/javascript">'
	. "new Chat('$room', '$url', $interval);"
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
 * @param  string $room  The name of the chat room.
 * @return void
 */
function Chat_appendMessage($room)
{ // TODO: handle Ajax submission errors!
    if (empty($_POST['chat_message'])) {
	return;
    }
    $rec = array(time(), Chat_currentUser(), stsl($_POST['chat_message']));
    $line = implode("\t", $rec) . "\n";
    $fn = Chat_dataFile($room);
    if (($fp = fopen($fn, 'a')) === false
	|| fwrite($fp, $line) === false)
    {
	e('cntwriteto', 'file', $fn);
    }
    if ($fp !== false) {
	fclose($fp);
    }
}


/**
 * Returns a message prepared as bag for the view.
 *
 * @global array  The localization of the plugins.
 * @param  int $time  The timestamp of the message.
 * @param  string $user  The user name.
 * @param  string $msg  The message text.
 * @return array
 */
function Chat_message($time, $user, $msg)
{
    global $plugin_tx;

    $ptx = $plugin_tx['chat'];
    if (!$user) {
	$user = $ptx['user_unknown'];
	$class = '';
    } elseif ($user == Chat_currentUser()) {
	$user = $ptx['user_self'];
	$class = 'chat_self';
    } else {
	$class = '';
    }
    $trans = array('{USER}' => $user,
		'{DATE}' => date($ptx['format_date'], $time),
		'{TIME}' => date($ptx['format_time'], $time));
    $user = strtr($ptx['format_user'], $trans);
    return array('class' => $class,
		 'user' => $user,
		 'text' => htmlspecialchars($msg, ENT_COMPAT, 'UTF-8'));
}


/**
 * Returns the view of an instantiated template.
 *
 * @global array  The paths of system files and folders.
 * @param string $template  The name of the template.
 * @param array $bag  The data for the view.
 * @return string
 */
function Chat_view($template, $bag)
{
    global $pth;

    extract($bag);
    ob_start();
    include $pth['folder']['plugins'] . 'chat/views/' . $template . '.htm';
    return ob_get_clean();
}


/**
 * Returns the view of the history of a chat room.
 *
 * @global array  The configuration of the plugins.
 * @param  string $room  The name of the chat room.
 * @return string  The (X)HTML.
 */
function Chat_messagesView($room)
{
    global $plugin_cf;

    $pcf = $plugin_cf['chat'];
    $fn = Chat_dataFile($room);
    $currentTime = time();
    $messages = array();
    if (file_exists($fn)
	&& ($lines = file($fn)) !== false)
    {
	foreach ($lines as $line) {
	    if (!empty($line)) {
		list($time, $user, $msg) = explode("\t", rtrim($line), 3);
		// The following if clause allows to hide messages,
		// that are older than interval_purge.
		//if (!$pcf['interval_purge']
		//    || $currentTime <= $time + $pcf['interval_purge'])
		//{
		    $messages[] = Chat_message($time, $user, $msg);
		//}
	    }
	}
    }
    $bag = array('messages' => $messages);
    return Chat_view('messages', $bag);
}


/**
 * Returns the complete view of the chat room.
 *
 * @global string  The name of the site.
 * @global string  The query string of the current page.
 * @global array  The localization of the plugins
 * @param  string $room  The name of the chat room.
 * @return string  The (X)HTML.
 */
function Chat_mainView($room)
{
    global $sn, $su, $plugin_tx;

    $url = "$sn?$su&amp;chat_room=$room";
    $inputs = tag('input type="text" name="chat_message"')
	. tag('input type="submit" class="submit" value="'
	      . $plugin_tx['chat']['label_send'] . '"');
    $bag = array('room' => $room,
		 'inputs' => $inputs,
		 'url' => $url,
		 'messages' => Chat_messagesView($room));
    return Chat_view('chat', $bag);
}


/**
 * Purges a chat room file after inactive interval has elapsed.
 *
 * @param  string $room  The name of the chat room.
 * @return void
 */
function Chat_purge($room)
{
    global $plugin_cf;

    $fn = Chat_dataFile($room);
    if (file_exists($fn)
	&& $plugin_cf['chat']['interval_purge']
	&& time() > filemtime($fn) + $plugin_cf['chat']['interval_purge'])
    {
	unlink($fn);
    }
}

/**
 * Handles the chat room and returns its (X)HTML view.
 *
 * @access public
 * @global string  The error messages.
 * @global array  The localization of the plugins.
 * @param  string $room  The name of the chat room.
 * @return string
 */
function Chat($room)
{
    global $e, $plugin_tx;

    if (!preg_match('/^[a-z0-9-]*$/u', $room)) {
	$e .= '<li><strong>' . $plugin_tx['chat']['error_room_name']
	    . '</strong></li>';
	return false;
    }
    if (session_id() == '') {
        session_start();
    }
    // necessary to prevent unauthorized access to protected chat rooms
    $_SESSION['chat_rooms'][$room] = true;
    Chat_purge($room);
    if (isset($_GET['chat_room']) && $_GET['chat_room'] == $room) {
	Chat_appendMessage($room);
    }
    return Chat_mainView($room) . Chat_JS($room);
}


/**
 * Respond to Ajax requests.
 */
if (isset($_GET['chat_ajax'])) {
    if (session_id() == '') {
        session_start();
    }
    // Check if user has accessed the page with chat room before.
    // TODO: better: ask Register/Memberpages if user is authorized to access this page.
    if (!empty($_SESSION['chat_rooms'][$_GET['chat_room']])) {
	Chat_purge($_GET['chat_room']);
	switch ($_GET['chat_ajax']) {
	case 'write':
	    Chat_appendMessage($_GET['chat_room']);
	    // FALLTHROUGH
	case 'read':
	    echo Chat_messagesView($_GET['chat_room']);
	    exit;
	}
    }
}

?>
