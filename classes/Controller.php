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

namespace Chat;

class Controller extends AbstractController
{
    public function dispatch(): void
    {
        if (defined('XH_ADM') && XH_ADM) {
            if (function_exists('XH_registerStandardPluginMenuItems')) {
                XH_registerStandardPluginMenuItems(false);
            }
            if ($this->wantsPluginAdministration()) {
                $this->handleAdministration();
            }
        }
    }

    protected function wantsPluginAdministration(): bool
    {
        global $chat;

        return function_exists('XH_wantsPluginAdministration')
            && XH_wantsPluginAdministration('chat')
            || isset($chat) && $chat == 'true';
    }

    protected function handleAdministration(): void
    {
        global $o, $admin, $action;

        $o .= print_plugin_admin('off');
        switch ($admin) {
            case '':
                $o .= (new InfoCommand())->render();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, 'chat'); // @phpstan-ignore-line
        }
    }
}
