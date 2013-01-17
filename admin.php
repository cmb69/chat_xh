<?php

/**
 * Back-End of Chat_XH.
 *
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */

if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('CHAT_VERSION', '1beta1');


/**
 * Returns the plugin version information view.
 *
 * @return string  The (X)HTML.
 */
function chat_version() {
    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Chat_XH">Chat_XH</a></h1>'."\n"
	    .'<p>Version: '.CHAT_VERSION.'</p>'."\n"
	    .'<p>Copyright &copy; 2012 <a href="http://3-magi.net">Christoph M. Becker</a></p>'."\n"
	    .'<p style="text-align: justify">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p style="text-align: justify">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p style="text-align: justify">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns the requirements information view.
 *
 * @return string  The (X)HTML.
 */
function chat_system_check() { // RELEASE-TODO
    global $pth, $tx, $plugin_tx;

    define('CHAT_PHP_VERSION', '4.0.7');
    $ptx = $plugin_tx['chat'];
    $imgdir = $pth['folder']['plugins'].'chat/images/';
    $ok = tag('img src="'.$imgdir.'ok.png" alt="ok"');
    $warn = tag('img src="'.$imgdir.'warn.png" alt="warning"');
    $fail = tag('img src="'.$imgdir.'fail.png" alt="failure"');
    $htm = tag('hr').'<h4>'.$ptx['syscheck_title'].'</h4>'
	    .(version_compare(PHP_VERSION, CHAT_PHP_VERSION) >= 0 ? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_phpversion'], CHAT_PHP_VERSION)
	    .tag('br').tag('br')."\n";
    foreach (array('date', 'pcre', 'session') as $ext) {
	$htm .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $htm .= tag('br').(strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br')."\n";
    $htm .= (!get_magic_quotes_runtime() ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br')."\n";
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'].'chat/'.$folder;
    }
    $folders[] = chat_data_folder();
    foreach ($folders as $folder) {
	$htm .= (is_writable($folder) ? $ok : $warn)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $folder).tag('br')."\n";
    }
    return $htm;
}


//function chat_admin_main() {
//    global $pth, $sn, $su, $action, $admin, $plugin;
//
//    if (isset($_GET['chat_room'])) {
//	$pth['file']['plugin_main'] = chat_data_folder().$_GET['chat_room'].'.dat';
//	$o = plugin_admin_common($action, $admin, $plugin);
//	return $o;
//    } else {
//	$chats = glob(chat_data_folder().'*.dat');
//	$url = $sn.'?&amp;chat&amp;admin=plugin_main&amp;action=plugin_text&amp;chat_room=';
//	$o = '<ul>'."\n";
//	foreach ($chats as $chat) {
//	    $chat = basename($chat, '.dat');
//	    $o .= '<li><a href="'.$url.$chat.'">'.$chat.'</a></li>'."\n";
//	}
//	$o .= '</ul>'."\n";
//	return $o;
//    }
//}


/**
 * Handle the plugin administration.
 */
initvar('chat');
if (!empty($chat)) {
    initvar('admin');
    initvar('action');

    $o .= print_plugin_admin('off');

    switch($admin) {
	case '':
	    $o .= chat_version().chat_system_check();
	    break;
	//case 'plugin_main':
	//    $o .= chat_admin_main();
	//    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
