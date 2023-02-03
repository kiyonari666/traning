<?php
// htmlspecialchars自作ライブラリ
function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES);
}

// 金額に桁区切り挿入用ファンクション
function addSeparator(string $total): string
{
    return  number_format($total);
}

// 型指定
//引数の前に型宣言すると、引数の型定義できる。
//かっこの外に ): 付けて型指定すると、返り値の型定義できる
// function h(string $mix): string
// {
//     return htmlspecialchars($value, ENT_QUOTES);
// }
