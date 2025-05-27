<?php

require_once "./vendor/autoload.php";

require_once "../../cmsimple/functions.php";
require_once "../../cmsimple/utf8.php";

require_once "../plib/classes/CsrfProtector.php";
require_once "../plib/classes/Document.php";
require_once "../plib/classes/DocumentStore.php";
require_once "../plib/classes/Request.php";
require_once "../plib/classes/Response.php";
require_once "../plib/classes/SystemChecker.php";
require_once "../plib/classes/Url.php";
require_once "../plib/classes/View.php";
require_once "../plib/classes/FakeRequest.php";
require_once "../plib/classes/FakeSystemChecker.php";

require_once "./classes/model/Message.php";
require_once "./classes/model/Room.php";
require_once "./classes/Dic.php";
require_once "./classes/RoomController.php";
require_once "./classes/InfoCommand.php";

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";
const CHAT_VERSION = "2.0";
