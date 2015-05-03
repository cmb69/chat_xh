<?php

/**
 * Testing the chat room controllers.
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

require_once './vendor/autoload.php';
require_once '../../cmsimple/functions.php';
require_once '../../cmsimple/adminfuncs.php';
require_once './classes/required_classes.php';

/**
 * Testing the chat room controllers.
 *
 * @category Testing
 * @package  Chat
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chat_XH
 */
class RoomControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * The test subject.
     *
     * @var Chat_RoomController
     */
    protected $subject;

    /**
     * The XH_message() mock.
     *
     * @var object
     */
    protected $messageMock;

    /**
     * Sets up the test fixture.
     *
     * @return void
     */
    public function setUp()
    {
        $this->subject = new Chat_RoomController();
        $this->messageMock = new PHPUnit_Extensions_MockFunction(
            'XH_message', $this->subject
        );
    }

    /**
     * Tests that an invalid room name returns a failure message.
     *
     * @return void
     */
    public function testInvalidRoomNameReturnsFailureMessage()
    {
        $this->messageMock->expects($this->once())->with($this->equalTo('fail'));
        $this->subject->handle('te$t');

    }
}

?>
