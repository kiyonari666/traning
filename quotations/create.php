<?php
require_once('./../const/status.php');
require_once('./../config/database.php');
require_once('./../function/quote.php');
require_once('./../function/common.php');

$listPath = './list.php?companyId=' . $_GET['companyId'];

// 見積番号prefix+連番生成部
$sql = "select prefix from companies where id=:companyId";
$stmt = $db->prepare($sql);
$stmt->bindValue(':companyId', $_GET['companyId'], PDO::PARAM_STR);
$stmt->execute();
$resPrefix = $stmt->fetch();

$sql = "select no from quotations where company_id=:companyId";
$stmt = $db->prepare($sql);
$stmt->bindValue(':companyId', $_GET['companyId'], PDO::PARAM_STR);
$stmt->execute();
$res = $stmt->fetchAll();

$count = count($res) ?? '';
$count += 1;
if ($count < 10) {
    $digits = 9;
} elseif ($count >= 10 && $count < 100) {
    $digits = 8;
} elseif ($count >= 100 && $count < 1000) {
    $digits = 7;
} elseif ($count >= 1000 && $count < 10000) {
    $digits = 6;
} elseif ($count >= 10000 && $count < 100000) {
    $digits = 5;
} elseif ($count >= 100000 && $count < 1000000) {
    $digits = 4;
} elseif ($count >= 1000000 && $count < 10000000) {
    $digits = 3;
} elseif ($count >= 10000000 && $count < 100000000) {
    $digits = 2;
} elseif ($count >= 100000000 && $count < 1000000000) {
    $digits = 1;
} elseif ($count >= 9999999999) {
    $upperLimit = '登録データ上限を超えています';
    $_POST = '';
}
$no = $resPrefix['prefix'] .= str_pad($count, $digits, '0', STR_PAD_LEFT);

$values = [
    'title' => "",
    'total' => "",
    'validity_period' => "",
    'due_date' => "",
    'status' => ""
];

if (!empty($_POST)) {
    $values = $_POST;
    $errors = [];
    // 件名バリテーション
    if (empty($_POST['title'])) {
        $errors['title'] = '必須入力項目です';
    } elseif (mb_strlen($_POST['title']) > 64) {
        $errors['title'] = '64文字以内で入力して下さい';
    }
    // 金額バリテーション
    if (empty($_POST['total'])) {
        $errors['total'] = '入力必須項目です';
    } elseif (!preg_match('/^[0-9]*$/', $_POST['total'])) {
        $errors['total'] = '半角英数字で入力してください';
    } elseif (mb_strlen($_POST['total']) > 10) {
        $errors['total'] = '入力上限を超えています';
    }
    // 見積書有効期限バリテーション
    if (empty($_POST['validity_period'])) {
        $errors['validity_period'] = '必須入力項目です';
    } elseif (!preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['validity_period'])) {
        $errors['validity_period'] = '日付は、20xx-01-01の形式で入力して下さい';
    }
    // 納期バリテーション
    if (empty($_POST['due_date'])) {
        $errors['due_date'] = '必須入力項目です';
    } elseif (!preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['due_date'])) {
        $errors['due_date'] = '日付は、20xx-01-01の形式で入力して下さい';
    } elseif (empty($_POST['validity_period']) && preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['due_date'])) {
        $errors['due_date'] = '見積書有効期限を先に設定してください';
    } elseif ($_POST['due_date'] < $_POST['validity_period'] && preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['due_date'])) {
        $errors['due_date'] = '納期日付は、見積書有効期限日付より、後日に設定してください';
    }
    // 状態バリテーション
    if (empty($_POST['status'])) {
        $errors['status'] = '必須入力項目です';
    }

    // テーブルデータ挿入部
    if (empty($errors)) {
        $sql = "insert into quotations (company_id, no, title, total, validity_period, due_date, status, created, modified) values (:company_id, :no, :title, :total, :validity_period, :due_date, :status, NOW(), NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':company_id', $_GET['companyId'], PDO::PARAM_STR);
        $stmt->bindValue(':no', $no, PDO::PARAM_STR);
        $stmt->bindValue(':title', $_POST['title'], PDO::PARAM_STR);
        $stmt->bindValue(':total', $_POST['total'], PDO::PARAM_STR);
        $stmt->bindValue(':validity_period', $_POST['validity_period'], PDO::PARAM_STR);
        $stmt->bindValue(':due_date', $_POST['due_date'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $_POST['status'], PDO::PARAM_STR);
        $stmt->execute();
        header('Location:' . $listPath);
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
            <h1>見積作成</h1>
            <a href="./list.php?companyId=<?php echo $_GET['companyId']; ?>">戻る</a>
        </header>

        <div class="container">
            <!-- 新規登録フォーム -->
            <form action="" method="post">
                <table>
                    <tr>
                        <th><p>見積番号</p></th>
                        <?php if (!empty($upperLimit)) : ?>
                            <td><p><?php echo addNo($no); ?></p></td>
                        <?php else : ?>
                            <td><p><?php echo addNo($no); ?></p></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <th><p>見積名</p></th>
                        <td>
                            <input type="text" name="title" placeholder="見積名" value="<?php echo $values['title'] ?>">
                            <?php if (!empty($errors['title'])) : ?>
                                <div class="valiError"><?php echo $errors['title']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>会社名</p></th>
                        <td><p><?php echo h($_GET['name']); ?></p></td>
                    </tr>
                    <tr>
                        <th><p>金額</p></th>
                        <td>
                            <input type="text" name="total" value="<?php echo $values['total']; ?>">
                            <?php if (!empty($errors['total'])) : ?>
                                <div class="valiError"><?php echo $errors['total']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>見積書有効期限</p></th>
                        <td>
                            <input type="text" name="validity_period" placeholder="20XX-00-00" value="<?php echo $values['validity_period']; ?>">                     
                            <?php if (!empty($errors['validity_period'])) : ?>
                                <div class="valiError"><?php echo $errors['validity_period']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>納期</p></th>
                        <td>
                            <input type="text" name="due_date" placeholder="20XX-00-00" value="<?php echo $values['due_date']; ?>">                     
                            <?php if (!empty($errors['due_date'])) : ?>
                                <div class="valiError"><?php echo $errors['due_date']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>状態</p></th>
                        <td>                               
                            <select name="status" class="selectbox">
                            <?php foreach (STATUS_LIST as $key => $val) : ?>
                                <?php if ($key == $values['status']) : ?>
                                    <option value="<?php echo $values['status']; ?>" selected><?php echo STATUS_LIST[$values['status']]; ?></option>
                                <?php else : ?>
                                    <option value="" hidden>選択してください</option>               
                                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?> 
                            </select>
                            <?php if (!empty($errors['status'])) : ?>
                                <br><div class="valiError"><?php echo $errors['status']; ?></div>
                            <?php endif; ?>
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