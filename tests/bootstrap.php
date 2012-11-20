<?php

$srcDir = dirname(__DIR__) . '/src';
set_include_path(get_include_path() . PATH_SEPARATOR . $srcDir);

require_once  'PCharset.php';
require_once  'PType.php';
require_once  'PString.php';
require_once  'PNumber.php';
require_once  'PInteger.php';
require_once  'PBool.php';