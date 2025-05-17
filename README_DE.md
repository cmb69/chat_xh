# Chat_XH

Chat_XH ermöglicht es, eine beliebige Anzahl von
einfachen Chats oder Shoutboxen auf Ihrer Website zu haben. Benutzer die mittels
[Memberpages_XH](https://github.com/cmsimple-xh/memberpages)
oder [Register_XH](https://github.com/cmb69/register_xh)
eingeloggt sind, werden als solche erkannt; andere Besucher können anonym
Nachrichten schicken.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Einschränkungen](#einschränkungen)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Chat_XH ist ein Plugin für [CMSimple_XH](https://cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0 und PHP ≥ 7.1.0.

## Download

Das [aktuelle Release](https://github.com/cmb69/chat_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins auch.

1. Sichern Sie die Daten auf Ihrem Server.
1. Entpacken Sie die ZIP-Datei auf Ihrem Rechner.
1. Laden Sie das ganzen Ordner `chat/` auf Ihren Server in den `plugins/`
   Ordner von CMSimple_XH hoch.
1. Machen Sie die Unterordner `config/`, `css/` und `languages/`
   beschreibbar.
1. Prüfen Sie unter `Plugins` → `Chat`, ob alle Voraussetzungen erfüllt sind.

## Einstellungen

Die Plugin-Konfiguration erfolgt wie bei vielen anderen CMSimple_XH-Plugins
auch im Administrationsbereich der Website. Gehen Sie zu `Plugins` → `Chat`.

Sie können die Voreinstellungen von Chat_XH unter `Konfiguration` ändern.
Hinweise zu den Optionen werden beim Überfahren der Hilfe-Icons mit der Maus
angezeigt.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können die
Sprachtexte in Ihre eigene Sprache übersetzen, falls keine entsprechende
Sprachdatei zur Verfügung steht, oder diese Ihren Wünschen gemäß anpassen.

Das Aussehen von Chat_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Zum Einbinden eines Chats auf einer Seite verwenden Sie folgenden
Pluginaufruf:

    {{{chat('%CHAT_ROOM%', %PURGE_TIME%)}}}

Um einen Chat im Template einzubinden, fügen Sie dort folgendes ein:

    <?=chat('%CHAT_ROOM%', %PURGE_TIME%)?>

- `%CHAT_ROOM%`:
   Der Name des Chats. Dieser kann beliebig gewählt werden, aber
   darf nur Kleinbuchstaben (a-z), Ziffern (0-9) und Bindestriche enthalten.
- `%PURGE_TIME%`:
   Die Zeitdauer in Sekunden nachdem ein inaktiver Chat gelöscht wird. `0`
   bedeutet, dass Beiträge nie gelöscht werden. Dieses Argument ist optional;
   wird es ausgelassen (einschließlich des vorangehenden Kommas),
   wird die Standardeinstellung aus der Konfiguration genommen.

Beispiele:

    {{{chat('chat')}}}
    {{{chat('chat17')}}}
    {{{chat('quick-chat', 120)}}}
    {{{chat('shoutbox', 0)}}}

Sie können auf jeder einzelnen Seite so viele Chats einbinden wie sie wollen –
diese sind unabhängig voneinander solange sie unterschiedliche Namen haben.

Nach einer einstellbaren Zeit der Inaktivität (d.h. es wurden keine neuen
Nachrichten geschickt) wird der Chatverlauf automatisch gelöscht.

Wenn Sie die Chat-Dateien bearbeiten wollen, müssen Sie dies per FTP tun.

## Einschränkungen

Wenn JavaScript oder Cookies im Browser des Besuchers deaktiviert sind, oder
falls der Browser das nötige JavaScript nicht unterstützt (z.B. IE 7 und älter),
funktioniert der automatische Abruf von neuen Nachrichten nicht. Statt dessen
müssen Besucher den Browser aktualisieren, um diese zu sehen.

Die Anzeige der aktuell angemeldeten Benutzer ist noch nicht implementiert.
Ein Workaround für Memberpages_XH ist im
[CMSimple_XH Forum](http://cmsimpleforum.com/viewtopic.php?f=12&t=5358#p33148)
zu finden.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/chat_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Chat_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Chat_XH erfolgt in der Hoffnung, dass es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Chat_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

Tschechische Übersetzung © 2012 Josef Němec<br>
Slovakische Übersetzung © 2012 Dr. Martin Sereday

## Danksagung

Chat_XH wurde von
[MiniChat von *scriptshow*](https://www.cmsimpleforum.com/viewtopic.php?f=2&t=4208)
angeregt.

Das Plugin-Icon wurde von [Alessandro Rei](http://www.mentalrey.it/) entworfen.
Vielen Dank für die Veröffentlichung dieses Icons unter GPL.

Dieses Plugin verwendet freie Anwendungs-Icons von [Aha-Soft](http://www.aha-soft.com/).
Vielen Dank für die freie Nutzbarkeit dieser Icons.

Vielen Dank an die Community im [CMSimple_XH Forum](http://www.cmsimpleforum.com/)
für Hinweise, Anregungen und das Testen.
Besonders möchte ich *snafu* für sein schnelles Feedback danken.

Und zu guter letzt vielen Dank an [Peter Harteg](https://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von
[CMSimple_XH](https://www.cmsimple-xh.org/de/) ohne die es dieses
phantastische CMS nicht gäbe.
