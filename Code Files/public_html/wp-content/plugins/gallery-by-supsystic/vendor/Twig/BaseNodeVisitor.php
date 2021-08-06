<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Twig_SupTwg_BaseNodeVisitor can be used to make node visitors compatible with Twig 1.x and 2.x.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Twig_SupTwg_BaseNodeVisitor implements Twig_SupTwg_NodeVisitorInterface
{
    final public function enterNode(Twig_SupTwg_NodeInterface $node, Twig_SupTwg_Environment $env)
    {
        if (!$node instanceof Twig_SupTwg_Node) {
            throw new LogicException('Twig_SupTwg_BaseNodeVisitor only supports Twig_SupTwg_Node instances.');
        }

        return $this->doEnterNode($node, $env);
    }

    final public function leaveNode(Twig_SupTwg_NodeInterface $node, Twig_SupTwg_Environment $env)
    {
        if (!$node instanceof Twig_SupTwg_Node) {
            throw new LogicException('Twig_SupTwg_BaseNodeVisitor only supports Twig_SupTwg_Node instances.');
        }

        return $this->doLeaveNode($node, $env);
    }

    /**
     * Called before child nodes are visited.
     *
     * @return Twig_SupTwg_Node The modified node
     */
    abstract protected function doEnterNode(Twig_SupTwg_Node $node, Twig_SupTwg_Environment $env);

    /**
     * Called after child nodes are visited.
     *
     * @return Twig_SupTwg_Node|false The modified node or false if the node must be removed
     */
    abstract protected function doLeaveNode(Twig_SupTwg_Node $node, Twig_SupTwg_Environment $env);
}
