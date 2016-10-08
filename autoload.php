<?php
$util = include_once __DIR__.'/lib/Util.php';

spl_autoload_register(array($util, 'autoload'));
