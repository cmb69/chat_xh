/**
 * JavaScript of Chat_XH.
 *
 * @package	Chat
 * @copyright	Copyright (c) 2012-2013 Christoph M. Becker <http://3-magi.net/>
 * @license	http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version     $Id$
 * @link	http://3-magi.net/?CMSimple_XH/Chat_XH
 */


/**
 * A chat room.
 *
 * @constructor
 * @param {String} room  The name of the chat room.
 * @param {String} base  The base URL for Ajax requests.
 * @param {Number} interval  The polling interval in sec.
 */
function Chat(room, base, interval) {
    var that = this;
    this.room = room;
    this.url = base + "&chat_room=" + this.room + "&chat_ajax=";
    this.container = document.getElementById("chat_room_" + room);
    this.messages = document.getElementById("chat_room_" + room + "_messages");
    this.form = document.getElementById("chat_room_" + room + "_form");
    this.scrollDown();
    if (typeof window.XMLHttpRequest == 'function') {
        this.form.onsubmit = function() {return that.submit()};
        setInterval(function() {that.poll()}, interval);
    }
}


/**
 * Clears the text input field.
 *
 * @returns {undefined}
 */
Chat.prototype.clearInput = function() {
    this.form.elements["chat_message"].value = "";
}


/**
 * Scrolls down to the bottom of the chat.
 *
 * @returns {undefined}
 */
Chat.prototype.scrollDown = function() {
    this.messages.scrollTop = this.messages.scrollHeight;
}


/**
 * Polls the chat.
 *
 * @returns {undefined}
 */
Chat.prototype.poll = function() {
    var that = this;
    var mustScroll = this.messages.scrollTop
        >= this.messages.scrollHeight - this.messages.clientHeight
    var request = new XMLHttpRequest();
    request.open('GET', this.url + "read");
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            that.messages.innerHTML = request.responseText;
            if (mustScroll) {
                that.scrollDown();
            }
        }
    }
    request.send(null);
}


/**
 * Submits a chat line.
 *
 * @returns {undefined}
 */
Chat.prototype.submit = function() {
    var that = this;
    var request = new XMLHttpRequest();
    request.open('POST', this.url + "write");
    request.setRequestHeader("Content-Type",
                             "application/x-www-form-urlencoded");
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            that.messages.innerHTML = request.responseText;
            that.scrollDown();
            that.clearInput();
        }
    }
    var msg = this.form.elements["chat_message"].value;
    request.send("chat_message=" + window.encodeURIComponent(msg));
    return false;
}
