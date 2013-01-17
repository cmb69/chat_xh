<?php

$plugin_tx['chat']['user_self']="I";
$plugin_tx['chat']['user_unknown']="Somebody";

$plugin_tx['chat']['format_user']="{USER}: ";
$plugin_tx['chat']['format_date']="F dS, Y";
$plugin_tx['chat']['format_time']="H:i:s";

$plugin_tx['chat']['label_send']="Send";

$plugin_tx['chat']['error_room_name']="Invalid room name for chat()!";

$plugin_tx['chat']['menu_main']="Edit Chats";

$plugin_tx['chat']['syscheck_title']="System check";
$plugin_tx['chat']['syscheck_phpversion']="PHP version ≥ %s";
$plugin_tx['chat']['syscheck_extension']="Extension '%s' loaded";
$plugin_tx['chat']['syscheck_encoding']="Encoding 'UTF-8' configured";
$plugin_tx['chat']['syscheck_magic_quotes']="Magic quotes runtime off";
$plugin_tx['chat']['syscheck_writable']="Folder '%s' writable";

$plugin_tx['chat']['cf_folder_data']="Path to a folder relative to the CMSimple root directory, where to store the plugin's data. E.g. <em>userfiles/chat/</em>. Leave empty to store into the plugin's data/ folder.";
$plugin_tx['chat']['cf_format_date']="The format of the date when used as {DATE} in \"format user\" (see http://php.net/manual/en/function.date.php).";
$plugin_tx['chat']['cf_format_time']="The format of the time when used as {TIME} in \"format user\" (see http://php.net/manual/en/function.date.php).";
$plugin_tx['chat']['cf_format_user']="The format of the username (and optionally the date and time of posting) in the message. The following placeholders are allowed: {USER} (the name of the user), {DATE} (the date of posting) and {TIME} (the time of posting). E.g.: \"{USER} wrote on {DATE} at {TIME}: \"";
$plugin_tx['chat']['cf_interval_poll']="The time in milliseconds between the next poll for new messages.";
$plugin_tx['chat']['cf_interval_purge']="The time in seconds after which an inactive chat will be purged.";

?>
