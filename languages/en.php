<?php

$plugin_tx['chat']['user_self']="I";
$plugin_tx['chat']['user_unknown']="Somebody";

$plugin_tx['chat']['format_user']="{USER}: ";
$plugin_tx['chat']['format_date']="F dS, Y";
$plugin_tx['chat']['format_time']="h:i:s a";

$plugin_tx['chat']['label_send']="Send";

$plugin_tx['chat']['alt_logo']="Speech bubbles";

$plugin_tx['chat']['error_room_name']="Invalid chat room name: may contain only lowercase alphanumeric letters (a-z and 0-9) and hyphens.";
$plugin_tx['chat']['error_not_writable']="Chat room %s is not writable!";

$plugin_tx['chat']['syscheck_title']="System check";
$plugin_tx['chat']['syscheck_phpversion']="PHP version ≥ %s";
$plugin_tx['chat']['syscheck_extension']="Extension '%s' loaded";
$plugin_tx['chat']['syscheck_encoding']="Encoding 'UTF-8' configured";
$plugin_tx['chat']['syscheck_magic_quotes']="Magic quotes runtime off";
$plugin_tx['chat']['syscheck_writable']="Folder '%s' writable";

$plugin_tx['chat']['cf_folder_data']="Path of a folder relative to the CMSimple_XH root directory, where to store the plugin's data. E.g. \"userfiles/chat/\". Leave empty to store into the plugin's data/ folder.";
$plugin_tx['chat']['cf_format_date']="The format of the date when used as {DATE} in \"format user\" (see http://php.net/manual/en/function.date.php).";
$plugin_tx['chat']['cf_format_time']="The format of the time when used as {TIME} in \"format user\" (see http://php.net/manual/en/function.date.php).";
$plugin_tx['chat']['cf_format_user']="The format of the username in the message. The following placeholders are allowed: {USER} (the name of the user), {DATE} (the date of posting) and {TIME} (the time of posting). E.g.: \"{USER} wrote on {DATE} at {TIME}: \"";
$plugin_tx['chat']['cf_interval_poll']="The interval in seconds between the polls for new messages.";
$plugin_tx['chat']['cf_interval_purge']="The time in seconds after which an inactive chat will be purged. \"0\" means: keep entries forever.";

?>
