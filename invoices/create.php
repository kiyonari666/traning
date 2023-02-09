<?php
require_once('./../config/database.php');
require_once('./../const/status.php');
require_once('./../function/common.php');
require_once('./../function/invoice.php');

$listPath = './list.php?companyId=' . $_GET['companyId'];

// 請求番号prefix+連番生成部
$sql = "select prefix from companies where id=:companyId";
$stmt = $db->prepare($sql);
$stmt->bindValue(':companyId', $_GET['companyId'], PDO::PARAM_INT);
$stmt->execute();
$resCompanies = $stmt->fetch();

// ?companyIdパラメーターいたずら対策
if (empty($resCompanies)) {
    echo '<script>alert("対応するデータがありません\nトップページへ移動します");</script>';
    echo '<script>location.href="./../companies/list.php";</script>';
}

$sql = "select count(no) as cnt from invoices where company_id=:companyId";
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

$no = $resCompanies['prefix'] .= str_pad($res[0]['cnt'], 8, '0', STR_PAD_LEFT);

$values = [
    'title' => '',
    'total' => '',
    'payment_deadline' => '',
    'date_of_issue' => '',
    'quotation_no' => '',
    'status' => ''
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
        $errors['total'] = '半角数字で入力してください';
    } elseif (mb_strlen($_POST['total']) > 10) {
        $errors['total'] = '入力上限を超えています';
    }
    // 支払期限バリテーション
    if (empty($_POST['payment_deadline'])) {
        $errors['payment_deadline'] = '必須入力項目です';
    } elseif (!preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['payment_deadline'])) {
        $errors['payment_deadline'] = '日付は、20xx-01-01の形式で入力して下さい';
    } elseif (empty($_POST['date_of_issue']) && preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['payment_deadline'])) {
        $errors['payment_deadline'] = '請求日を先に設定してください';
    } elseif ($_POST['payment_deadline'] <= $_POST['date_of_issue'] && preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['payment_deadline'])) {
        $errors['payment_deadline'] = '支払期限は、請求日より、後日に設定してください';
    }
    // 請求日バリテーション
    if (empty($_POST['date_of_issue'])) {
        $errors['date_of_issue'] = '必須入力項目です';
    } elseif (!preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['date_of_issue'])) {
        $errors['date_of_issue'] = '日付は、20xx-01-01の形式で入力して下さい';
    }
    // 見積書番号バリテーション
    if (empty($_POST['quotation_no'])) {
        $errors['quotation_no'] = '必須入力項目です';
    } elseif (!preg_match('/^[a-zA-Z0-9]*$/', $_POST['quotation_no'])) {
        $errors['quotation_no'] = '半角英数字のみで、入力して下さい';
    } elseif (mb_strlen($_POST['quotation_no']) > 100) {
        $errors['quotation_no'] = '100文字以内で入力して下さい';
    }
    // 状態バリテーション
    if (empty($_POST['status'])) {
        $errors['status'] = '必須入力項目です';
    }

    // テーブルデータ挿入部
    if (empty($errors)) {
        $sql = "insert into invoices (company_id, no, title, total, payment_deadline, date_of_issue, quotation_no, status, created, modified, deleted) values (:company_id, :no, :title, :total, :payment_deadline, :date_of_issue, :quotation_no, :status, NOW(), NOW(), null)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':company_id', $_GET['companyId'], PDO::PARAM_STR);
        $stmt->bindValue(':no', $no, PDO::PARAM_STR);
        $stmt->bindValue(':title', $_POST['title'], PDO::PARAM_STR);
        $stmt->bindValue(':total', $_POST['total'], PDO::PARAM_STR);
        $stmt->bindValue(':payment_deadline', $_POST['payment_deadline'], PDO::PARAM_STR);
        $stmt->bindValue(':date_of_issue', $_POST['date_of_issue'], PDO::PARAM_STR);
        $stmt->bindValue(':quotation_no', $_POST['quotation_no'], PDO::PARAM_STR);
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
            <h1>請求作成</h1>
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
                        <th><p>請求名</p></th>
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
                        <th><p>支払期限</p></th>
                        <td>
                            <input type="text" name="payment_deadline" placeholder="20XX-00-00" value="<?php echo $values['payment_deadline']; ?>">                     
                            <?php if (!empty($errors['payment_deadline'])) : ?>
                                <div class="valiError"><?php echo $errors['payment_deadline']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>請求日</p></th>
                        <td>
                            <input type="text" name="date_of_issue" placeholder="20XX-00-00" value="<?php echo $values['date_of_issue']; ?>">                     
                            <?php if (!empty($errors['date_of_issue'])) : ?>
                                <div class="valiError"><?php echo $errors['date_of_issue']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>見積番号</p></th>
                        <td>
                            <input type="text" name="quotation_no" value="<?php echo $values['quotation_no'] ?>">
                            <?php if (!empty($errors['quotation_no'])) : ?>
                                <div class="valiError"><?php echo $errors['quotation_no']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>状態</p></th>
                        <td>                               
                            <select name="status" class="selectbox">
                            <?php foreach (STATUS_LIST_I as $key => $val) : ?>
                                <?php if ($key === (int)$values['status']) : ?>
                                    <option value="<?php echo $values['status']; ?>" selected><?php echo STATUS_LIST_I[$values['status']]; ?></option>
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