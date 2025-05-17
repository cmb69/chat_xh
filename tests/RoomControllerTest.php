<?php

namespace Chat;

use ApprovalTests\Approvals;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;

class RoomControllerTest extends TestCase
{
    /** @var array<string,string> */
    private $conf;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        global $pth, $plugin_tx;
        vfsStream::setup();
        mkdir(vfsStream::url("root/chat"));
        file_put_contents(
            vfsStream::url("root/chat/chat.csv"),
            "1747486215\t\thello\n1747486220\tolape\tworld\n1747486225\tcmb\t'sup"
        );
        $pth = ["folder" => ["content" => vfsStream::url("root/"), "plugins" => "../"]];
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["chat"];
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->view = new View("./views/", $plugin_tx["chat"]);
    }

    private function sut(): RoomController
    {
        return new RoomController($this->conf, $this->view);
    }

    public function testInvalidRoomNameReturnsFailureMessage(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()->handle('te$t', null, $request);
        $this->assertStringContainsString("Invalid chat room name:", $response);
    }

    public function testReportsFailureToSave(): void
    {
        $_GET = ["chat_room" => "chat"];
        $_POST = ["chat_message" => "test"];
        vfsStream::setQuota(0);
        $request = new FakeRequest();
        $response = $this->sut()->handle("chat", null, $request);
        $this->assertStringContainsString("Chat message could not be saved!", $response);
    }

    public function testShowsRoom(): void
    {
        global $bjs;
        $request = new FakeRequest(["username" => "cmb"]);
        $response = $this->sut()->handle("chat", null, $request);
        $this->assertSame(
            "<script type=\"text/javascript\">var CHAT = {\"url\":\"\/\",\"interval\":12000};</script>"
            . "<script type=\"text/javascript\" src=\"../chat/chat.js\"></script>\n",
            $bjs
        );
        Approvals::verifyHtml($response);
    }
}
