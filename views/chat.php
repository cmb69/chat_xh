
<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $room
 * @var string $url
 * @var string $messages
 * @var string $script
 * @var array<string,mixed> $config
 */
?>

<script type="module" src="<?=$this->esc($script)?>"></script>
<figure class="chat_room" data-chat-room="<?=$this->esc($room)?>" data-chat-config='<?=$this->json($config)?>'>
  <figcaption><?=$this->text("caption_room", $room)?></figcaption>
  <ol id="chat_room_<?=$this->esc($room)?>_messages" class="chat_messages">
  <?=$this->raw($messages)?>
  </ol>
  <form id="chat_room_<?=$this->esc($room)?>_form" action="<?=$this->esc($url)?>" method="post">
    <p class="chat_message">
      <label>
        <span><?=$this->text("label_message")?></span>
        <input type="text" name="chat_message">
      </label>
    </p>
    <p class="chat_button">
      <button name="chat_room" value="<?=$this->esc($room)?>"><?=$this->text("label_send")?></button>
    </p>
  </form>
</figure>
