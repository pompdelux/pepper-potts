<?php

$file  = '/var/www/github.pompdelux.com/public_html/hook.log';
$mtime = filemtime($file);
$line  = trim(`tail -n 1 $file`);
$by    = explode(' by ', $line);

die(json_encode(array(
    'ts' => date('d/m H:i', $mtime),
    'by' => $by,
)));
