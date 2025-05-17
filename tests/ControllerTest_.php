<?php

/**
 * Testing the chat controllers.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Chat
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2014-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Chat_XH
 */

namespace Chat;

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    /** @var Controller */
    protected $subject;

    /** @var object */
    protected $rspmiMock;

    public function setUp(): void
    {
        $this->defineConstant('XH_ADM', true);
        $this->subject = new Controller();
        $this->rspmiMock = new PHPUnit_Extensions_MockFunction(
            'XH_registerStandardPluginMenuItems',
            $this->subject
        );
    }

    public function testDispatchRegistersPluginMenuItems(): void
    {
        $this->rspmiMock->expects($this->once())->with(false);
        $this->subject->dispatch();
    }

    protected function defineConstant(string $name, string $value): void
    {
        if (!defined($name)) {
            define($name, $value);
        } else {
            runkit_constant_redefine($name, $value);
        }
    }
}
