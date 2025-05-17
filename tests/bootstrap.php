<?php

require_once './vendor/autoload.php';

require_once '../../cmsimple/functions.php';
require_once '../../cmsimple/adminfuncs.php';

require_once "../plib/classes/Request.php";
require_once "../plib/classes/Response.php";
require_once "../plib/classes/SystemChecker.php";
require_once "../plib/classes/Url.php";
require_once "../plib/classes/View.php";
require_once "../plib/classes/FakeRequest.php";
require_once "../plib/classes/FakeSystemChecker.php";

require_once "./classes/Entry.php";
require_once "./classes/Room.php";
require_once "./classes/Dic.php";
require_once "./classes/RoomController.php";
require_once "./classes/InfoCommand.php";

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";
const CHAT_VERSION = "1.0";
