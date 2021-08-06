<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_SupTwg_Node_Expression_Filter extends Twig_SupTwg_Node_Expression_Call
{
    public function __construct(Twig_SupTwg_NodeInterface $node, Twig_SupTwg_Node_Expression_Constant $filterName, Twig_SupTwg_NodeInterface $arguments, $lineno, $tag = null)
    {
        parent::__construct(array('node' => $node, 'filter' => $filterName, 'arguments' => $arguments), array(), $lineno, $tag);
    }

    public function compile(Twig_SupTwg_Compiler $compiler)
    {
        $name = $this->getNode('filter')->getAttribute('value');
        $filter = $compiler->getEnvironment()->getFilter($name);

        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'filter');
        $this->setAttribute('thing', $filter);
        $this->setAttribute('needs_environment', $filter->needsEnvironment());
        $this->setAttribute('needs_context', $filter->needsContext());
        $this->setAttribute('arguments', $filter->getArguments());
        if ($filter instanceof Twig_SupTwg_FilterCallableInterface || $filter instanceof Twig_SupTwg_SimpleFilter) {
            $this->setAttribute('callable', $filter->getCallable());
        }
        if ($filter instanceof Twig_SupTwg_SimpleFilter) {
            $this->setAttribute('is_variadic', $filter->isVariadic());
        }

        $this->compileCallable($compiler);
    }
}
