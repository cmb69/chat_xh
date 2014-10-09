<?php

/**
 * The chat entries.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Chat
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2014 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Chat_XH
 */

/**
 * The chat entries.
 *
 * @category CMSimple_XH
 * @package  Chat
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chat_XH
 */
class Chat_Entry
{
    /**
     * The timestamp.
     *
     * @var int
     */
    protected $timestamp;

    /**
     * The username.
     *
     * @var string
     */
    protected $username;

    /**
     * The message.
     *
     * @var string
     */
    protected $message;

    /**
     * Makes a new entry from a CSV line.
     *
     * @param string $line A CSV line.
     *
     * @return Chat_Entry
     */
    public static function makeFromLine($line)
    {
        $entry = new self();
        list($entry->timestamp, $entry->username, $entry->message)
            = explode("\t", $line, 3);
        return $entry;
    }

    /**
     * Returns the timestamp.
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Sets the timestamp.
     *
     * @param int $timestamp A timestamp.
     *
     * @return void
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * Returns the username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the username.
     *
     * @param string $username A username.
     *
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Returns the message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the message.
     *
     * @param string $message A message.
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Returns the CSV line.
     *
     * @return string
     */
    public function getLine()
    {
        return $this->timestamp . "\t" . $this->username . "\t" . $this->message;
    }
}

?>
