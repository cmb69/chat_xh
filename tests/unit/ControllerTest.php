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
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Chat_XH
 */

require_once './vendor/autoload.php';
require_once '../../cmsimple/functions.php';
require_once '../../cmsimple/adminfuncs.php';
require_once './classes/Room.php';
require_once './classes/Controller.php';

/**
 * Testing the chat controllers.
 *
 * @category Testing
 * @package  Chat
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chat_XH
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * The test subject.
     *
     * @var Chat_Controller
     */
    protected $subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     */
    public function setUp()
    {
        $this->defineConstant('XH_ADM', true);
        $this->subject = new Chat_Controller();
        $this->rspmiMock = new PHPUnit_Extensions_MockFunction(
            'XH_registerStandardPluginMenuItems', $this->subject
        );
    }

    /**
     * Tests that dispatch() registers the plugin menu items.
     *
     * @return void
     */
    public function testDispatchRegistersPluginMenuItems()
    {
        $this->rspmiMock->expects($this->once())->with(false);
        $this->subject->dispatch();
    }

    /**
     * Tests that an invalid room name returns nothing.
     *
     * @return void
     */
    public function testInvalidRoomNameReturnsNothing()
    {
        $this->assertEmpty($this->subject->main('te$t'));

    }

    /**
     * Defines or redefines a constant.
     *
     * @param string $name  A name.
     * @param string $value A value.
     *
     * @return void
     */
    protected function defineConstant($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        } else {
            runkit_constant_redefine($name, $value);
        }
    }
}

?>
