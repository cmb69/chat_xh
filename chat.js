/*!
 * Chat_XH.
 *
 * @copyright Copyright (c) 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

/*global CHAT*/

(function () {
    "use strict";

    /**
     * Registers a load event listener for window.
     *
     * @param {function} listener A listener.
     *
     * @returns {undefined}
     */
    function onload(listener) {
        if (typeof window.addEventListener !== "undefined") {
            window.addEventListener("load", listener, false);
        } else if (typeof window.attachEvent !== "undefined") {
            window.attachEvent("onload", listener);
        }
    }

    /**
     * Initializes a chat room widget.
     *
     * @param {string} room The name of the chat room.
     *
     * @returns {undefined}
     */
    function initWidget(element) {
        var room, url, messages, form;

        /**
         * Clears the text input field.
         *
         * @returns {undefined}
         */
        function clearInput() {
            form.elements.chat_message.value = "";
        }

        /**
         * Scrolls down to the bottom of the chat.
         *
         * @returns {undefined}
         */
        function scrollDown() {
            messages.scrollTop = messages.scrollHeight;
        }

        /**
         * Polls the chat.
         *
         * @returns {undefined}
         */
        function poll() {
            var request;

            function mustScroll() {
                return messages.scrollTop >= messages.scrollHeight - messages.clientHeight;
            }

            function onReadyStateChange() {
                if (request.readyState === 4 && request.status === 200) {
                    messages.innerHTML = request.responseText;
                    if (mustScroll()) {
                        scrollDown();
                    }
                }
            }

            request = new XMLHttpRequest();
            request.open("GET", url + "read");
            request.onreadystatechange = onReadyStateChange;
            request.send(null);
        }

        /**
         * Submits a chat line.
         *
         * @returns {undefined}
         */
        function submit() {
            var request, msg;

            function onReadyStateChange() {
                if (request.readyState === 4 && request.status === 200) {
                    messages.innerHTML = request.responseText;
                    scrollDown();
                    clearInput();
                }
            }

            request = new XMLHttpRequest();
            request.open("POST", url + "write");
            request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            request.onreadystatechange = onReadyStateChange;
            msg = form.elements.chat_message.value;
            request.send("chat_message=" + encodeURIComponent(msg));
            return false;
        }

        room = element.getAttribute("data-chat-room");
        url = CHAT.url + "&chat_room=" + room + "&chat_ajax=";
        messages = document.querySelector("#chat_room_" + room + "_messages");
        form = document.querySelector("#chat_room_" + room + "_form");
        scrollDown();
        form.onsubmit = submit;
        setInterval(poll, CHAT.interval);
    }

    onload(function () {
        document.querySelectorAll(".chat_room", function (element) {
            initWidget(element);
        });
    });
}());
