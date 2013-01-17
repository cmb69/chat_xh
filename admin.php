<?php

/**
 * Back-end of Chat_XH.
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
 * Returns the plugin's "About" view.
 *
 * @return string  The (X)HTML.
 */
function Chat_aboutView()
{
    global $pth;

    $icon = tag('img src="' . $pth['folder']['plugins']
		. 'chat/chat.png" alt="Plugin Icon"');
    $bag = array('heading' => 'Chat_XH',
		 'url' => 'http://3-magi.net/?CMSimple_XH/Chat_XH',
		 'icon' => $icon,
		 'version' => CHAT_VERSION);
    return Chat_view('about', $bag);
}


/**
 * Returns the requirements information view.
 *
 * @return string  The (X)HTML.
 */
function Chat_systemCheck()
{ // RELEASE-TODO
    global $pth, $tx, $plugin_tx;

    $phpVersion = '4.3.0';
    $ptx = $plugin_tx['chat'];
    $imgdir = $pth['folder']['plugins'] . 'chat/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $o = '<h4>' . $ptx['syscheck_title'] . '</h4>'
	. (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
	. '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
	. tag('br');
    foreach (array('date', 'pcre', 'session') as $ext) {
	$o .= (extension_loaded($ext) ? $ok : $fail)
	    . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext) . tag('br');
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
	. '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes'] . tag('br');
    $o .= tag('br') . (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	. '&nbsp;&nbsp;' . $ptx['syscheck_encoding'] . tag('br');
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'] . 'chat/' . $folder;
    }
    $folders[] = Chat_dataFolder();
    foreach ($folders as $folder) {
	$o .= (is_writable($folder) ? $ok : $warn)
	    . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder) . tag('br');
    }
    return $o;
}


/*
 * Handle the plugin administration.
 */
if (isset($chat) && $chat == 'true') {
    $o .= print_plugin_admin('off');
    switch($admin) {
    case '':
	$o .= Chat_aboutView() . tag('hr') . Chat_systemCheck();
	break;
    default:
	$o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
