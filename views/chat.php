
<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $room
 * @var string $url
 * @var list<string> $errors
 * @var list<object{class:string,message:string}> $messages
 * @var string $script
 * @var array<string,mixed> $config
 * @var string $token
 */
?>

<script type="module" src="<?=$this->esc($script)?>"></script>
<figure class="chat_room" data-chat-room="<?=$this->esc($room)?>" data-chat-config='<?=$this->json($config)?>'>
<!--START-->
  <figcaption><?=$this->text("caption_room", $room)?></figcaption>
<?foreach ($errors as $error):?>
<?=$this->raw($error)?>
<?endforeach?>
  <ol class="chat_messages">
<?foreach ($messages as $message):?>
    <li class="chat_message <?=$this->esc($message->class)?>"><?=$this->raw($message->message)?></li>
<?endforeach?>
  </ol>
  <form action="<?=$this->esc($url)?>" method="post">
    <input type="hidden" name="chat_token" value="<?=$this->esc($token)?>">
    <p class="chat_message">
      <label>
        <span><?=$this->text("label_message")?></span>
        <span class="chat_help"><?=$this->text("help_message")?></span>
        <input type="text" name="chat_message" required maxlength="160">
      </label>
    </p>
    <script type="text/x-template">
      <p class="chat_volume">
        <label>
          <span>Volume</span>
          <input type="range" value="50" min="0" max="100">
        </label>
      </p>
    </script>
    <p class="chat_button">
      <button name="chat_room" value="<?=$this->esc($room)?>"><?=$this->text("label_send")?></button>
    </p>
  </form>
<!--END-->
</figure>
