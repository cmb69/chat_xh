<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var list<object{class:string,message:string}> $messages
 */
?>

<?foreach ($messages as $message):?>
<li class="chat_message <?=$this->esc($message->class)?>"><?=$this->raw($message->message)?></li>
<?endforeach?>
