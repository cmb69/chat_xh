
<!-- Chat_XH: begin of chat room -->
<div id="chat_room_<?php echo $room;?>" class="chat_room">
    <div id="chat_room_<?php echo $room;?>_messages" class="chat_messages">
	<?php echo $messages;?>
    </div>
    <form id="chat_room_<?php echo $room;?>_form" action="<?php echo $url;?>" method="post">
	<?php echo $inputs;?>

    </form>
</div>
<!-- Chat_XH: end of chat room -->
