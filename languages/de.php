<?php

$plugin_tx['chat']['user_self']="Ich";
$plugin_tx['chat']['user_unknown']="Jemand";

$plugin_tx['chat']['format_user']="{USER}: ";
$plugin_tx['chat']['format_date']="j.n.Y";
$plugin_tx['chat']['format_time']="H:i:s";

$plugin_tx['chat']['label_send']="Senden";

$plugin_tx['chat']['alt_logo']="Sprechblasen";

$plugin_tx['chat']['error_room_name']="Ungültiger Chatraum-Name: darf nur Kleinbuchstaben (a-z), Ziffern (0-9) und Bindestriche enthalten.";
$plugin_tx['chat']['error_not_writable']="Chatraum %s ist nicht schreibbar!";

$plugin_tx['chat']['syscheck_title']="Systemprüfung";
$plugin_tx['chat']['syscheck_alt_ok']="OK";
$plugin_tx['chat']['syscheck_alt_warn']="Warnung";
$plugin_tx['chat']['syscheck_alt_fail']="Fehler";
$plugin_tx['chat']['syscheck_phpversion']="PHP-Version ≥ %s";
$plugin_tx['chat']['syscheck_extension']="Erweiterung '%s' geladen";
$plugin_tx['chat']['syscheck_magic_quotes']="Magic quotes runtime off";
$plugin_tx['chat']['syscheck_writable']="Ordner '%s' schreibbar";
$plugin_tx['chat']['syscheck_xhversion']="CMSimple_XH Version &ge; %s";

$plugin_tx['chat']['cf_folder_data']="Pfad eines Ordners relativ zum CMSimple_XH Installationsverzeichnis, wo die Plugindaten gespeichert werden sollen. Z.B. \"userfiles/chat/\". Leer lassen, um im data/ Ordner des Plugins zu speichern.";
$plugin_tx['chat']['cf_format_date']="Das Datumsformat wenn {DATE} in \"format user\" verwendet wird (siehe http://php.net/manual/de/function.date.php).";
$plugin_tx['chat']['cf_format_time']="Das Zeitformat, wenn {TIME} in \"format user\" verwendet wird (siehe http://php.net/manual/de/function.date.php).";
$plugin_tx['chat']['cf_format_user']="Das Format des Benutzernamens in der Nachricht. Folgende Platzhalter sind erlaubt: {USER} (der Benutzername), {DATE} (das Datum der Nachricht) und {TIME} (die Uhrzeit der Nachricht). Z.B.: \"{USER} schrieb am {DATE} um {TIME}: \"";
$plugin_tx['chat']['cf_interval_poll']="Das Intervall in Sekunden zwischen dem Abrufen neuer Nachrichten.";
$plugin_tx['chat']['cf_interval_purge']="Die Zeitdauer in Sekunden nachdem ein inaktiver Chat gelöscht wird. \"0\" bedeutet: nie löschen.";

?>
