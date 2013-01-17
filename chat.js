function Chat(room, url, interval) {
    this.room = room;
    this.url = url + "&chat_room=" + this.room + "&chat_ajax=";
    this.container = document.getElementById("chat_room_" + room);
    this.messages = this.container.firstChild; // TODO: is that safe
    this.form = this.container.getElementsByTagName("form")[0];
    this.scrollDown();
    var that = this;
    this.form.onsubmit = function() {
        return that.submit()
    };
    setInterval(function() {
        that.poll()
    }, interval);
}


Chat.prototype.clearInput = function() {
    this.form.elements["chat_message"].value = "";
}

Chat.prototype.scrollDown = function() {
    this.messages.scrollTop = this.messages.scrollHeight;
}

Chat.prototype.poll = function() {
    var that = this;
    var mustScroll = this.messages.scrollTop >= this.messages.scrollHeight - this.messages.clientHeight
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


Chat.prototype.submit = function() {
    var that = this;
    var request = new XMLHttpRequest();
    request.open('POST', this.url + "write");
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
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

//(function($) {
//
//    CHAT = {
//        container: function(room) {
//            return $('#chat_room_' + room);
//        },
//
//        clearInput: function(room) {
//            CHAT.container(room).find('input[type=text]')[0].value = '';
//        },
//
//        scrollDown: function(room) {
//            var cm = CHAT.container(room).children('div.chat_messages');
//            cm.scrollTop(cm[0].scrollHeight);
//        },
//
//
//        poll: function(url, room) {
//            $.ajax({
//                url: url + '&chat_room=' + room + '&chat_ajax=read',
//                success: function(data) {
//                    CHAT.container(room).children('div.chat_messages').html(data);
//                    CHAT.scrollDown(room);
//                }
//            })
//        },
//
//        submit: function(url, room) {
//            $.ajax({
//                url: url + '&chat_room=' + room + '&chat_ajax=write',
//                type: 'POST',
//                data: CHAT.container(room).children('form').serialize(),
//                success: function(data) {
//                    CHAT.container(room).children('div.chat_messages').html(data);
//                    CHAT.scrollDown(room);
//                    CHAT.clearInput(room);
//                }
//            })
//            return false;
//        }
//    }
//
//})(jQuery)
