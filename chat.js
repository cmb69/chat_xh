// utf-8-marker: äöüß


(function($) {

    CHAT = {
        container: function(room) {
            return $('#chat_room_' + room);
        },

        clearInput: function(room) {
            CHAT.container(room).find('input[type=text]')[0].value = '';
        },

        scrollDown: function(room) {
            var cm = CHAT.container(room).children('div.chat_messages');
            cm.scrollTop(cm[0].scrollHeight);
        },


        poll: function(url, room) {
            $.ajax({
                url: url + '&chat_room=' + room + '&chat_ajax=read',
                success: function(data) {
                    CHAT.container(room).children('div.chat_messages').html(data);
                    CHAT.scrollDown(room);
                }
            })
        },

        submit: function(url, room) {
            $.ajax({
                url: url + '&chat_room=' + room + '&chat_ajax=write',
                type: 'POST',
                data: CHAT.container(room).children('form').serialize(),
                success: function(data) {
                    CHAT.container(room).children('div.chat_messages').html(data);
                    CHAT.scrollDown(room);
                    CHAT.clearInput(room);
                }
            })
            return false;
        }
    }

})(jQuery)
