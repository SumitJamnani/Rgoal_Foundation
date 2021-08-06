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
 * Loads templates from other loaders.
 *
 * @final
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Twig_SupTwg_Loader_Chain implements Twig_SupTwg_LoaderInterface, Twig_SupTwg_ExistsLoaderInterface, Twig_SupTwg_SourceContextLoaderInterface
{
    private $hasSourceCache = array();
    protected $loaders = array();

    /**
     * @param Twig_SupTwg_LoaderInterface[] $loaders
     */
    public function __construct(array $loaders = array())
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    public function addLoader(Twig_SupTwg_LoaderInterface $loader)
    {
        $this->loaders[] = $loader;
        $this->hasSourceCache = array();
    }

    public function getSource($name)
    {
        @trigger_error(sprintf('Calling "getSource" on "%s" is deprecated since 1.27. Use getSourceContext() instead.', get_class($this)), E_USER_DEPRECATED);

        $exceptions = array();
        foreach ($this->loaders as $loader) {
            if ($loader instanceof Twig_SupTwg_ExistsLoaderInterface && !$loader->exists($name)) {
                continue;
            }

            try {
                return $loader->getSource($name);
            } catch (Twig_SupTwg_Error_Loader $e) {
                $exceptions[] = $e->getMessage();
            }
        }

        throw new Twig_SupTwg_Error_Loader(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' ('.implode(', ', $exceptions).')' : ''));
    }

    public function getSourceContext($name)
    {
        $exceptions = array();
        foreach ($this->loaders as $loader) {
            if ($loader instanceof Twig_SupTwg_ExistsLoaderInterface && !$loader->exists($name)) {
                continue;
            }

            try {
                if ($loader instanceof Twig_SupTwg_SourceContextLoaderInterface) {
                    return $loader->getSourceContext($name);
                }

                return new Twig_SupTwg_Source($loader->getSource($name), $name);
            } catch (Twig_SupTwg_Error_Loader $e) {
                $exceptions[] = $e->getMessage();
            }
        }

        throw new Twig_SupTwg_Error_Loader(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' ('.implode(', ', $exceptions).')' : ''));
    }

    public function exists($name)
    {
        $name = (string) $name;

        if (isset($this->hasSourceCache[$name])) {
            return $this->hasSourceCache[$name];
        }

        foreach ($this->loaders as $loader) {
            if ($loader instanceof Twig_SupTwg_ExistsLoaderInterface) {
                if ($loader->exists($name)) {
                    return $this->hasSourceCache[$name] = true;
                }

                continue;
            }

            try {
                if ($loader instanceof Twig_SupTwg_SourceContextLoaderInterface) {
                    $loader->getSourceContext($name);
                } else {
                    $loader->getSource($name);
                }

                return $this->hasSourceCache[$name] = true;
            } catch (Twig_SupTwg_Error_Loader $e) {
            }
        }

        return $this->hasSourceCache[$name] = false;
    }

    public function getCacheKey($name)
    {
        $exceptions = array();
        foreach ($this->loaders as $loader) {
            if ($loader instanceof Twig_SupTwg_ExistsLoaderInterface && !$loader->exists($name)) {
                continue;
            }

            try {
                return $loader->getCacheKey($name);
            } catch (Twig_SupTwg_Error_Loader $e) {
                $exceptions[] = get_class($loader).': '.$e->getMessage();
            }
        }

        throw new Twig_SupTwg_Error_Loader(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' ('.implode(', ', $exceptions).')' : ''));
    }

    public function isFresh($name, $time)
    {
        $exceptions = array();
        foreach ($this->loaders as $loader) {
            if ($loader instanceof Twig_SupTwg_ExistsLoaderInterface && !$loader->exists($name)) {
                continue;
            }

            try {
                return $loader->isFresh($name, $time);
            } catch (Twig_SupTwg_Error_Loader $e) {
                $exceptions[] = get_class($loader).': '.$e->getMessage();
            }
        }

        throw new Twig_SupTwg_Error_Loader(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' ('.implode(', ', $exceptions).')' : ''));
    }
}
