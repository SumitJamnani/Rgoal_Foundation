<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_SupTwg_Node_Expression_Binary_FloorDiv extends Twig_SupTwg_Node_Expression_Binary
{
    public function compile(Twig_SupTwg_Compiler $compiler)
    {
        $compiler->raw('(int) floor(');
        parent::compile($compiler);
        $compiler->raw(')');
    }

    public function operator(Twig_SupTwg_Compiler $compiler)
    {
        return $compiler->raw('/');
    }
}
