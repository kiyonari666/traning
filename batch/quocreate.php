<?php
//http://192.168.13.48/training/batch/quocreate.php

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
    $sql .= "'見積プレデータ',";
    $sql .= "1000000000,";
    $sql .= "'2023-02-09',";
    $sql .= "'2023-02-10',";
    if ((int)$i <= 20) {
        $sql .= "1,";
    } elseif ((int)$i > 20 && (int)$i <= 40) {
        $sql .= "2,";
    } elseif ((int)$i > 40 && (int)$i <= 60) {
        $sql .= "9,";
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
    <?php echo 'insert into quotations (company_id, no, title, total, validity_period, due_date, status, created, modified)
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