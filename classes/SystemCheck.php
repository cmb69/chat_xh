<?php

/**
 * The system check.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Chat
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Chat_XH
 */

namespace Chat;

class SystemCheck
{
    public function render(): string
    {
        global $plugin_tx;

        $o = '<h4>' . $plugin_tx['chat']['syscheck_title'] . '</h4>'
            . $this->checkPHPVersion('7.1.0') . tag('br');
        foreach (array('pcre', 'session') as $ext) {
            $o .= $this->checkExtension($ext) . tag('br');
        }
        $o .= $this->checkXHVersion('1.7.0') . tag('br') . tag('br');
        foreach ($this->getWritableFolders() as $folder) {
            $o .= $this->checkWritability($folder) . tag('br');
        }
        return $o;
    }

    protected function checkPHPVersion(string $version): string
    {
        global $plugin_tx;

        $kind = version_compare(PHP_VERSION, $version) >= 0 ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf($plugin_tx['chat']['syscheck_phpversion'], $version);
    }

    protected function checkExtension(string $name): string
    {
        global $plugin_tx;

        $kind = extension_loaded($name) ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf($plugin_tx['chat']['syscheck_extension'], $name);
    }

    protected function checkXHVersion(string $version): string
    {
        global $plugin_tx;

        $kind = $this->hasXHVersion($version) ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf($plugin_tx['chat']['syscheck_xhversion'], $version);
    }

    protected function hasXHVersion(string $version): bool
    {
        return defined('CMSIMPLE_XH_VERSION')
            && strpos(CMSIMPLE_XH_VERSION, 'CMSimple_XH') === 0
            && version_compare(CMSIMPLE_XH_VERSION, "CMSimple_XH {$version}", 'gt');
    }

    protected function checkWritability(string $filename): string
    {
        global $plugin_tx;

        $kind = is_writable($filename) ? 'ok' : 'warn';
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
