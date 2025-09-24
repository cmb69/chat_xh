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

/**
 * @typedef {object} Config
 * @property {string} url
 * @property {number} interval
 */

class Widget {
    /** @type {Config} */
    get config() {
        return JSON.parse(this.element.dataset.chatConfig);
    }

    /** @type {string} */
    get room() {
        return this.element.dataset.chatRoom;
    }

    /** @type {HTMLOListElement} */
    get messages() {
        return this.element.querySelector("ol");
    }

    /** @type {HTMLFormElement} */
    get form() {
        return this.element.querySelector("form");
    }

    constructor(/** @type {HTMLElement} */ element) {
        this.element = element;
        this.doInit();
    }

    /** @type {() => void} */
    scrollDown() {
        this.messages.scrollTop = this.messages.scrollHeight;
    }

    /** @type {(request: XMLHttpRequest) => void} */
    onReadyStateChange(request) {
        if (request.readyState !== 4) return;
        if (request.status !== 200) {
            this.form.onsubmit = null;
            return;
        }
        let [_, content] = request.responseText.match(/<!--START-->([\s\S]*?)<!--END-->/) || [];
        if (content !== undefined) {
            this.element.innerHTML = content;
            this.doInit();
            return;
        }
    }

    /** @type {() => void} */
    poll() {
        let request = new XMLHttpRequest();
        request.open("GET", this.config.url);
        request.setRequestHeader("X-CMSimple-XH-Request", "chat-" + this.room);
        request.onreadystatechange = () => {
            this.onReadyStateChange(request);
        };
        request.send();
    }

    /** @type {() => false} */
    submit() {
        let request = new XMLHttpRequest();
        request.open("POST", this.config.url);
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        request.setRequestHeader("X-CMSimple-XH-Request", "chat-" + this.room);
        request.onreadystatechange = () => {
            this.onReadyStateChange(request);
        };
        request.send(
            "chat_message=" +
                encodeURIComponent(this.form.querySelector["name=chat_message"].value) +
                "&chat_token=" +
                encodeURIComponent(this.form.querySelector["name=chat_token"].value)
        );
        return false;
    }

    /** @type {() => void} */
    doInit() {
        this.form.onsubmit = this.submit.bind(this);
        this.scrollDown();
        setTimeout(this.poll.bind(this), this.config.interval);
    }
}

/** @type {NodeListOf<HTMLElement>} */ (document.querySelectorAll(".chat_room")).forEach(
    function (element) {
        new Widget(element);
    }
);
