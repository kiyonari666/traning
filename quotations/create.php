<?php
require_once('./../config/database.php');
require_once('./../const/status.php');
require_once('./../function/common.php');
require_once('./../function/invoice.php');

$listPath = './list.php?companyId=' . $_GET['companyId'];

// 見積番号prefix+連番生成部
$sql = "select prefix from companies where id=:companyId";
$stmt = $db->prepare($sql);
$stmt->bindValue(':companyId', $_GET['companyId'], PDO::PARAM_INT);
$stmt->execute();
$resPrefix = $stmt->fetch();

$sql = "select count(no) as cnt from quotations where company_id=:companyId";
$stmt = $db->prepare($sql);
$stmt->bindValue(':companyId', $_GET['companyId'], PDO::PARAM_INT);
$stmt->execute();
$res = $stmt->fetchAll();

$res[0]['cnt'] += 1;

$upperLimit = '';
if ($res[0]['cnt'] > 99999999) {
     $upperLimit = '登録データ上限を超えています';
     $_POST = '';
}

$no = $resPrefix['prefix'] .= str_pad($res[0]['cnt'], 8, '0', STR_PAD_LEFT);

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
    } elseif ($_POST['due_date'] <= $_POST['validity_period'] && preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['due_date'])) {
        $errors['due_date'] = '納期日付は、見積書有効期限日付より、後日に設定してください';
    }
    // 状態バリテーション
    if (empty($_POST['status'])) {
        $errors['status'] = '必須入力項目です';
    }

    // テーブルデータ挿入部
    if (empty($errors)) {
        $sql = "insert into quotations (company_id, no, title, total, validity_period, due_date, status, created, modified, deleted) values (:company_id, :no, :title, :total, :validity_period, :due_date, :status, NOW(), NOW(), null)";
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
            
            <!-- 登録数上限メッセージ -->
            <?php if ($upperLimit !== '') : ?>
                <div class="valiErrorLimit"><?php echo $upperLimit; ?></div>
            <?php endif; ?>
            
            <!-- 新規登録フォーム -->
            <form action="" method="post">
                <table>
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
                            <?php foreach (STATUS_LIST_Q as $key => $val) : ?>
                                <?php if ($key === (int)$values['status']) : ?>
                                    <option value="<?php echo $values['status']; ?>" selected><?php echo STATUS_LIST_Q[$values['status']]; ?></option>
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