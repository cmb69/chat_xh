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
 * Returns the data folder.
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
 *
 * @global array  The paths of system files and folders.
 * @global string  The name of the site.
 * @global string  The query string of the current page.
 * @global array  The configuration of the plugins.
 * @param  string $room  The name of the chat room.
 * @return string
 */
function Chat_JS($room)
{ // TODO: use $bjs if available
    global $pth, $sn, $su, $plugin_cf;
    static $again = false;

    $o = '';
    if (!$again) {
	$o .= '<script type="text/javascript" src="' . $pth['folder']['plugins']
	    . 'chat/chat.js"></script>';
	$again = true;
    }
    $url = $sn.'?'.$su;
    $o .= '<script type="text/javascript">'
	. "new Chat('$room', '$url', {$plugin_cf['chat']['interval_poll']});"
	. '</script>';
    return $o;
}


/**
 * Appends the posted message to the data file.
 *
 * @param  string $room  The name of the chat room.
 * @return void
 */
function Chat_appendMessage($room)
{ // TODO: handle Ajax submission!
    if (empty($_POST['chat_message'])) {
	return;
    }
    $line = Chat_currentUser() . "\t" . stsl($_POST['chat_message']) . "\n";
    $fn = Chat_dataFolder() . $room . '.dat';
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
 * Returns a data line (i.e. record) as (X)HTML.
 *
 * @global array  The configuration of the plugins.
 * @global array  The localization of the plugins.
 * @param  string $line
 * @return string
 */
function Chat_message($line)
{
    global $plugin_cf, $plugin_tx;

    $pcf = $plugin_cf['chat'];
    $ptx = $plugin_tx['chat'];
    list($user, $msg) = explode("\t", rtrim($line), 2);
    if (!$user) {
	$user = $ptx['user_unknown'];
	$self = '';
    } elseif ($user == Chat_currentUser()) {
	$user = $ptx['user_self'];
	$self = ' chat_self';
    } else {
	$self = '';
    }
    return '<div class="chat_message' . $self . '">'
	. '<span class="chat_user">' . sprintf($pcf['format_user'], $user) . '</span>'
	. '<span class="chat_message">' . htmlspecialchars($msg, ENT_COMPAT, 'UTF-8') . '</span>'
	. '</div>';
}


/**
 * Returns the history of the chat room as (X)HTML.
 *
 * @global array  The configuration of the plugins.
 * @param  string $room  The name of the chat room.
 * @return string
 */
function Chat_messages($room)
{
    global $plugin_cf;

    $fn = Chat_dataFolder() . $room . '.dat';
    if (file_exists($fn)
	&& time() > filemtime($fn) + $plugin_cf['chat']['interval_purge'])
    {
	unlink($fn);
    }
    $o = '';
    if (($lines = file($fn)) !== false) {
	foreach ($lines as $line) {
	    if (!empty($line)) {
		$o .= Chat_message($line);
	    }
	}
    }
    return $o;
}


/**
 * Returns the complete (X)HTML view of the chat room.
 *
 * @global string  The name of the site.
 * @global string  The query string of the current page.
 * @global array  The localization of the plugins
 * @param  string $room  The name of the chat room.
 * @return string
 */
function Chat_view($room)
{
    global $sn, $su, $plugin_tx;

    $url = "$sn?$su&amp;chat_room=$room";
    $o = '<div id="chat_room_' . $room . '" class="chat_room">'
	. '<div class="chat_messages">' . Chat_messages($room) . '</div>'
	. '<form action="' . $url . '" method="POST">'
	. tag('input type="text" name="chat_message"')
	. tag('input type="submit" class="submit" value="'
	      . $plugin_tx['chat']['label_send'] . '"')
	. '</form>'
	. '</div>';
    return $o;
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

    if (!preg_match('/^[a-z0-9_-]*$/u', $room)) {
	$e .= '<li><strong>' . $plugin_tx['chat']['error_room_name'] . '</strong></li>';
	return false;
    }
    $_SESSION['chat_rooms'][$room] = true; // TODO: really necessary?
    if (isset($_GET['chat_room']) && $_GET['chat_room'] == $room) {
	Chat_appendMessage($room);
    }
    return Chat_view($room) . Chat_JS($room);
}


/**
 * Respond to Ajax requests.
 */
if (isset($_GET['chat_ajax'])
    && !empty($_SESSION['chat_rooms'][$_GET['chat_room']]))
{
    switch ($_GET['chat_ajax']) {
    case 'write':
	Chat_appendMessage($_GET['chat_room']);
	// FALLTHROUGH
    case 'read':
	echo Chat_messages($_GET['chat_room']);
	exit;
    }
}

?>
