<?php
//http://192.168.13.48/training/batch/invcreate.php

require_once('./../config/database.php');

$num = 60;

$sql = "select * from companies where id=1";
$stmt = $db->prepare($sql);
$stmt->execute();
$res = $stmt->fetch();

$prefix = $res['prefix'];

for ($i = 1; $i <= $num; $i++) {
    $sql = '(';
    $sql .= "1,";
    $sql .= '\'';
    $sql .= $prefix.str_pad($i, 8, '0', STR_PAD_LEFT);
    $sql .= '\',';
    $sql .= '\'';
    $sql .= $prefix.str_pad($i, 8, '0', STR_PAD_LEFT);
    $sql .= '\',';
    $sql .= "'請求プレデータ',";
    $sql .= "1000000000,";
    $sql .= "'2023-02-10',";
    $sql .= "'2023-02-09',";
    if ((int)$i <= 30) {
        $sql .= "1,";
    } elseif ((int)$i > 30 && (int)$i <= 60) {
        $sql .= "2,";
    }
    $sql .= "NOW(),";
    $sql .= "NOW()";
    $sql .= ')';
    $array[] = $sql;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php echo 'insert into invoices (company_id, no, quotation_no, title, total, payment_deadline, date_of_issue, status, created, modified)
     <br> values'; ?><br>
    <?php foreach ($array as $key => $data) : ?>
        <?php echo ($data); ?>
        <?php if ($key === array_key_last($array)) : ?>
            <?php echo ';'; ?>
        <?php else : ?>
                <?php echo ','; ?>
        <?php endif; ?>
        <br>
    <?php endforeach; ?>
</body>
</html>