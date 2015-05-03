/**
 * JavaScript of Chat_XH.
 *
 * @package   Chat
 * @copyright Copyright (c) 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Chat_XH
 */

(function () {
    "use strict";

    /**
     * A chat room.
     *
     * @constructor
     *
     * @param {String} room     The name of the chat room.
     * @param {String} base     The base URL for Ajax requests.
     * @param {Number} interval The polling interval in sec.
     */
    CHAT.Widget = function (room, base, interval) {
        var that = this;

        this.room = room;
        this.url = base + "&chat_room=" + this.room + "&chat_ajax=";
        this.container = document.getElementById("chat_room_" + room);
        this.messages = document.getElementById(
            "chat_room_" + room + "_messages"
        );
        this.form = document.getElementById("chat_room_" + room + "_form");
        this.scrollDown();
        if (typeof window.XMLHttpRequest !== "undefined") {
            this.form.onsubmit = function () {
                return that.submit();
            };
            setInterval(function () {
                that.poll();
            }, interval);
        }
    };

    /**
     * Clears the text input field.
     */
    CHAT.Widget.prototype.clearInput = function () {
        this.form.elements.chat_message.value = "";
    };

    /**
     * Scrolls down to the bottom of the chat.
     */
    CHAT.Widget.prototype.scrollDown = function () {
        this.messages.scrollTop = this.messages.scrollHeight;
    };

    /**
     * Polls the chat.
     */
    CHAT.Widget.prototype.poll = function () {
        var that = this,
            mustScroll = this.messages.scrollTop >=
                this.messages.scrollHeight - this.messages.clientHeight,
            request = new XMLHttpRequest();

        request.open("GET", this.url + "read");
        request.onreadystatechange = function () {
            if (request.readyState === 4 && request.status === 200) {
                that.messages.innerHTML = request.responseText;
                if (mustScroll) {
                    that.scrollDown();
                }
            }
        };
        request.send(null);
    };

    /**
     * Submits a chat line.
     */
    CHAT.Widget.prototype.submit = function () {
        var that = this,
            request = new XMLHttpRequest(),
            msg;

        request.open("POST", this.url + "write");
        request.setRequestHeader(
            "Content-Type",
            "application/x-www-form-urlencoded"
        );
        request.onreadystatechange = function () {
            if (request.readyState === 4 && request.status === 200) {
                that.messages.innerHTML = request.responseText;
                that.scrollDown();
                that.clearInput();
            }
        };
        msg = this.form.elements.chat_message.value;
        request.send("chat_message=" + window.encodeURIComponent(msg));
        return false;
    };

    function onload(listener) {
        if (typeof window.addEventListener !== "undefined") {
            window.addEventListener("load", listener, false);
        } else if (typeof window.attachEvent !== "undefined") {
            window.attachEvent("onload", listener);
        }
    }

    function doForEach(className, func) {
        var elements, i, n;

        if (typeof document.getElementsByClassName != "undefined") {
            elements = document.getElementsByClassName(className);
        } else if (typeof document.querySelectorAll != "undefined") {
            elements = document.querySelectorAll("." + className);
        } else {
            elements = [];
        }
        for (i = 0, n = elements.length; i < n; i++) {
            func(elements[i]);
        }
    }

    onload(function () {
        doForEach("chat_room", function (element) {
            var room = element.id.substr(("chat_room_").length);

            new CHAT.Widget(room, CHAT.config.url, CHAT.config.interval);
        });
    });
}());
