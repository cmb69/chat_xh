function Chat(room, url, interval) {
    this.room = room;
    this.url = url;
    this.container = document.getElementById("chat_room_" + room);
    this.messages = this.container.firstChild; // TODO: is that safe
    this.form = this.container.getElementsByTagName("form")[0];
    this.scrollDown();
    var that = this;
    this.form.onsubmit= function() {
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
    var cm = jQuery(this.container).children("div.chat_messages");
    cm.scrollTop(cm[0].scrollHeight);
}

Chat.prototype.poll = function() {
    var that = this;
    jQuery.ajax({
        url: this.url + '&chat_room=' + this.room + '&chat_ajax=read',
        success: function(data) {
            jQuery(that.container).children("div.chat_messages").html(data);
            that.scrollDown();
        }
    })
}


Chat.prototype.submit = function() {
    var that = this;
    jQuery.ajax({
        url: this.url + '&chat_room=' + this.room + '&chat_ajax=write',
        type: 'POST',
        data: jQuery(this.container).children("form").serialize(),
        success: function(data) {
            jQuery(that.container).children("div.chat_messages").html(data);
            that.scrollDown();
            that.clearInput();
        }
    });
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
