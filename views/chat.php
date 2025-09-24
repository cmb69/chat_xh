
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
  <figcaption><?=$this->text("caption_room", $room)?></figcaption>
<?foreach ($errors as $error):?>
<?=$this->raw($error)?>
<?endforeach?>
  <ol class="chat_messages">
<!--START-->
<?foreach ($messages as $message):?>
    <li class="chat_message <?=$this->esc($message->class)?>"><?=$this->raw($message->message)?></li>
<?endforeach?>
<!--END-->
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
          <button type="button" data-volume="0.0" title="<?=$this->text("label_muted")?>" hidden><svg xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true" height="2em" viewBox="0 -960 960 960" width="2em" fill="currentColor"><path d="M280-360v-240h160l200-200v640L440-360H280Z"/></svg></button>
          <button type="button" data-volume="0.5" title="<?=$this->text("label_low")?>"><svg xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true" height="2em" viewBox="0 -960 960 960" width="2em" fill="currentColor"><path d="M200-360v-240h160l200-200v640L360-360H200Zm440 40v-322q45 21 72.5 65t27.5 97q0 53-27.5 96T640-320Z"/></svg></button>
          <button type="button" data-volume="1.0" title="<?=$this->text("label_loud")?>" hidden><svg xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true" height="2em" viewBox="0 -960 960 960" width="2em" fill="currentColor"><path d="M560-131v-82q90-26 145-100t55-168q0-94-55-168T560-749v-82q124 28 202 125.5T840-481q0 127-78 224.5T560-131ZM120-360v-240h160l200-200v640L280-360H120Zm440 40v-322q47 22 73.5 66t26.5 96q0 51-26.5 94.5T560-320Z"/></svg></button>
      </p>
    </script>
    <p class="chat_button">
      <button name="chat_room" value="<?=$this->esc($room)?>"><?=$this->text("label_send")?></button>
    </p>
  </form>
</figure>
