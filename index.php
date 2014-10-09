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
 * Prevent direct access and usage from unsupported CMSimple_XH versions.
 */
if (!defined('CMSIMPLE_XH_VERSION')
    || strpos(CMSIMPLE_XH_VERSION, 'CMSimple_XH') !== 0
    || version_compare(CMSIMPLE_XH_VERSION, 'CMSimple_XH 1.6', 'lt')
) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: text/plain; charset=UTF-8');
    die(<<<EOT
Chat_XH detected an unsupported CMSimple_XH version.
Uninstall Chat_XH or upgrade to a supported CMSimple_XH version!
EOT
    );
}

require_once $pth['folder']['plugin_classes'] . 'Entry.php';

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

$_Chat->dispatch();

?>
