<?php
//http://192.168.13.48/training/batch/comcreate.php

$num = 60;

for ($i = 1; $i <= $num; $i++) {
    $sql = '(';
    $sql .=  "'会社プレデータ{$i}',";
    $sql .= "'テストさん',";
    $sql .= "08011111111,";
    $sql .= "1060023,";
    $sql .= "13,";
    $sql .= "'新宿区西新宿',";
    $sql .= "'test@gmail.com',";
    $sql .= '\'';
    $sql .= 'test'.str_pad($i, 4, '0', STR_PAD_LEFT);
    $sql .= '\',';
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
    <?php echo 'insert into companies 
        (name, manager_name, phone_number, postal_code, prefecture_code, address,  
        mail_address, prefix, created, modified) <br> values'; ?><br>
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