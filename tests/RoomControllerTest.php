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
        global $pth;
        vfsStream::setup();
        mkdir(vfsStream::url("root/chat"));
        file_put_contents(
            vfsStream::url("root/chat/chat.csv"),
            "1747486215\t\thello\n1747486220\tolape\tworld\n1747486225\tcmb\t'sup\n"
        );
        $pth = ["folder" => ["content" => vfsStream::url("root/")]];
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["chat"];
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chat"]);
    }

    private function sut(): RoomController
    {
        return new RoomController("./", $this->conf, $this->view);
    }

    public function testInvalidRoomNameReturnsFailureMessage(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()->handle('te$t', null, $request);
        $this->assertStringContainsString("Invalid chat room name:", $response->output());
    }

    public function testHandlesAjaxRequest(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/",
            "header" => ["X-CMSimple-XH-Request" => "chat-chat"],
        ]);
        $response = $this->sut()->handle("chat", null, $request);
        $this->assertSame("Content-Type: text/html; charset=UTF-8", $response->contentType());
        Approvals::verifyHtml($response->output());
    }

    public function testSuccessfulAjaxPostShowsMessages(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/",
            "header" => ["X-CMSimple-XH-Request" => "chat-chat"],
            "post" => ["chat_message" => "test"],
        ]);
        $response = $this->sut()->handle("chat", null, $request);
        $this->assertSame("Content-Type: text/html; charset=UTF-8", $response->contentType());
        Approvals::verifyHtml($response->output());
    }

    public function testReportsFailureToSave(): void
    {
        vfsStream::setQuota(0);
        $request = new FakeRequest([
            "url" => "http://example.com/",
            "post" => ["chat_room" => "chat", "chat_message" => "test"],
        ]);
        $response = $this->sut()->handle("chat", null, $request);
        $this->assertStringContainsString("Chat message could not be saved!", $response->output());
    }

    public function testShowsRoom(): void
    {
        $request = new FakeRequest(["username" => "cmb"]);
        $response = $this->sut()->handle("chat", null, $request);
        Approvals::verifyHtml($response->output());
    }

    public function testRedirectsAfterSuccessfulSave(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/",
            "post" => ["chat_room" => "chat", "chat_message" => "test"],
        ]);
        $response = $this->sut()->handle("chat", null, $request);
        $this->assertSame("http://example.com/", $response->location());
    }
}
