<?php

function _log($what) {
    error_log(date('[Y-m-d H:i:s] ').print_r($what, 1)."\n", 3, "/tmp/travis-hook.log");
}

function _humantime($seconds) {
    $days = intval($seconds/86400);
    $seconds -= $days*86400;

    $hours = intval($seconds/3600);
    $seconds -= $hours*3600;

    $minutes = intval($seconds/60);
    $seconds -= $minutes*60;

    $str = '';
    if ($days) {
        $str .= $days . _plural('day', $days);
    }

    if ($hours) {
        $str .= $hours . _plural('hour', $hours);
    }

    if ($minutes) {
        $str .= $minutes . _plural('minute', $minutes);
    }

    if ($seconds) {
        $str .= $seconds . _plural('second', $seconds);
    }

    return trim($str);
}

function _plural($unit, $value) {
    if ($value > 1) {
        $unit .='s';
    }

    return ' '.$unit.' ';
}

if (!function_exists('getallheaders')) {
    function getallheaders() {
       $headers = '';
       foreach ($_SERVER as $name => $value) {
           if (substr($name, 0, 5) == 'HTTP_') {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }

       return $headers;
    }
}

function notify_dashing($payload) {
    $board = 'master';
    if ('master' != $payload['branch']) {
        $board = 'other';
    }

    $message = '';
    switch ($payload['matrix'][0]['state']) {
        case 'started':
            $message = '[#'.$payload['branch'].']<br> !build in progress';
            break;
        case 'finished':
            $message = '[#'.$payload['branch'].']<br>'.$payload['matrix'][0]['state'].'] in #'.$payload['duration'].'s';
            break;
    }

    $data = [
        'auth_token' => 'monsterhack',
        'items' => [0 => [
            'label' => 'Build #'.$payload['number'],
            'value' => $message,
            'state' => $payload['matrix'][0]['state'],
        ]]
    ];

    $ch = curl_init("http://localhost:3030/widgets/travis-".$board);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    curl_exec($ch);
    curl_close($ch);
}

function notify_dev_team($data) {
    $status = strtolower($data['status_message']);

    if ($status == 'pending') {
        return;
    }

    if ($data['branch'] == 'master') {
        $recipients = 'pdl@bellcom.dk, lv@pompdelux.dk, hd@pompdelux.dk';
    } else {
        $recipients = 'pdl@bellcom.dk';
    }

    if ($status == 'passed') {
        $subject  = 'Build passed, please continue :-)';
        $icon     = '&#10004;';
        $bc_color = '#baecb7';
        $fg_color = '#32a32d';
    } else {
        $subject  = 'Build FAILED !!! Stop everything and investigate... :-(';
        $icon     = '&#10008;';
        $bc_color = '#ff7373';
        $fg_color = '#a60000';
    }

    $from = 'pdl@bellcom.dk';
    $headers =
        "From: ".$from."\r\n".
        "Return-Path: ".$from."\r\n".
        "Errors-To: ".$from."\r\n".
        "MIME-Version: 1.0\r\n".
        "Content-type: text/html; charset=utf-8\r\n"
    ;

    $message = '<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"></head>
<body>
<div id="body" style="font-family:%22Helvetica Neue%22, Helvetica, Arial, sans-serif;font-size:16px;color:#808080;width:570px;margin:0 auto">
  <table background="" class="repository" style="padding:0px;border:0px;width:100%;color:#606060;font-size:20px;margin-bottom:15px;margin-top:15px;">
    <tr style="padding:0px;border:0px;">
      <td style="padding:0px;border:0px;vertical-align:middle">
        <span style="vertical-align:middle;margin-left:3px">
            <strong><a href="https://magnum.travis-ci.com/'.$data['repository']['owner_name'].'/'.$data['repository']['name'].'" style="text-decoration:underline;color:#606060">'.$data['repository']['owner_name'].' / '.$data['repository']['name'].'</a></strong>
            (<a href="'.$data['repository']['url'].'/tree/'.$data['branch'].'" style="text-decoration:underline;color:#606060">'.$data['branch'].'</a>)
        </span>
    </td>
    </tr>
  </table>
  <div class="success" id="build" style="border-radius:5px;padding:0px;width:570px;font-size:13px">
    <div class="content">
      <table style="padding:0px;border:0px;width:100%;border-spacing:0">
        <thead>
          <tr style="padding:0px;border:0px;font-weight:700;font-size:18px;background-color:'.$bc_color.';color:'.$fg_color.'">
            <td style="border:0px;border-top:1px solid #808080;border-bottom:1px solid #adadad;width:50px;padding:0px;text-align:center;vertical-align:middle;padding-top:5px;border-left:1px solid #606060;border-top-left-radius:5px">
                <div class="status-image" style="width:25px;background-size:25px;height:30px;margin-left:15px;margin-top:0px;vertical-align:middle">
                    <div style="height:24px;width:24px;border:2px solid '.$fg_color.';border-radius:4px;">'.$icon.'</div>
                </div>
            </td>
            <td class="build-message" style="border:0px;padding:0px 20px 0px 0px;vertical-align:middle;border-top:1px solid #808080;border-bottom:1px solid #adadad"><span style="display:inline-block;margin-top:12px;vertical-align:middle"><a href="'.$data['build_url'].'" style="font-weight:bold;text-decoration:underline;color:'.$fg_color.'">Build #'.$data['number'].' '.$status.'.</a></span><div style="float:right;height:45px;"></div></td>
            <td align="right" class="time" style="border:0px;font-weight:normal;font-size:12px;padding:0px 20px 0px 0px;vertical-align:middle;border-top:1px solid #808080;border-bottom:1px solid #adadad;border-right:1px solid #606060;border-top-right-radius:5px">
                <span style="vertical-align:middle">Build in '._humantime($data['duration']).'</span>
            </td>
          </tr>
        </thead>
        <tbody style="margin-bottom:40px">
          <tr style="padding:0px;border:0px">
            <td class="profile-image" style="border:0px;height:20px;width:50px;padding:0px;border-left:1px solid #adadad;padding-top:20px;padding-bottom:5px;text-align:center">&nbsp;</td>
            <td class="grey" style="border:0px;color:#808080;padding:10px 20px 10px 0px;height:20px;padding-top:20px;padding-bottom:5px"><strong>'.$data['committer_name'].'</strong></td>
            <td align="right" class="grey" style="border:0px;color:#808080;padding:10px 20px 10px 0px;height:20px;border-right:1px solid #adadad;padding-top:20px;padding-bottom:5px">
                <a href="'.$data['repository']['url'].'/commit/'.$data['commit'].'" style="text-decoration:none;font-weight:bold;color:#57769d">Commit</a> /
                <a href="'.$data['compare_url'].'" style="text-decoration:none;font-weight:bold;color:#57769d">Changeset &#8702;</a>
            </td>
          </tr>
          <tr style="padding:0px;border:0px">
            <td style="border:0px;height:20px;width:50px;padding:0px;border-left:1px solid #adadad;border-bottom-left-radius:5px;border-bottom:1px solid #adadad">&nbsp;</td>
            <td class="grey" colspan="2" style="border:0px;color:#808080;padding:10px 20px 10px 0px;height:20px;border-right:1px solid #adadad;padding-bottom:20px;padding-top:0px;border-bottom:1px solid #adadad;border-bottom-right-radius:5px">'.nl2br(htmlentities($data['message'])).'</td>
          </tr>
          </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>';

    mail(
        $recipients,
        $subject,
        $message,
        $headers,
        '-f'.$from
    );
}


$headers = array_change_key_case(getallheaders(), CASE_UPPER);
$secret  = hash('sha256', $headers['TRAVIS-REPO-SLUG'].'UA2TnLisELk6rr7prsvr');

if (empty($headers['AUTHORIZATION']) || ($headers['AUTHORIZATION'] != $secret)) {
    _log($headers['AUTHORIZATION']);
    _log($secret);
    exit;
}

$payload = json_decode($_POST['payload'], true);

_log($payload);

notify_dashing($payload);
notify_dev_team($payload);
