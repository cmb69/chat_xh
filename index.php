<?php

/**
 * Front-end of Chat_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Chat
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2015 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Chat_XH
 */

/**
 * The version of the plugin.
 */
define('CHAT_VERSION', '@CHAT_VERSION@');

/**
 * Handles the chat room and returns its view.
 *
 * @param string $room          A chat room name.
 * @param int    $purgeInterval A purge interval in seconds.
 *
 * @return string (X)HTML.
 */
function chat($room, $purgeInterval = null)
{
    $controller = new Chat_RoomController();
    return $controller->handle($room, $purgeInterval);
}

$temp = new Chat_Controller();
$temp->dispatch();

?>
