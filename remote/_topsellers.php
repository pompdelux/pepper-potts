<?php
$max = 10;
$gb_filter = '';

$filter = (isset($_GET['filter']) ? strtoupper($_GET['filter']) : '');
switch ($filter) {
    case 'G':
        $max = 20;
        $gb_filter = "
            AND
                ol.products_id IN (
                    SELECT
                        products_id
                    FROM
                        search_products_tags
                    WHERE
                        token LIKE 'G\_%'
                )
        ";
        break;

    case 'B':
        $max = 20;
        $gb_filter = "
            AND
                ol.products_id IN (
                    SELECT
                        products_id
                    FROM
                        search_products_tags
                    WHERE
                        token LIKE 'B\_%'
                )
        ";
        break;

    case 'L':
        $max = 20;
        $gb_filter = "
            AND
                ol.products_id IN (
                    SELECT
                        products_id
                    FROM
                        search_products_tags
                    WHERE
                        token LIKE 'LB\_%'
                        OR
                        token LIKE 'LG\_%'
                )
        ";
        break;
}


$con = mysqli_connect("192.168.2.118", "pdl_dk", "ceineeF8f3G", "pdl_dk");
$sql = "
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
    {$gb_filter}
    GROUP BY
        ol.products_name
    ORDER BY
        highscore DESC,
        products_name
    LIMIT
        {$max}
";
error_log($sql);
$res = mysqli_query($con, $sql);

$data = [];
while ($record = mysqli_fetch_assoc($res)) {
    $data[] = $record;
}

die(json_encode($data));
