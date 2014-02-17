<?php

header('Content-Type: application/json');

$action = empty($_GET['action'])
    ? ''
    : $_GET['action']
;

if (empty($action) || !preg_match('/^[a-z_]+$/', $action)) {
    die(json_encode(array('error' => 'no action defined.')));
}

$action_file = __DIR__.'/_'.$action.'.php';
if (!is_file($action_file)) {
    die(json_encode(array('error' => 'action not defined, maby you forgot to add ?')));
}

require $action_file;

die(json_encode(array('error' => 'the action is responsible for sending correct formatted json output.')));
