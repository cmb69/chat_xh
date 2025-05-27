# Chat_XH

Chat_XH facilitates to have an arbitrary amount of
simple chat rooms or shoutboxes on your website. Users that are logged in via
[Memberpages_XH](https://github.com/cmsimple-xh/memberpages)
or [Register_XH](https://github.com/cmb69/register_xh) are
recognized as such; other visitors can send messages anonymously.

## Table of Contents

  - [Requirements](#requirements)
  - [Download](#download)
  - [Installation](#installation)
  - [Settings](#settings)
  - [Usage](#usage)
    - [Formatting of Chat Messages](#formatting-of-chat-messages)
  - [Limitations](#limitations)
  - [Troubleshooting](#troubleshooting)
  - [License](#license)
  - [Credits](#credits)

## Requirements

Chat_XH is a plugin for [CMSimple_XH](https://cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0 and PHP ≥ 7.1.0.
Chat_XH also requires [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.10;
if that is not already installed (see `Settings` → `Info`),
get the [lastest release](https://github.com/cmb69/plib_xh/releases/latest),
and install it.

## Download

The [lastest release](https://github.com/cmb69/chat_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple_XH plugins.

1. Backup the data on your server.
1. Unzip the distribution on your computer.
1. Upload the whole folder `chat/` to your server into the `plugins/` folder
   of CMSimple_XH.
1. Set write permissions for the subfolders `config/`, `css/` and
   `languages/`.
1. Check under `Plugins` → `Chat` if all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple_XH plugins
in the back-end of the website. Go to `Plugins` → `Chat`.

You can change the default settings of Chat_XH under `Config`.  Hints for the
options will be displayed when hovering over the help icon with your mouse.

Localization is done under `Language`.  You can translate the character
strings to your own language, if there is no appropriate language file
available, or customize them according to your needs.

The look of Chat_XH can be customized under `Stylesheet`.

## Usage

Embedding of a chat room on a page is done with the following plugin
call:

    {{{chat('%CHAT_ROOM%', %PURGE_TIME%)}}}

To embed a chat room in the template, insert:

    <?=chat('%CHAT_ROOM%', %PURGE_TIME%)?>

- `%CHAT_ROOM%`:
  The name of the chat.  It can be choosen arbitrarily, but may
  contain only lowercase alphanumeric characters (a-z and 0-9) and hyphens.
- `%PURGE_TIME%`:
  The time in seconds after which an inactive chat will be purged.  `0` means
  that entries are kept forever.  This argument is optional; if it is omitted
  (including the leading comma), the default is taken from the configuration.

Examples:

    {{{chat('chat')}}}
    {{{chat('chat17')}}}
    {{{chat('quick-chat', 120)}}}
    {{{chat('shoutbox', 0)}}}

You can embed as many chat rooms on any single page as you like – they are
working independent of each other as long as they have different names.

After a configurable time of inactivity (i.e. no new messages are posted),
the chat history will be purged automatically.

If you want to edit the chat data files, you have to do this via FTP.

### Formatting of Chat Messages

The format of the messages in the chat history can be configured in the
language settings (`Pugins` → `Chat` → `Language` → `Format` → `Message`).
The following placeholders are supported: `{USER}` (the name of the user),
`{DATE}` (the date of posting), `{TIME}` (the time of posting), and
`{TEXT}` (the posted message).  Additional text is taken as is.  A more fancy
example than the default:

    {USER} wrote on {DATE} at {TIME}: {TEXT}

Note that the components are marked up with suitable CSS classes
(`chat_user`, `chat_date`, etc.) so further styling can be applied.

## Limitations

If JavaScript is disabled in the browser of the visitor, or in case
the browser does not support the required JavaScript (e.g. IE 7 and older), the
automatic retrieval of new messages does not work. Instead visitors have to
refresh their browsers to see them.

Displaying the currently logged in users is not yet implemented. For a
workaround regarding Memberpages_XH see the
[CMSimple_XH Forum](https://cmsimpleforum.com/viewtopic.php?f=12&t=5358#p33148).

## Troubleshooting

Report bugs and ask for support either on [Github](https://github.com/cmb69/chat_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Chat_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Chat_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Chat_XH.  If not, see <https://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

Czech translation © 2012 Josef Němec<br>
Slovak translation © 2012 Dr. Martin Sereday

## Credits

Chat_XH was inspired by
[MiniChat by *scriptshow*](https://www.cmsimpleforum.com/viewtopic.php?f=2&t=4208).

The plugin icon is designed by [Alessandro Rei](http://www.mentalrey.it/).
Many thanks for publishing this icon under GPL.

Many thanks to the community at the
[CMSimple_XH Forum](https://www.cmsimpleforum.com/) for tips, suggestions and testing.
Particularly I want to thank *snafu* for giving early feedback.

And last but not least many thanks to [Peter Harteg](https://www.harteg.dk/),
the “father” of CMSimple, and all developers of [CMSimple_XH](https://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
