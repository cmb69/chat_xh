<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var list<object{class:string,user:string,text:string}> $messages
 */
?>

<?foreach ($messages as $message):?>
<div class="chat_message <?=$this->esc($message->class)?>">
  <span class="chat_user"><?=$this->esc($message->user)?></span>
  <span class="chat_message"><?=$this->esc($message->text)?></span>
</div>
<?endforeach?>
