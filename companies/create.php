<?php
require_once('./../const/prefecture.php');
require_once('./../config/database.php');
require_once('./../function/company.php');

// プレフィックス重複バリテーション
$sql = 'select prefix from companies';
$stmt = $db->prepare($sql);
$stmt->execute();
$res = $stmt->fetchAll();

if (!empty($_POST)) {
    foreach ($res as $val) {
        $array[] = $val['prefix'] === $_POST['prefix'];
        $count += 1;
    }
    $duplication = '';
    if (in_array(true, $array)) {
        $duplication = '重複する番号は使用できません';
        $_POST['prefix'] = '';
    }
}

$values = [
    'name' => '',
    'manager_name' => '',
    'phone_number' => '',
    'postal_code' => '',
    'prefecture_code' => '',
    'address' => '',
    'mail_address' => '',
    'prefix' => ''
];

if (!empty($_POST)) {
    $values = $_POST;
    $errors = [];
    // 会社名バリテーション
    if (empty($_POST['name'])) {
        $errors['name'] = '必須入力項目です';
    } elseif (mb_strlen($_POST['name']) > 64) {
        $errors['name'] = '64文字以内で入力して下さい';
    }
    //担当者名バリテーション
    if (empty($_POST['manager_name'])) {
        $errors['manager_name'] = '必須入力項目です';
    } elseif (mb_strlen($_POST['manager_name']) > 32) {
        $errors['manager_name'] = '32文字以内で入力して下さい';
    }
    // 電話番号バリテーション
    if (empty($_POST['phone_number'])) {
        $errors['phone_number'] = '必須入力項目です';
    } elseif (!preg_match('/^[0-9]{11}$/', $_POST['phone_number'])) {
        $errors['phone_number'] = '半角整数のみ、ハイフンなしで入力して下さい';
    }
    // 郵便番号バリテーション
    if (empty($_POST['postal_code'])) {
        $errors['postal_code'] = '必須入力項目です';
    } elseif (!preg_match('/^[0-9]{7}$/', $_POST['postal_code'])) {
        $errors['postal_code'] = '半角整数のみ、ハイフンなしで入力して下さい';
    }
    // 都道府県コードバリテーション
    if (empty($_POST['prefecture_code'])) {
        $errors['prefecture_code'] = '都道府県を選んでください';
    }
    // 住所バリテーション
    if (empty($_POST['address'])) {
        $errors['address'] = '必須入力項目です';
    } elseif (mb_strlen($_POST['address']) > 100) {
        $errors['address'] = '100文字以内で入力して下さい';
    }
    // メールアドレスバリテーション
    if (empty($_POST['mail_address'])) {
        $errors['mail_address'] = '必須入力項目です';
    } elseif (!preg_match('/^[A-Za-z0-9]+\.*[\w\-]*\.*[A-Za-z0-9]+@+[A-Za-z0-9]+[\w\-]*\.+[A-Za-z]+\.*[A-Za-z]*$/', $_POST['mail_address'])) {
        $errors['mail_address'] = '別のメールアドレスをお試しください';
    }
    // プレフィックスバリテーション
    if (empty($_POST['prefix'])) {
        $errors['prefix'] = '必須入力項目です';
    } elseif (mb_strlen($_POST['prefix']) > 8) {
        $errors['prefix'] = '8文字以内で入力して下さい';
    } elseif (!preg_match('/^[A-Za-z0-9]+$/', $_POST['prefix'])) {
        $errors['prefix'] = '半角英数文字のみで入力して下さい';
    }

    // DB挿入部
    if (empty($errors)) {
        $sql = "insert into companies (name, manager_name, phone_number, postal_code,prefecture_code, address, mail_address, prefix, created, modified) values (:name, :manager_name, :phone_number, :postal_code, :prefecture_code, :address, :mail_address, :prefix, NOW(), NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindValue(':manager_name', $_POST['manager_name'], PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $_POST['phone_number'], PDO::PARAM_STR);
        $stmt->bindValue(':postal_code', $_POST['postal_code'], PDO::PARAM_STR);
        $stmt->bindValue(':prefecture_code', $_POST['prefecture_code'], PDO::PARAM_STR);
        $stmt->bindValue(':address', $_POST['address'], PDO::PARAM_STR);
        $stmt->bindValue(':mail_address', $_POST['mail_address'], PDO::PARAM_STR);
        $stmt->bindValue(':prefix', $_POST['prefix'], PDO::PARAM_STR);
        $stmt->execute();
        header('Location: ./list.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./../stylesheet/style.css">
</head>
<body>
    <div class="wrap">

        <header>
            <h1>会社登録</h1>
            <a href="./list.php">戻る</a>
        </header>

        <div class="container">
            <!-- 新規登録フォーム -->
            <form action="" method="post">
                <table>
                    <tr>
                        <th><p>会社名</p></th>
                        <td>                           
                            <input type="text" name="name" placeholder="株式会社〇〇〇〇" value="<?php echo $values['name']; ?>">
                            <?php if (!empty($errors['name'])) : ?>
                                <div class="valiError"><?php echo $errors['name']; ?></div>
                            <?php endif; ?>
                        </td>                        
                    </tr>
                    <tr>
                        <th><p>担当者名</p></th>
                        <td>
                            <input type="text" name="manager_name" placeholder="担当者名" value="<?php echo $values['manager_name']; ?>">                        
                            <?php if (!empty($errors['manager_name'])) : ?>
                                <div class="valiError"><?php echo $errors['manager_name']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>電話番号</p></th>
                        <td>
                            <input type="text" name="phone_number" value="<?php echo $values['phone_number']; ?>">                          
                            <?php if (!empty($errors['phone_number'])) : ?>
                                <div class="valiError"><?php echo $errors['phone_number']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!-- 住所行 -->
                    <tr class="addressRowLayout">
                        <th><p>住所</p></th>
                        <td>
                            <span class="title">郵便番号</span>
                            <input type="text" name="postal_code" value="<?php echo $values['postal_code']; ?>"><br>                         
                            <?php if (!empty($errors['postal_code'])) : ?>
                                <br><div class="valiError"><?php echo $errors['postal_code']; ?></div>
                            <?php endif; ?>
                        </td> 
                    </tr>
                    <tr class="addressRowLayout">
                        <th></th>    
                        <td>
                            <span class="title">都道府県コード</span>                                
                            <select name="prefecture_code"> 
                                <?php foreach (PREFECTURES_ARRAY as $key => $val) : ?>               
                                    <?php if ($key === $res['prefecture_code']) : ?>
                                        <option value="<?php echo $res['prefecture_code']; ?>" selected><?php echo PREFECTURES_ARRAY[$res['prefecture_code']]; ?></option>
                                    <?php else : ?>
                                        <option value="" hidden>都道府県を選んでください</option>
                                        <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select><br>
                            <?php if (!empty($errors['prefecture_code'])) : ?>
                                <br><div class="valiError"><?php echo $errors['prefecture_code']; ?></div>
                            <?php endif; ?>
                        </td> 
                    </tr>
                    <tr class="addressRowLayout">
                        <th></th>    
                        <td>
                            <span class="title">住所</span>
                                <input type="text" name="address" value="<?php echo $values['address']; ?>"><br>                      
                            <?php if (!empty($errors['address'])) : ?>
                                <br><div class="valiError"><?php echo $errors['address']; ?></div>
                            <?php endif; ?>
                        </td> 
                    </tr>                             
                    <!-- 住所行終 -->
                    <tr>
                        <th><p>メールアドレス</p></th>
                        <td>
                            <input type="text" name="mail_address" value="<?php echo $values['mail_address']; ?>">                         
                            <?php if (!empty($errors['mail_address'])) : ?>
                                <div class="valiError"><?php echo $errors['mail_address']; ?></div>
                            <?php endif; ?>                          
                        </td>
                    </tr>
                    <tr>
                        <th><p>プレフィックス</p></th>
                        <td>
                            <input type="text" name="prefix" value="<?php echo $values['prefix']; ?>">                        
                            <?php if (!empty($errors['prefix']) && empty($duplication)) : ?>
                                <div class="valiError"><?php echo $errors['prefix']; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($duplication)) : ?>
                                <div class="valiError"><?php echo $duplication; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
        </div>

        <footer>
                <input type="submit" class="submitButton" value="新規登録">
            </form>
        </footer>

    </div>
</body>
</html>