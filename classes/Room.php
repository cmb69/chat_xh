<?php

/**
 * The chat rooms.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Chat
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Chat_XH
 */

/**
 * The chat rooms.
 *
 * @category CMSimple_XH
 * @package  Chat
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chat_XH
 */
class Chat_Room
{
    /**
     * The room name.
     *
     * @var string
     */
    protected $name;

    /**
     * Returns the path of the data folder.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the plugins.
     */
    public static function dataFolder()
    {
        global $pth, $plugin_cf;

        $pcf = $plugin_cf['chat'];

        if ($pcf['folder_data'] == '') {
            $filename = $pth['folder']['plugins'] . 'chat/data/';
        } else {
            $filename = $pth['folder']['base'] . $pcf['folder_data'];
        }
        if (substr($filename, -1) != '/') {
            $filename .= '/';
        }
        if (file_exists($filename)) {
            if (!is_dir($filename)) {
                e('cntopen', 'folder', $filename);
            }
        } else {
            if (mkdir($filename, 0777, true)) {
                chmod($filename, 0777);
            } else {
                e('cntwriteto', 'folder', $filename);
            }
        }
        return $filename;
    }

    /**
     * Returns whether a name is a valid room name.
     *
     * @param string $name A name.
     *
     * @return bool
     */
    public static function isValidName($name)
    {
        return (bool) preg_match('/^[a-z0-9-]*$/u', $name);
    }

    /**
     * Initializes a new instance.
     *
     * @param string $name A name.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the path of the chat room data file.
     *
     * @return string
     */
    protected function getFilename()
    {
        return self::dataFolder() . $this->name . '.csv';
    }

    /**
     * Returns whether the room is expired.
     *
     * @return bool
     *
     * @global array The configuration of the plugins.
     */
    public function isExpired()
    {
        global $plugin_cf;

        $filename = $this->getFilename();
        $purgeInterval = $plugin_cf['chat']['interval_purge'];
        return file_exists($filename)
            && $purgeInterval
            && time() > filemtime($filename) + $purgeInterval;
    }

    /**
     * Purges a chat room.
     *
     * @return void
     */
    public function purge()
    {
        unlink($this->getFilename());
    }

    /**
     * Finds and returns all entries.
     *
     * @return array<Chat_Entry>
     */
    public function findEntries()
    {
        $filename = $this->getFilename();
        $entries = array();
        if (is_readable($filename)
            && ($lines = file($filename)) !== false
        ) {
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $entries[] = Chat_Entry::makeFromLine(rtrim($line));
                }
            }
        }
        return $entries;
    }

    /**
     * Appends an entry.
     *
     * @param Chat_Entry $entry A chat entry.
     *
     * @return void
     */
    public function appendEntry(Chat_Entry $entry)
    {
        $okay = true;
        $filename = $this->getFilename();
        if (($file = fopen($filename, 'a')) === false
            || fwrite($file, $entry->getLine() . PHP_EOL) === false
        ) {
            $okay = false;
        }
        if ($file !== false) {
            fclose($file);
        }
        return $okay;
    }
}

?>
