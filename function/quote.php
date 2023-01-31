<?php
// 金額に桁区切り挿入用ファンクション
function thousandsSeparator($total)
{
    return  number_format($total);
}
// 見積番号に記号挿入用ファンクション
function addNo($no)
{
    return  substr_replace($no, '-p-', 8, 0);
}
