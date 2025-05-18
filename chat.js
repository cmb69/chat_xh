/*!
 * Chat_XH.
 *
 * @copyright Copyright (c) 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

/**
 * Initializes a chat room widget.
 *
 * @param {string} room The name of the chat room.
 *
 * @returns {undefined}
 */
function initWidget(element) {
    var room, config, messages, form;

    /**
     * Scrolls down to the bottom of the chat.
     *
     * @returns {undefined}
     */
    function scrollDown() {
        messages.scrollTop = messages.scrollHeight;
    }

    function onReadyStateChange(request) {
        if (request.readyState === 4) {
            if (request.status === 200) {
                const matches = request.responseText.match(/<!--START-->(.*?)<!--END-->/s);
                if (matches !== null && matches.length === 2) {
                    element.innerHTML = matches[1];
                    doInit();
                    return;
                }
            }
            form.onsubmit = "";
            return;
        }
    }

    /**
     * Polls the chat.
     *
     * @returns {undefined}
     */
    function poll() {
        var request;

        request = new XMLHttpRequest();
        request.open("GET", config.url);
        request.setRequestHeader("X-CMSimple-XH-Request", "chat-" + room);
        request.onreadystatechange = () => {
            onReadyStateChange(request);
        }
        request.send();
    }

    /**
     * Submits a chat line.
     *
     * @returns {undefined}
     */
    function submit() {
        var request;

        request = new XMLHttpRequest();
        request.open("POST", config.url);
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        request.setRequestHeader("X-CMSimple-XH-Request", "chat-" + room);
        request.onreadystatechange = () => {
            onReadyStateChange(request);
        }
        request.send("chat_message=" + encodeURIComponent(form.elements.chat_message.value));
        return false;
    }

    function doInit() {
        messages = element.querySelector("ol");
        form = element.querySelector("form");
        form.onsubmit = submit;
        scrollDown();
        setTimeout(poll, config.interval);
    }

    room = element.dataset.chatRoom;
    config = JSON.parse(element.dataset.chatConfig);
    doInit();
}

document.querySelectorAll(".chat_room").forEach(function (element) {
    initWidget(element);
});
