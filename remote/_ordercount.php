<?php
require '/var/www/pompdelux/shared/cron/config.php';

$name_map = [
    'vip' => '.DK/.COM',
    'no'  => '.NO',
    'se'  => '.SE',
    'nl'  => '.NL',
    'fi'  => '.FI',
    'de'  => '.DE',
#    'at'  => '.AT',
    'ch'  => '.CH',
];

$sql = "
    SELECT COUNT(*) AS order_count
    FROM orders
    WHERE created_at >= (NOW() - INTERVAL 1 HOUR)
    AND state > 20
";

$data = [];
$total = 0;
foreach ($_databases as $name => $connection) {
    if (empty($name_map[$name])) {
        continue;
    }

    $stm = $connection->prepare($sql);
    $stm->execute();

    $value = $stm->fetchColumn();
    $total += $value;

    $data[] = [
        'label' => $name_map[$name],
        'value' => $value,
    ];
}

$data[] = [
    'label' => 'Total',
    'value' => $total,
];

die(json_encode($data));
