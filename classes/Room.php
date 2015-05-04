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
     * The purge interval in seconds.
     *
     * @var int
     */
    protected $purgeInterval;

    /**
     * Returns the path of the data folder.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     */
    public static function dataFolder()
    {
        global $pth;

        $filename = $pth['folder']['content'] . 'chat/';
        if (!file_exists($filename)) {
            if (mkdir($filename, 0777, true)) {
                chmod($filename, 0777);
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
     * @param string $name          A name.
     * @param int    $purgeInterval A purge interval in seconds.
     */
    public function __construct($name, $purgeInterval)
    {
        $this->name = $name;
        $this->purgeInterval = $purgeInterval;
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
    public function getFilename()
    {
        return self::dataFolder() . $this->name . '.csv';
    }

    /**
     * Returns whether the chat room data file is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        $filename = $this->getFilename();
        return is_writable($filename) ||
            !file_exists($filename) && is_writable(dirname($filename));
    }

    /**
     * Returns whether the room is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        $filename = $this->getFilename();
        return file_exists($filename)
            && $this->purgeInterval
            && time() > filemtime($filename) + $this->purgeInterval;
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
     * @return bool
     */
    public function appendEntry(Chat_Entry $entry)
    {
        $filename = $this->getFilename();
        return (bool) file_put_contents(
            $filename, $entry->getLine() . PHP_EOL, FILE_APPEND
        );
    }
}

?>
