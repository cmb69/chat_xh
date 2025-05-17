<?php

/**
 * The abstract chat controllers.
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

class AbstractController
{
    protected function view(string $template, array $bag): string
    {
        global $pth;

        extract($bag);
        ob_start();
        include $pth['folder']['plugins'] . 'chat/views/' . $template . '.php';
        return ob_get_clean();
    }
}
