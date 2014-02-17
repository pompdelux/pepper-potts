<?php

$con = mysqli_connect("192.168.2.118", "pdl_dk", "ceineeF8f3G", "pdl_dk");
$res = mysqli_query($con, "
    SELECT
        ol.products_name,
        COUNT(ol.products_name) AS highscore
    FROM
        orders_lines AS ol
    JOIN
        orders AS o
        ON (
            o.id = ol.orders_id
        )
    WHERE
        o.created_at > (NOW() - INTERVAL 24 HOUR)
    AND
        o.state >= 30
    AND
        ol.type = 'product'
    GROUP BY
        ol.products_name
    ORDER BY
        highscore DESC, products_name
    LIMIT
        10
");

$data = [];
while ($record = mysqli_fetch_assoc($res)) {
    $data[] = $record;
}

die(json_encode($data));
