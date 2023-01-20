<?php
// テーブルデータ作成バリテーション用ファンクション
function insertValidate($postData)
{
    return ($postData['name']) !== null
        && ($postData['manager_name']) !== null
        && ($postData['phone_number']) !== null
        && ($postData['postal_code']) !== null
        && ($postData['prefecture_code']) !== null
        && ($postData['address']) !== null
        && ($postData['mail_address']) !== null
        && ($postData['prefix']) !== null;
}

// テーブルデータ更新用バリテーション用ファンクション
function updateValidate($postData)
{
    return ($postData['name']) !== null
        && ($postData['manager_name']) !== null
        && ($postData['phone_number']) !== null
        && ($postData['postal_code']) !== null
        && ($postData['prefecture_code']) !== null
        && ($postData['address']) !== null
        && ($postData['mail_address']) !== null;
}
// 郵便番号にハイフン挿入用ファンクション
function addHyphen($postalCode)
{
    return  substr_replace($postalCode, '-', 3, 0);
}
