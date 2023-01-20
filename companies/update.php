<?php
require_once('./../const/prefecture.php');
require_once('./../config/database.php');
require_once('./../function/common.php');
require_once('./../function/company.php');

// データ取得部
$id = $_GET['id'];
    
$sql = "select * from companies where id=:id";
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$res = $stmt->fetch();


 // レコード変更部
 $postData = $_POST;
if (!empty($postData)) {
    if (updateValidate($postData)) {
        $sql = "update companies set name=:name, manager_name=:manager_name, phone_number=:phone_number, postal_code=:postal_code, prefecture_code=:prefecture_code, address=:address, mail_address=:mail_address, modified=NOW() where id=:id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', $postData['name'], PDO::PARAM_STR);
        $stmt->bindValue(':manager_name', $postData['manager_name'], PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $postData['phone_number'], PDO::PARAM_STR);
        $stmt->bindValue(':postal_code', $postData['postal_code'], PDO::PARAM_STR);
        $stmt->bindValue(':prefecture_code', $postData['prefecture_code'], PDO::PARAM_STR);
        $stmt->bindValue(':address', $postData['address'], PDO::PARAM_STR);
        $stmt->bindValue(':mail_address', $postData['mail_address'], PDO::PARAM_STR);
        $stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
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
            <h1 class="headerTitle">会社編集</h1>
            <a class="backButton" href="./list.php">戻る</a>
        </header>

        <div class="container">
            <!-- 登録情報編集フォーム -->
            <form action="" method="post">
                <table>
                    <tr>
                        <th><p>会社名</p></th>
                        <td><input type="text" name="name" value="<?php echo h($res['name']); ?>"></td>
                    </tr>
                    <tr>
                        <th><p>担当者名</p></th>
                        <td><input type="text" name="manager_name" value="<?php echo h($res['manager_name']); ?>"></td>
                    </tr>
                    <tr>
                        <th><p>電話番号</p></th>
                        <td><input type="text" name="phone_number" value="<?php echo h($res['phone_number']); ?>"></td>
                    </tr>
                    <!-- 住所行 -->
                    <tr class="addressRowLayout">
                        <th><p>住所</p></th>
                        <td>
                            <span class="title">郵便番号</span>
                            <input type="text" name="postal_code" value="<?php echo h($res['postal_code']); ?>"><br>
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
                            <input type="text" name="address" value="<?php echo h($res['address']); ?>">
                        </td> 
                    </tr>                             
                    <!-- 住所行終 -->
                    <tr>
                        <th><p>メールアドレス</p></th>
                        <td><input type="text" name="mail_address" value="<?php echo h($res['mail_address']); ?>"></td>
                    </tr>
                    <tr>
                        <th><p>プレフィックス</p></th>
                        <td><p><?php echo h($res['prefix']); ?></p></td>
                    </tr>
                </table>
                <input type="hidden" name="id" value= "<?php echo h($res['id']); ?>">
        </div>

        <footer>
            <input type="submit" class="submitButton" value="登録変更">
            </form>
        </footer>

    </div>    
</body>
</html>