<?php

/**
 * Front-End of Chat_XH.
 *
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */


if (!isset($_SESSION)) {session_start();}


/**
 * Returns the data folder.
 *
 * @return string
 */
function chat_data_folder() {
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['chat'];

    if ($pcf['folder_data'] == '') {
	$fn = $pth['folder']['plugins'].'chat/data/';
    } else {
	$fn = $pth['folder']['base'].$pcf['folder_data'];
    }
    if (substr($fn, -1) != '/') {$fn .= '/';}
    if (file_exists($fn)) {
	if (!is_dir($fn)) {
	    e('cntopen', 'folder', $fn);
	}
    } else {
	if (!mkdir($fn, 0777, TRUE)) {
	    e('cntwriteto', 'folder', $fn);
	}
    }
    return $fn;
}


/**
 * Returns the name of the currently logged in user,
 * FALSE otherwise.
 *
 * @return string
 */
function chat_current_user() {
    return isset($_SESSION['username']) ? $_SESSION['username'] // Register
	    : (isset($_SESSION['Name']) ? $_SESSION['Name'] // Memberpages
	    : FALSE);
}


/**
 * Writes the necessary JS to $hjs.
 *
 * @global string $hjs
 * @param string $room  The name of the chat room.
 * @return void
 */
function chat_js($room) { // TODO: use $bjs if available
    global $pth, $hjs, $sn, $su, $plugin_cf;
    static $again = FALSE;

    $o = '';
    if (!$again) {
	$o .= '<script type="text/javascript" src="'.$pth['folder']['plugins'].'chat/chat.js"></script>'."\n";
	$again = TRUE;
    }
    $url = $sn.'?'.$su;
    $o .= <<<SCRIPT
<script type="text/javascript">
/* <![CDATA[ */
new Chat('$room', "$url", {$plugin_cf['chat']['interval_poll']});
/* ]]> */
</script>

SCRIPT;
    return $o;
}


/**
 * Appends the posted message to the data file.
 *
 * @param string $room  The name of the chat room.
 * @return void
 */
function chat_append_message($room) {
    if (empty($_POST['chat_message'])) {return;}
    $line = chat_current_user()."\t".stsl($_POST['chat_message'])."\n";
    $fn = chat_data_folder().$room.'.dat';
    if (($fp = fopen($fn, 'a')) === FALSE
	    || fwrite($fp, $line) === FALSE) {
	e('cntwriteto', 'file', $fn);
    }
    if ($fp !== FALSE) {fclose($fp);}
}


/**
 * Returns a data line (i.e. record) as (X)HTML.
 *
 * @param string $line
 * @return string
 */
function chat_message($line) {
    global $plugin_cf, $plugin_tx;

    $pcf = $plugin_cf['chat'];
    $ptx = $plugin_tx['chat'];
    list($user, $msg) = explode("\t", rtrim($line)); // TODO: handle for empty line
    if (!$user) {
	$user = $ptx['user_unknown'];
	$self = '';
    } elseif ($user == chat_current_user()) {
	$user = $ptx['user_self'];
	$self = ' chat_self';
    } else {
	$self = '';
    }
    return '<div class="chat_message'.$self.'">'
	    .'<span class="chat_user">'.sprintf($pcf['format_user'], $user).'</span>'
	    .'<span class="chat_message">'.htmlspecialchars($msg).'</span>'
	    .'</div>'."\n";
}


/**
 * Returns the history of the chat room as (X)HTML.
 *
 * @param string $room  The name of the chat room.
 * @return string
 */
function chat_messages($room) {
    global $plugin_cf;

    $fn = chat_data_folder().$room.'.dat';
    if (file_exists($fn)
	    && time() > filemtime($fn) + $plugin_cf['chat']['interval_purge']) {
	unlink($fn);
    }
    $o = '';
    if (is_readable($fn)
	    && ($fp = fopen($fn, 'r')) !== FALSE
	    && ($lines = file($fn)) !== FALSE) {
	foreach ($lines as $line) {
	    $o .= chat_message($line);
	}
	if ($fp !== FALSE) {fclose($fp);}
//    } else {
//	e('cntopen', 'file', $fn);
    }
    return $o;
}


/**
 * Returns the complete (X)HTML view of the chat room.
 *
 * @param string $room  The name of the chat room.
 * @return string
 */
function chat_view($room) {
    global $sn, $su, $plugin_tx;

    $url = $sn.'?'.$su.'&amp;chat_room='.$room;
    $o = '<div id="chat_room_'.$room.'" class="chat_room">'
	    .'<div class="chat_messages">'.chat_messages($room).'</div>'
	    .'<form action="'.$url.'" method="POST">'
	    .tag('input type="text" name="chat_message"')
	    .tag('input type="submit" class="submit" value="'.$plugin_tx['chat']['label_send'].'"')
	    .'</form>'
	    .'</div>';
    return $o;
}


/**
 * Handles the chat room and returns its (X)HTML view.
 *
 * @access public
 * @global string $e
 * @param string $room  The name of the chat room.
 * @return string
 */
function chat($room) {
    global $e, $plugin_tx;

    if (!preg_match('/^[a-z0-9_-]*$/u', $room)) {
	$e .= '<li><strong>'.$plugin_tx['chat']['error_room_name'].'</strong></li>'."\n";
	return FALSE;
    }
    $_SESSION['chat_rooms'][$room] = TRUE;
    $o = chat_js($room);
    if (isset($_GET['chat_room']) && $_GET['chat_room'] == $room) {
	chat_append_message($room);
    }
    return chat_view($room) . $o;
}


/**
 * Respond to ajax requests.
 */
if (isset($_GET['chat_ajax']) && !empty($_SESSION['chat_rooms'][$_GET['chat_room']])) {
    switch ($_GET['chat_ajax']) {
	case 'write':
	    chat_append_message($_GET['chat_room']);
	    // FALLTHROUGH
	case 'read':
	    echo chat_messages($_GET['chat_room']);
	    exit;
    }
}

?>
