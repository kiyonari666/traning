<?php
require_once('./../const/status.php');
require_once('./../config/database.php');
require_once('./../function/common.php');

$listPath = './list.php?id=' . $_GET['company_id'];

// データ取得部
$sql = "select * from quotations where id=:id";
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$res = $stmt->fetch();

 // レコード変更部
 $postData = $_POST;
if (!empty($postData)) {
        $sql = "update quotations set title=:title, total=:total, validity_period=:validity_period, due_date=:due_date, status=:status, modified=NOW() where id=:id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':title', $postData['title'], PDO::PARAM_STR);
        $stmt->bindValue(':total', $postData['total'], PDO::PARAM_STR);
        $stmt->bindValue(':validity_period', $postData['validity_period'], PDO::PARAM_STR);
        $stmt->bindValue(':due_date', $postData['due_date'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $postData['status'], PDO::PARAM_STR);
        $stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $stmt->execute();
        header('Location:' . $listPath);
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
            <h1 class="headerTitle">見積編集</h1>
            <a href="./list.php?id=<?php echo $_GET['company_id']; ?>">戻る</a>
        </header>

        <div class="container">
            <!-- 登録情報編集フォーム -->
            <form action="" method="post">
                <table>
                    <tr>
                        <th><p>見積番号</p></th>
                        <td><p><?php echo h($res['no']); ?></p></td>
                    </tr>
                    <tr>
                        <th><p>見積名</p></th>
                        <td><input type="text" name="title" value="<?php echo h($res['title']); ?>"></td>
                    </tr>
                    <tr>
                        <th><p>会社名</p></th>
                        <td><p><?php echo $_GET['name'] ?></p></td>
                    </tr>
                    <tr>
                        <th><p>金額</p></th>
                        <td><input type="text" name="total" value="<?php echo h($res['total']); ?>"></td>
                    </tr>
                    <tr>
                        <th><p>見積書有効期限</p></th>
                        <td><input type="text" name="validity_period" value="<?php echo h($res['validity_period']); ?>"></td>
                    </tr>
                    <tr>
                        <th><p>納期</p></th>
                        <td><input type="text" name="due_date" value="<?php echo h($res['due_date']); ?>"></td>
                    </tr>
                    <tr>
                        <th><p>状態</p></th>
                        <td>                               
                            <select name="status" class="selectbox">
                            <?php foreach (STATUS_LIST as $key => $val) : ?>
                                <option value="" selected hidden>選択してください</option>               
                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                            <?php endforeach; ?> 
                            </select><br>
                        </td> 
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