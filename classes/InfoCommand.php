<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Chat_XH.
 *
 * Chat_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Chat_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Chat_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Chat;

use Plib\SystemChecker;
use Plib\View;

class InfoCommand
{
    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(SystemChecker $systemChecker, View $view)
    {
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function render(): string
    {
        global $plugin_tx;

        $o = $this->aboutView() . tag('hr');
        $o .= '<h4>' . $plugin_tx['chat']['syscheck_title'] . '</h4>' . "\n"
            . $this->checkPHPVersion('7.1.0') . tag('br') . "\n";
        foreach (array('pcre', 'session') as $ext) {
            $o .= $this->checkExtension($ext) . tag('br') . "\n";
        }
        $o .= $this->checkXHVersion('1.7.0') . tag('br') . tag('br') . "\n";
        foreach ($this->getWritableFolders() as $folder) {
            $o .= $this->checkWritability($folder) . tag('br') . "\n";
        }
        return $o;
    }

    protected function aboutView(): string
    {
        global $pth, $plugin_tx;

        $icon = tag(
            'img class="chat_logo" src="' . $pth['folder']['plugins']
            . 'chat/chat.png" alt="' . $plugin_tx['chat']['alt_logo'] . '"'
        );
        $bag = array(
            'heading' => 'Chat &ndash; Info',
            'icon' => $icon,
            'version' => CHAT_VERSION
        );
        return $this->view->render('about', $bag);
    }

    protected function checkPHPVersion(string $version): string
    {
        global $plugin_tx;

        $kind = $this->systemChecker->checkVersion(PHP_VERSION, $version) ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf($plugin_tx['chat']['syscheck_phpversion'], $version);
    }

    protected function checkExtension(string $name): string
    {
        global $plugin_tx;

        $kind = $this->systemChecker->checkExtension($name) ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf($plugin_tx['chat']['syscheck_extension'], $name);
    }

    protected function checkXHVersion(string $version): string
    {
        global $plugin_tx;

        $kind = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH {$version}") ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf($plugin_tx['chat']['syscheck_xhversion'], $version);
    }

    protected function checkWritability(string $filename): string
    {
        global $plugin_tx;

        $kind = $this->systemChecker->checkWritability($filename) ? 'ok' : 'warn';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf($plugin_tx['chat']['syscheck_writable'], $filename);
    }

    protected function renderCheckIcon(string $kind): string
    {
        global $pth, $plugin_tx;

        $path = $pth['folder']['plugins'] . 'chat/images/'
            . $kind . '.png';
        $alt = $plugin_tx['chat']['syscheck_alt_' . $kind];
        return tag('img src="' . $path  . '" alt="' . $alt . '"');
    }

    protected function getWritableFolders(): array
    {
        global $pth;

        $folders = array();
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'chat/' . $folder;
        }
        $folders[] = Room::dataFolder();
        return $folders;
    }
}
