<?php

declare (strict_types=1);
namespace EasyCI202212\PhpParser\Node\Expr\AssignOp;

use EasyCI202212\PhpParser\Node\Expr\AssignOp;
class Plus extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Plus';
    }
}
