<?php
require_once('./../const/status.php');
require_once('./../config/database.php');
require_once('./../function/company.php');

$listPath = './list.php?companyId=' . $_GET['companyId'];

// テーブルデータ挿入部
$postData = $_POST;
if (!empty($postData)) {
        $sql = "insert into quotations (company_id, no, title, total, validity_period, due_date, status, created, modified) values (:company_id, :no, :title, :total, :validity_period, :due_date, :status, NOW(), NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':company_id', $_GET['id'], PDO::PARAM_STR);
        $stmt->bindValue(':no', $postData['no'], PDO::PARAM_STR);
        $stmt->bindValue(':title', $postData['title'], PDO::PARAM_STR);
        $stmt->bindValue(':total', $postData['total'], PDO::PARAM_STR);
        $stmt->bindValue(':validity_period', $postData['validity_period'], PDO::PARAM_STR);
        $stmt->bindValue(':due_date', $postData['due_date'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $postData['status'], PDO::PARAM_STR);
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
            <h1>見積作成</h1>
            <a href="./list.php?companyId=<?php echo $_GET['companyId']; ?>">戻る</a>
        </header>

        <div class="container">
            <!-- 新規登録フォーム -->
            <form action="" method="post">
                <table>
                    <tr>
                        <th><p>見積番号</p></th>
                        <td><input type="text" name="no" placeholder="見積番号"></td>
                    </tr>
                    <tr>
                        <th><p>見積名</p></th>
                        <td><input type="text" name="title" placeholder="見積名"></td>
                    </tr>
                    <tr>
                        <th><p>会社名</p></th>
                        <td><p><?php echo $_GET['name'] ?></p></td>
                    </tr>
                    <tr>
                        <th><p>金額</p></th>
                        <td><input type="text" name="total"></td>
                    </tr>
                    <tr>
                        <th><p>見積書有効期限</p></th>
                        <td><input type="text" name="validity_period" placeholder="20XX-00-00"></td>
                    </tr>
                    <tr>
                        <th><p>納期</p></th>
                        <td><input type="text" name="due_date" placeholder="20XX-00-00"></td>
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
        </div>

        <footer>
                <input type="submit" class="submitButton" value="見積作成">
            </form>
        </footer>

    </div>
</body>
</html>