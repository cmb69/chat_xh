
<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $room
 * @var string $inputs
 * @var string $url
 * @var string $messages
 */
?>

<div class="chat_room" data-chat-room="<?=$this->esc($room)?>">
  <div id="chat_room_<?=$this->esc($room)?>_messages" class="chat_messages">
  <?=$this->raw($messages)?>
  </div>
  <form id="chat_room_<?=$this->esc($room)?>_form" action="<?=$this->esc($url)?>" method="post">
  <?=$this->raw($inputs)?>
  </form>
</div>
