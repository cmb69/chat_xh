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
  - [Formatierung der Chat-Nachrichten](#formatierung-der-chat-nachrichten)
- [Einschränkungen](#einschränkungen)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Chat_XH ist ein Plugin für [CMSimple_XH](https://cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0 und PHP ≥ 7.1.0.
Chat_XH benötigt weiterhin [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.10;
ist dieses noch nicht installiert (siehe `Einstellungen` → `Info`),
laden Sie das [aktuelle Release](https://github.com/cmb69/plib_xh/releases/latest)
herunter, und installieren Sie es.

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

### Formatierung der Chat-Nachrichten

Die Formatierung der Nachrichten im Chatverlauf kann in den Spracheinstellungen
(`Pugins` → `Chat` → `Language` → `Format` → `Message`) konfiguriert werden.
Folgende Platzhalter werden unterstützt: `{USER}` (der Benutzername),
`{DATE}` (das Datum der Nachricht), `{TIME}` (die Uhrzeit der Nachricht)
und `{TEXT}` (die eigentliche Nachricht). Weiterer Text wird wie eingegeben
angezeigt. Ein schickeres Beispiel als die Voreinstellung.

    {USER} schrieb am {DATE} um {TIME}: {TEXT}

Es ist zu beachten, dass die Komponenten mit passenden CSS-Klassen (`chat_user`,
`chat_date` usw.) versehen werden, so dass diese individuell gestaltet werden können.

## Einschränkungen

Wenn JavaScript im Browser des Besuchers deaktiviert ist, oder ein uralter
Browser verwendet wird, funktioniert der automatische Abruf von neuen Nachrichten
nicht. Statt dessen müssen Besucher die Seite aktualisieren, um sie zu sehen.

Das Plugin verwendet Polling (d.h. periodisches Abfrage des Servers) um auf
neue Nachrichten zu prüfen.  Das Poll-Intervall ist zwar konfigurierbar
(`Plugins` → `Chat` → `Konfiguration` → `Interval` → `Poll`), aber es gibt keine
Pauschaleinstellung; es ist immer ein Kompromiss zwischen einem flüssigen Chat-
Erlebnis (d.h. einem kurzen Poll-Intervall), und der Menge an Verkehr, die der
Server abwickeln kann. Gibt es nur ein paar Chatter, dann kann das voreingestellte
Poll-Intervall kleiner gewählt werden, und alles sollte in Ordnung sein. Gibt es
allerdings potentiell viele Chatter, dann ist zu erwägen ein anderes Chat-Widget
zu verwenden, das durch Server-Push-Benachrichtigungen implementiert ist, aber
eben nicht durch Polling.

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

Der Benachrichtigungs-Soundeffekt stammt von
[ALEXIS_GAMING_CAM](https://pixabay.com/de/users/alexis_gaming_cam-50011695/)
auf [Pixabay](https://pixabay.com/sound-effects/).
Vielen Dank für die freie Verfügbarkeit.

Das Plugin verwendet [Material Icons](https://fonts.google.com/icons).
Vielen Dank für die Veröffentlichung dieser unter der Apache 2.0 Lizenz.

Vielen Dank an die Community im [CMSimple_XH Forum](http://www.cmsimpleforum.com/)
für Hinweise, Anregungen und das Testen.
Besonders möchte ich *snafu* für sein schnelles Feedback danken.

Und zu guter letzt vielen Dank an [Peter Harteg](https://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von
[CMSimple_XH](https://www.cmsimple-xh.org/de/) ohne die es dieses
phantastische CMS nicht gäbe.
