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

use PHPUnit\Framework\TestCase;

class RoomControllerTest extends TestCase
{
    /** @var RoomController */
    protected $subject;

    /** @var object */
    protected $messageMock;

    public function setUp(): void
    {
        global $plugin_tx;
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->subject = new RoomController();
    }

    public function testInvalidRoomNameReturnsFailureMessage(): void
    {
        $response = $this->subject->handle('te$t');
        $this->assertStringContainsString("Invalid chat room name:", $response);
    }
}
