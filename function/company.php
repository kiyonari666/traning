<?php
// テーブルデータ作成バリテーション用自作ライブラリ
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

// テーブルデータ更新用バリテーション用自作ライブラリ
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
