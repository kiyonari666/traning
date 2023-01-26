<?php
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

     // レコード削除部
if (isset($_POST['id'])) {
    $sql = "delete from companies where id=:delete";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':delete', $_POST['id'], PDO::PARAM_INT);
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
            <h1 class="headerTitle">会社削除</h1>
            <a class="backButton" href="./list.php">戻る</a>
        </header>

        <div class="container">
            <!-- 登録情報削除 -->
            <form action="" method="post">
                <table>
                    <tr>
                        <th><p>会社名</p></th>
                        <td><p><?php echo h($res['name']); ?></p></td>
                    </tr>
                    <tr>
                        <th><p>担当者名</p></th>
                        <td><p><?php echo h($res['manager_name']); ?></p></td>
                    </tr>
                    <tr>
                        <th><p>電話番号</p></th>
                        <td><p><?php echo h($res['phone_number']); ?></p></td>
                    </tr>
                    <!-- 住所行 -->
                    <tr class="addressRowLayout">
                        <th><p>住所</p></th>
                        <td>
                            <span class="title">郵便番号</span>
                            <?php $postalCode = h($res['postal_code']); ?>
                            <span class="rightText">〒<?php echo h(addHyphen($postalCode)); ?></span><br>
                        </td> 
                    </tr>
                    <tr class="addressRowLayout">
                        <th></th>    
                        <td>                                
                            <span class="title">都道府県コード　</span>
                            <span class="rightText"><?php echo h($res['prefecture_code']); ?></span><br>
                        </td> 作った
                    </tr>
                    <tr class="addressRowLayout">
                        <th></th>    
                        <td>
                            <span class="title">住所</span>
                            <span class="rightText"><?php echo h($res['address']); ?></span><br>
                        </td> 
                    </tr>                             
                    <!-- 住所行終 -->
                    <tr>
                        <th><p>メールアドレス</p></th>
                        <td><p><?php echo h($res['mail_address']); ?></p></td>
                    </tr>
                    <tr>
                        <th><p>プレフィックス</p></th>
                        <?php $prefixCode = h($res['prefix']); ?>
                        <td><p><?php echo h(addPrefix($prefixCode)); ?></p></td>
                    </tr>
                </table>
                <input type="hidden" name="id" value= "<?php echo h($res['id']); ?>">    
        </div>

        <footer>
            <input type="submit" class="submitButton" value="登録削除">
            </form>
        </footer>

    </div>
</body>
</html>