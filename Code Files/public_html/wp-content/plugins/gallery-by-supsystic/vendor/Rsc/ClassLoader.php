<?php


class Rsc_ClassLoader
{

    /**
     * @var array
     */
    protected $prefixes;

    /**
     * Constructor
     * @param array $prefixes An array of vendor prefixes
     */
    public function __construct(array $prefixes = array())
    {
        $this->prefixes = $prefixes;
    }

    /**
     * Add vendor prefix to the autoload stack
     * @param string $prefix Vendor prefix
     * @param string|array $path Classes path
     * @return Rsc_ClassLoader
     */
    public function add($prefix, $path)
    {
        if (isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = array_merge($this->prefixes[$prefix], (array)$path);
        } else {
            $this->prefixes[$prefix] = (array)$path;
        }

        return $this;
    }

    /**
     * Remove prefixes from the autoload stack
     * @param string|array $prefix Vendor prefixes to remove
     * @return Rsc_ClassLoader
     */
    public function remove($prefix)
    {
        if (is_array($prefix)) {
            array_map(array($this, 'remove'), $prefix);
        }

        if (isset($this->prefixes[$prefix])) {
            unset ($this->prefixes[$prefix]);
        }

        return $this;
    }

    /**
     * Add an array of prefixes to the autoload stack
     * @param array $prefixes An array of prefixes
     * @return Rsc_ClassLoader
     */
    public function addPrefixes(array $prefixes)
    {
        foreach ($prefixes as $prefix => $path) {
            $this->add($prefix, $path);
        }

        return $this;
    }

    /**
     * Set an array of the vendor prefixes
     * @param array $prefixes An array of prefixes
     * @return Rsc_ClassLoader
     */
    public function setPrefixes(array $prefixes)
    {
        $this->prefixes = $prefixes;
        return $this;
    }

    /**
     * Returns registered vendor prefixes
     * @return array
     */
    public function getPrefixes()
    {
        return $this->prefixes;
    }

    /**
     * Register current instance of the autoloader
     * @param bool $prepend If true, spl_autoload_register() will prepend the autoloader on the autoload stack instead of appending it
     */
    public function register($prepend = false)
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            spl_autoload_register(array($this, 'load'), true);
        } else {
            spl_autoload_register(array($this, 'load'), true, $prepend);
        }
    }

    /**
     * Unregister current autoloader
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'load'));
    }

    /**
     * Load class
     * @param string $class Classname
     */
    public function load($class)
    {
        if ($file = $this->find($class)) {
            require_once $file;
        }
    }

    /**
     * Find class
     * @param string $class Classname
     * @return string Path to the class
     */
    public function find($class)
    {
        if ($pos = strpos($class, '\\') !== false) {
            $path = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 0, $pos)) . DIRECTORY_SEPARATOR;
            $name = substr($class, $pos + 1);
        } else {
            $path = null;
            $name = $class;
        }

        if (stripos($class,'Twig_') !== false) {
           $className = str_replace('Twig_SupTwg_', '', $name);
           $classDelimeters = explode('_', $className);

           $newName = '';
           for ($i=0; $i < count($classDelimeters); $i++) {
              $newName .= DIRECTORY_SEPARATOR . $classDelimeters[$i];
           }
           $path .= 'Twig' . $newName . '.php';
           if ($class == 'SupsysticSlider_Slider_Twig_SupTwg_Attachment') {
             $path = DIRECTORY_SEPARATOR.'SupsysticSlider'.DIRECTORY_SEPARATOR.'Slider'.DIRECTORY_SEPARATOR.'Twig'.DIRECTORY_SEPARATOR.'Attachment.php';
           }
           if ($class == 'SupsysticSliderPro_Slider_Twig_SupTwg_Video') {
             $path = DIRECTORY_SEPARATOR.'SupsysticSliderPro'.DIRECTORY_SEPARATOR.'Slider'.DIRECTORY_SEPARATOR.'Twig'.DIRECTORY_SEPARATOR.'Video.php';
           }

        } else {
           $path .= str_replace('_', DIRECTORY_SEPARATOR, $name) . '.php';
        }

        foreach ($this->prefixes as $prefix => $dirs) {
            if ($class === strstr($class, $prefix)) {
                foreach ($dirs as $dir) {
                    if (file_exists($dir . DIRECTORY_SEPARATOR . $path)) {
                        return $dir . DIRECTORY_SEPARATOR . $path;
                    }
                }
            }
        }
    }
}
