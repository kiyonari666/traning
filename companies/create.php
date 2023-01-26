<?php
require_once('./../const/prefecture.php');
require_once('./../config/database.php');
require_once('./../function/company.php');

// DB挿入部
if (!empty($_POST)) {
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
            <form action="./createVali.php" method="post">
                <table>
                    <tr>
                        <th><p>会社名</p></th>
                        <td><input type="text" name="name" placeholder="株式会社〇〇〇〇"></td>
                    </tr>
                    <tr>
                        <th><p>担当者名</p></th>
                        <td><input type="text" name="manager_name" placeholder="担当者名"></td>
                    </tr>
                    <tr>
                        <th><p>電話番号</p></th>
                        <td><input type="text" name="phone_number"></td>
                    </tr>
                    <!-- 住所行 -->
                    <tr class="addressRowLayout">
                        <th><p>住所</p></th>
                        <td>
                            <span class="title">郵便番号</span>
                            <input type="text" name="postal_code"><br>
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
                        </td> 
                    </tr>
                    <tr class="addressRowLayout">
                        <th></th>    
                        <td>
                            <span class="title">住所</span>
                            <input type="text" name="address">
                        </td> 
                    </tr>                             
                    <!-- 住所行終 -->
                    <tr>
                        <th><p>メールアドレス</p></th>
                        <td><input type="text" name="mail_address"></td>
                    </tr>
                    <tr>
                        <th><p>プレフィックス</p></th>
                        <td><input type="text" name="prefix"></td>
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