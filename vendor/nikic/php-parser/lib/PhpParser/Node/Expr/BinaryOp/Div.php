<?php

declare (strict_types=1);
namespace EasyCI20220415\PhpParser\Node\Expr\BinaryOp;

use EasyCI20220415\PhpParser\Node\Expr\BinaryOp;
class Div extends \EasyCI20220415\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '/';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Div';
    }
}
