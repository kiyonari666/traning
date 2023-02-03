<?php
require_once('./../config/database.php');
require_once('./../const/status.php');
require_once('./../function/common.php');
require_once('./../function/invoice.php');

$listPath = './list.php?companyId=' . $_GET['companyId'];

// データ取得部
$sql = "select * from invoices where id=:id";
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$res = $stmt->fetch();

// レコード削除部
if (!empty($_POST)) {
    $sql = "delete from invoices where id=:id";
    $stmt = $db->prepare($sql);
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
            <h1 class="headerTitle">請求削除</h1>
            <a href="./list.php?companyId=<?php echo $_GET['companyId']; ?>">戻る</a>
        </header>

        <div class="container">
            <!-- 登録情報削除 -->
            <form action="" method="post">
                <table>
                    <tr>
                        <th><p>請求番号</p></th>
                        <td><p><?php echo h(addI($res['no'])); ?></p></td>
                    </tr>
                    <tr>
                        <th><p>請求名</p></th>
                        <td><p><?php echo h($res['title']); ?></p></td>
                    </tr>
                    <tr>
                        <th><p>会社名</p></th>
                        <td><p><?php echo h($_GET['name']); ?></p></td>
                    </tr>
                    <tr>
                        <th><p>金額</p></th>
                        <td><p><?php echo h(addSeparator($res['total'])); ?>円</p></td>
                    </tr>
                    <tr>
                        <th><p>請求期限</p></th>
                        <td><p><?php echo h($res['payment_deadline']); ?></p></td>
                    </tr>
                    <tr>
                        <th><p>請求日</p></th>
                        <td><p><?php echo h($res['date_of_issue']); ?></p></td>
                    </tr>
                    <tr>
                        <th><p>見積番号</p></th>
                        <td><p><?php echo h($res['quotation_no']); ?></p></td>
                    </tr>
                    <tr>
                        <th><p>状態</p></th>
                        <td><p><?php echo STATUS_LIST_I[h($res['status'])]; ?></p></td>
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