<?php
require_once('./../const/prefecture.php');
require_once('./../config/database.php');
require_once('./../function/company.php');

// 会社名バリテーション
if (empty($_POST['name']) || mb_strlen($_POST['name']) >= 64) {
    $error['name'] = '64文字以内で入力して下さい';
} else {
    $textKeep['name'] = $_POST['name'];
}
// 担当者名バリテーション
if (empty($_POST['manager_name']) || mb_strlen($_POST['manager_name']) >= 32) {
    $error['manager_name'] = '32文字以内で入力して下さい';
} else {
    $textKeep['manager_name'] = $_POST['manager_name'];
}
// 電話番号バリテーション
if (empty($_POST['phone_number']) || !preg_match('/^[0-9]{11}$/', $_POST['phone_number'])) {
    $error['phone_number'] = '半角整数のみ、ハイフンなしで入力して下さい';
} else {
    $textKeep['phone_number'] = $_POST['phone_number'];
}
// 郵便番号バリテーション
if (empty($_POST['postal_code']) || !preg_match('/^[0-9]{7}$/', $_POST['postal_code'])) {
    $error['postal_code'] = '半角整数のみ、ハイフンなしで入力して下さい';
} else {
    $textKeep['postal_code'] = $_POST['postal_code'];
}
// 都道府県コードバリテーション
if (empty($_POST['prefecture_code'])) {
    $error['prefecture_code'] = '都道府県を選んでください';
} else {
    $textKeep['prefecture_code'] = $_POST['prefecture_code'];
}
// 住所バリテーション
if (empty($_POST['address']) || mb_strlen($_POST['address']) >= 100) {
    $error['address'] = '100文字以内で入力して下さい';
} else {
    $textKeep['address'] = $_POST['address'];
}
// メールアドレスバリテーション
if (empty($_POST['mail_address']) || !preg_match('/^[A-Za-z0-9]+\.*[\w\-]*\.*[A-Za-z0-9]+@+[A-Za-z0-9]+[\w\-]*\.+[A-Za-z]+\.*[A-Za-z]*$/', $_POST['mail_address'])) {
    $error['mail_address'] = '別のメールアドレスをお試しください';
} else {
    $textKeep['mail_address'] = $_POST['mail_address'];
}
// プレフィックスバリテーション
if (empty($_POST['prefix']) || mb_strlen($_POST['prefix']) >= 16 || !preg_match('/^[A-Za-z0-9]+$/', $_POST['prefix'])) {
    $error['prefix'] = '半角英数文字のみ、16文字以内で入力して下さい';
} else {
    $textKeep['prefix'] = $_POST['prefix'];
}

// DB挿入部
if (empty($error)) {
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
                            <?php if (!empty($textKeep['name'])) : ?>
                                <input type="text" name="name" value="<?php echo $textKeep['name']; ?>">
                            <?php else : ?>    
                                <input type="text" name="name" placeholder="株式会社〇〇〇〇">
                            <?php endif; ?>                               
                            <?php if (!empty($error['name']) && empty($messageSwitch)) : ?>
                                <div class="valiError"><?php echo $error['name']; ?></div>
                            <?php endif; ?>
                        </td>                        
                    </tr>
                    <tr>
                        <th><p>担当者名</p></th>
                        <td>
                            <?php if (!empty($textKeep['manager_name'])) : ?>
                                <input type="text" name="manager_name" value="<?php echo $textKeep['manager_name']; ?>">
                            <?php else : ?>    
                                <input type="text" name="manager_name" placeholder="担当者名">
                            <?php endif; ?>                           
                            <?php if (!empty($error['manager_name'])) : ?>
                                <div class="valiError"><?php echo $error['manager_name']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>電話番号</p></th>
                        <td>
                            <?php if (!empty($textKeep['phone_number'])) : ?>
                                <input type="text" name="phone_number" value="<?php echo $textKeep['phone_number']; ?>">
                            <?php else : ?>    
                                <input type="text" name="phone_number">
                            <?php endif; ?>                           
                            <?php if (!empty($error['phone_number'])) : ?>
                                <div class="valiError"><?php echo $error['phone_number']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!-- 住所行 -->
                    <tr class="addressRowLayout">
                        <th><p>住所</p></th>
                        <td>
                            <span class="title">郵便番号</span>
                            <?php if (!empty($textKeep['postal_code'])) : ?>
                                <input type="text" name="postal_code" value="<?php echo $textKeep['postal_code']; ?>"><br>
                            <?php else : ?>    
                                <input type="text" name="postal_code"><br>
                            <?php endif; ?>                           
                            <?php if (!empty($error['postal_code'])) : ?>
                                <br><div class="valiError"><?php echo $error['postal_code']; ?></div>
                            <?php endif; ?>
                        </td> 
                    </tr>
                    <tr class="addressRowLayout">
                        <th></th>    
                        <td>
                            <span class="title">都道府県コード</span>                                
                            <select name="prefecture_code">
                            <option value="" selected hidden>選択してください</option>
                            <?php foreach (PREFECTURES_ARRAY as $key => $val) : ?>               
                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                            <?php endforeach; ?> 
                            </select><br>
                            <?php if (!empty($error['prefecture_code'])) : ?>
                                <br><div class="valiError"><?php echo $error['prefecture_code']; ?></div>
                            <?php endif; ?>
                        </td> 
                    </tr>
                    <tr class="addressRowLayout">
                        <th></th>    
                        <td>
                            <span class="title">住所</span>
                            <?php if (!empty($textKeep['address'])) : ?>
                                <input type="text" name="address" value="<?php echo $textKeep['address']; ?>"><br>
                            <?php else : ?>    
                                <input type="text" name="address"><br>
                            <?php endif; ?>                           
                            <?php if (!empty($error['address'])) : ?>
                                <br><div class="valiError"><?php echo $error['address']; ?></div>
                            <?php endif; ?>
                        </td> 
                    </tr>                             
                    <!-- 住所行終 -->
                    <tr>
                        <th><p>メールアドレス</p></th>
                        <td>
                            <?php if (!empty($textKeep['mail_address'])) : ?>
                                <input type="text" name="mail_address" value="<?php echo $textKeep['mail_address']; ?>">
                            <?php else : ?>    
                                <input type="text" name="mail_address">
                            <?php endif; ?>                            
                            <?php if (!empty($error['mail_address'])) : ?>
                                <div class="valiError"><?php echo $error['mail_address']; ?></div>
                            <?php endif; ?>                          
                        </td>
                    </tr>
                    <tr>
                        <th><p>プレフィックス</p></th>
                        <td>
                            <?php if (!empty($textKeep['prefix'])) : ?>
                                <input type="text" name="prefix" value="<?php echo $textKeep['prefix']; ?>">
                            <?php else : ?>    
                                <input type="text" name="prefix">
                            <?php endif; ?>                          
                            <?php if (!empty($error['prefix'])) : ?>
                                <div class="valiError"><?php echo $error['prefix']; ?></div>
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