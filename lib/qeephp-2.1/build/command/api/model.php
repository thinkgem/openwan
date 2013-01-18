<?php

class API_Model
{
    /**
     * 所有的包
     *
     * @var array of API_Doc_Package
     */
    public $packages = array();

    /**
     * 所有的类
     *
     * @var arary of API_Doc_Class
     */
    public $classes = array();

    public $docs_dir;

    protected $_last_errors = array();

    static function create()
    {
        return new API_Model();
    }

    function build(array $files)
    {
        $this->_findPackages($files);
        return $this;
    }

    function docsDir($dir)
    {
        $this->docs_dir = rtrim($dir, '/\\');
        return $this;
    }

    function packages()
    {
        return $this->packages;
    }

    function lastErrors()
    {
        return $this->_last_errors;
    }

    protected function _findPackages($files)
    {
        $found_classes = array();

        foreach ($files as $file)
        {
            require_once $file;
        }

        $classes = array_merge(get_declared_classes(), get_declared_interfaces());
        $this->_last_errors = array();
        foreach ($classes as $class)
        {
            $doc = new ReflectionClass($class);
            if (!in_array($doc->getFileName(), $files)) continue;

            try
            {
                $found_classes[] = API_Doc_Class::instance($class);
            }
            catch (API_Doc_Exception $ex)
            {
                $this->_last_errors[] = $ex;
            }
        }

        foreach ($found_classes as $class)
        {
            try
            {
                $package = $class->package;
                if (!$package)
                {
                    throw new API_Doc_EmptyPackageException($class, sprintf('Class "%s" use empty package name.', $class->name));
                }

                $package->classes[] = $class;
                if (!isset($this->packages[$package->name]))
                {
                    $this->packages[$package->name] = $package;
                }
            }
            catch (API_Doc_Exception $ex)
            {
                $this->_last_errors[] = $ex;
            }
        }
        $this->classes = $found_classes;

        foreach ($this->packages as $name => $package)
        {
            $filename = "{$this->docs_dir}/package.{$name}.texy";
            if (is_file($filename))
            {
                $contents = file_get_contents($filename);
                $lines = explode("\n", $contents);
                $summary = reset($lines);
                $package->summary = trim($summary);
                $package->description = $contents;
                unset($lines);
            }
        }
    }
}

