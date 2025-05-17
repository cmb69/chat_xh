<?php

namespace Chat;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeSystemChecker;
use Plib\View;

class InfoCommandTest extends TestCase
{
    /** @var View */
    private $view;

    public function setUp(): void
    {
        global $pth, $plugin_tx;
        $pth = ["folder" => ["content" => "../../content/", "plugins" => "../"]];
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->view = new View("./views/", $plugin_tx["chat"]);
    }

    private function sut(): InfoCommand
    {
        return new InfoCommand(new FakeSystemChecker(), $this->view);
    }

    public function testRendersSystemCheck(): void
    {
        $response = $this->sut()->render();
        Approvals::verifyHtml($response);
    }
}
