<?php
// 金額に桁区切り挿入用ファンクション
function thousandsSeparator($total)
{
    return  number_format($total);
}
// プレフィックスに記号挿入用ファンクション
function addPrefix($prefixCode)
{
    return  substr_replace($prefixCode, 'prefix-q-', 0, 0);
}
