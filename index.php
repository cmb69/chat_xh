<?php

/**
 * Front-end of Chat_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Chat
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2014 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Chat_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * The chat class.
 */
require_once $pth['folder']['plugin_classes'] . 'Chat.php';

/**
 * The version of the plugin.
 */
define('CHAT_VERSION', '@CHAT_VERSION@');

/**
 * The chat object.
 *
 * @var Chat
 */
$_Chat = new Chat();

/**
 * Handles the chat room and returns its view.
 *
 * @param string $room A chat room name.
 *
 * @return string (X)HTML.
 *
 * @global Chat The chat object.
 */
function chat($room)
{
    global $_Chat;

    return $_Chat->main($room);
}

/*
 * Start session.
 */
if (session_id() == '') {
    session_start();
}

$_Chat->dispatch();

?>
