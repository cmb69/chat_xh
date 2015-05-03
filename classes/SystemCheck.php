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

/**
 * The system check.
 *
 * @category CMSimple_XH
 * @package  Chat
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chat_XH
 */
class Chat_SystemCheck
{
    /**
     * Returns the requirements information view.
     *
     * @return string (X)HTML
     *
     * @global array The localization of the plugins.
     */
    public function render()
    {
        global $plugin_tx;

        $o = '<h4>' . $plugin_tx['chat']['syscheck_title'] . '</h4>'
            . $this->checkPHPVersion('5.1.2') . tag('br');
        foreach (array('pcre', 'session') as $ext) {
            $o .= $this->checkExtension($ext) . tag('br');
        }
        $o .= $this->checkMagicQuotesRuntime() . tag('br') . tag('br')
            . $this->checkXHVersion('1.6') . tag('br') . tag('br');
        foreach ($this->getWritableFolders() as $folder) {
            $o .= $this->checkWritability($folder) . tag('br');
        }
        return $o;
    }

    /**
     * Renders the PHP version check.
     *
     * @param string $version Required PHP version.
     *
     * @return string (X)HTML
     *
     * @global array The localization of the plugins.
     */
    protected function checkPHPVersion($version)
    {
        global $plugin_tx;

        $kind = version_compare(PHP_VERSION, $version) >= 0 ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf(
                $plugin_tx['chat']['syscheck_phpversion'], $version
            );
    }

    /**
     * Renders the extension availability check.
     *
     * @param string $name An extension name.
     *
     * @return string (X)HTML
     *
     * @global array The localization of the plugins.
     */
    protected function checkExtension($name)
    {
        global $plugin_tx;

        $kind = extension_loaded($name) ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf(
                $plugin_tx['chat']['syscheck_extension'], $name
            );
    }

    /**
     * Renders the magic_quotes_runtime check.
     *
     * @return string (X)HTML
     *
     * @global array The localization of the plugins.
     */
    protected function checkMagicQuotesRuntime()
    {
        global $plugin_tx;

        $kind = get_magic_quotes_runtime() ? 'fail' : 'ok';
        return $this->renderCheckIcon($kind). '&nbsp;&nbsp;'
            . $plugin_tx['chat']['syscheck_magic_quotes'];
    }

    /**
     * Renders the CMSimple_XH version check.
     *
     * @param string $version Required CMSimple_XH version.
     *
     * @return string (X)HTML
     *
     * @global array The localization of the plugins.
     */
    protected function checkXHVersion($version)
    {
        global $plugin_tx;

        $kind = $this->hasXHVersion($version) ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf(
                $plugin_tx['chat']['syscheck_xhversion'], $version
            );
    }

    /**
     * Returns whether at least a certain CMSimple_XH version is installed.
     *
     * @param string $version A CMSimple_XH version number.
     *
     * @return bool
     */
    protected function hasXHVersion($version)
    {
        return defined('CMSIMPLE_XH_VERSION')
            && strpos(CMSIMPLE_XH_VERSION, 'CMSimple_XH') === 0
            && version_compare(CMSIMPLE_XH_VERSION, "CMSimple_XH {$version}", 'gt');
    }

    /**
     * Renders a writability check.
     *
     * @param string $filename A filename.
     *
     * @return string (X)HTML
     *
     * @global array The localization of the plugins.
     */
    protected function checkWritability($filename)
    {
        global $plugin_tx;

        $kind = is_writable($filename) ? 'ok' : 'warn';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf(
                $plugin_tx['chat']['syscheck_writable'], $filename
            );
    }

    /**
     * Renders a check icon.
     *
     * @param string $kind A kind.
     *
     * @return string (X)HTML
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
     */
    protected function renderCheckIcon($kind)
    {
        global $pth, $plugin_tx;

        $path = $pth['folder']['plugins'] . 'chat/images/'
            . $kind . '.png';
        $alt = $plugin_tx['chat']['syscheck_alt_' . $kind];
        return tag('img src="' . $path  . '" alt="' . $alt . '"');
    }

    /**
     * Returns the folders that should be writable.
     *
     * @return array
     *
     * @global array The paths of system files and folders.
     */
    protected function getWritableFolders()
    {
        global $pth;

        $folders = array();
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'chat/' . $folder;
        }
        $folders[] = Chat_Room::dataFolder();
        return $folders;
    }
}

?>
