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

namespace Chat;

class RoomControllerTest extends PHPUnit_Framework_TestCase
{
    /** @var Chat_RoomController */
    protected $subject;

    /** @var object */
    protected $messageMock;

    public function setUp(): void
    {
        $this->subject = new Chat_RoomController();
        $this->messageMock = new PHPUnit_Extensions_MockFunction('XH_message', $this->subject);
    }

    public function testInvalidRoomNameReturnsFailureMessage(): void
    {
        $this->messageMock->expects($this->once())->with($this->equalTo('fail'));
        $this->subject->handle('te$t');
    }
}
