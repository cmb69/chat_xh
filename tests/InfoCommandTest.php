<?php

namespace Chat;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;

class InfoCommandTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_tx;
        $pth = ["folder" => ["content" => "../../content/", "plugins" => "../"]];
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
    }

    private function sut(): InfoCommand
    {
        return new InfoCommand();
    }

    public function testRendersSystemCheck(): void
    {
        $response = $this->sut()->render();
        Approvals::verifyHtml($response);
    }
}
