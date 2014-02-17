<?php

$con = mysqli_connect("192.168.2.118", "pdl_dk", "ceineeF8f3G", "pdl_dk");
$res = mysqli_query($con, "
    SELECT COUNT(*)
    FROM orders
    WHERE updated_AT > (NOW() - INTERVAL 5 MINUTE)
");

$record = mysqli_fetch_row($res);

die(json_encode([
    'count' => $record[0]
]));
