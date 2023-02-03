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

$no = $res['no'];

if (!empty($_POST)) {
    $res = $_POST;
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
   // 支払期限バリテーション
    if (empty($_POST['payment_deadline'])) {
        $errors['payment_deadline'] = '必須入力項目です';
    } elseif (!preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['payment_deadline'])) {
        $errors['payment_deadline'] = '日付は、20xx-01-01の形式で入力して下さい';
    } elseif (empty($_POST['date_of_issue']) && preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['payment_deadline'])) {
        $errors['payment_deadline'] = '請求日を先に設定してください';
    } elseif ($_POST['payment_deadline'] < $_POST['date_of_issue'] && preg_match('/^[0-9]{4}[-]+[0-9]{2}[-]+[0-9]{2}$/', $_POST['payment_deadline'])) {
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

    // レコード変更部
    if (empty($errors)) {
        $sql = "update invoices set title=:title, total=:total, payment_deadline=:payment_deadline, date_of_issue=:date_of_issue, quotation_no=:quotation_no, status=:status, modified=NOW() where id=:id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':title', $_POST['title'], PDO::PARAM_STR);
        $stmt->bindValue(':total', $_POST['total'], PDO::PARAM_STR);
        $stmt->bindValue(':payment_deadline', $_POST['payment_deadline'], PDO::PARAM_STR);
        $stmt->bindValue(':date_of_issue', $_POST['date_of_issue'], PDO::PARAM_STR);
        $stmt->bindValue(':quotation_no', $_POST['quotation_no'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $_POST['status'], PDO::PARAM_STR);
        $stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
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
            <h1 class="headerTitle">見積編集</h1>
            <a href="./list.php?companyId=<?php echo $_GET['companyId']; ?>">戻る</a>
        </header>

        <div class="container">
            <!-- 登録情報編集フォーム -->
            <form action="" method="post">
                <table>
                    <tr>
                        <th><p>請求番号</p></th>
                        <td><p><?php echo addi($no); ?></p></td>                        
                    </tr>
                    <tr>
                        <th><p>請求名</p></th>
                        <td>
                            <input type="text" name="title" value="<?php echo h($res['title']); ?>">
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
                            <input type="text" name="total" value="<?php echo h($res['total']); ?>">
                            <?php if (!empty($errors['total'])) : ?>
                                <div class="valiError"><?php echo $errors['total']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>支払期限</p></th>
                        <td>
                            <input type="text" name="payment_deadline" value="<?php echo h($res['payment_deadline']); ?>">
                            <?php if (!empty($errors['payment_deadline'])) : ?>
                                <div class="valiError"><?php echo $errors['payment_deadline']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>請求日</p></th>
                        <td>
                            <input type="text" name="date_of_issue" value="<?php echo h($res['date_of_issue']); ?>">
                            <?php if (!empty($errors['date_of_issue'])) : ?>
                                <div class="valiError"><?php echo $errors['date_of_issue']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><p>見積番号</p></th>
                        <td>
                            <input type="text" name="quotation_no" value="<?php echo $res['quotation_no'] ?>">
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
                                <?php if ($key === (int)$res['status']) : ?>
                                    <option value="<?php echo $res['status']; ?>" selected><?php echo STATUS_LIST_I[$res['status']]; ?></option>
                                <?php else : ?>              
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
                <input type="hidden" name="id" value= "<?php echo h($res['id']); ?>">
        </div>

        <footer>
            <input type="submit" class="submitButton" value="登録変更">
            </form>
        </footer>

    </div>    
</body>
</html>