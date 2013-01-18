<?php

require_once dirname(__FILE__) . '/abstract.php';

class Command_LoadClass extends Command_Abstract
{
    protected $_output_file;
    protected $_files;

    static function create()
    {
        return new Command_LoadClass();
    }

    function outputFile($filename)
    {
        $this->_output_file = $filename;
        return $this;
    }

    function execute()
    {
        echo "search class files...";

        $files = find_files($this->_source_dir, array(
            'extnames' => array('.php'),
            'excludes' => array(
                '_config',
                // '_vendor',
                // 'extend/behavior/acluser',
            ),
        ));

        echo "ok\n\n";

        $this->_files = $files;
        spl_autoload_register(array($this, 'autoload'));
        $classes = get_declared_classes();
        $classes = array_merge($classes, get_declared_interfaces());

        foreach ($files as $path)
        {
            require_once $path;
        }

        $new_classes = get_declared_classes();
        $new_classes = array_merge($new_classes, get_declared_interfaces());
        $found = array_diff($new_classes, $classes);

        $files = array();
        foreach ($found as $class)
        {
            $r = new ReflectionClass($class);
            $files[$class] = $r->getFileName();
        }

        $arr = array();
        $len = strlen($this->_source_dir);

        foreach ($files as $class => $path)
        {
            $filename = str_replace(array('/', '\\'), '/', substr($path, $len + 1));
            $class = strtolower($class);
            $arr[$class] = $filename;
        }

        $output = "<?php global \$G_CLASS_FILES;\$G_CLASS_FILES = ";
        $output .= str_replace(array(' ', "\n"), '', var_export($arr, true));
        $output .= ";\n";

        file_put_contents($this->_output_file, $output, LOCK_EX);

        echo "ok\n";
    }

    function autoload($class)
    {
        echo "autoload {$class}...";
        foreach ($this->_files as $path)
        {
            $contents = file_get_contents($path);
            if (preg_match("/(class|interface) {$class}( |\n)/", $contents))
            {
                echo "\n  load {$path}\n";
                require_once $path;
                return $class;
            }
        }
        echo "failed\n";
    }

    function autoload2($class)
    {
        $classes = get_declared_classes();
        foreach ($this->_files as $path)
        {
            require_once $path;
        }
        $req = get_declared_classes();
        $new = array_diff($req, $classes);
        if (!empty($new))
        {
            $this->_autoload = array_merge($this->_autoload, $new);
        }
    }
}


function find_files($dir, $options=array())
{
    $extnames = !empty($options['extnames'])
                ? $options['extnames']
                : array();
    foreach ($extnames as $offset => $extname)
    {
        if ($extname[0] == '.')
        {
            $extnames[$offset] = substr($extname, 1);
        }
    }
    $excludes = !empty($options['excludes'])
                ? $options['excludes']
                : array();
    $level    = isset($options['level'])
                ? intval($options['level'])
                : -1;

    $list = find_files_recursive($dir, '', $extnames, $excludes, $level);
    sort($list);
    return $list;
}

function find_files_recursive($dir, $base, $extnames, $excludes, $level)
{
    $list = array();
    $handle = opendir($dir);
    while($file = readdir($handle))
    {
        if($file == '.' || $file == '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $file;

        $is_file = is_file($path);
        if (validate_path($base, $file, $is_file, $extnames, $excludes))
        {
            if ($is_file)
            {
                $list[] = $path;
            }
            elseif ($level)
            {
                $list = array_merge($list, find_files_recursive($path,
                        $base . '/' . $file, $extnames, $excludes, $level - 1));
            }
        }
    }
    closedir($handle);
    return $list;
}

function validate_path($base, $file, $is_file, array $extnames, array $excludes)
{
    $test = ltrim(str_replace('\\', '/', "/{$base}/{$file}"), '/');
    foreach($excludes as $e)
    {
        if ($file == $e || $test == $e) return false;
    }
    if(!$is_file || empty($extnames)) return true;

    if(($pos = strrpos($file, '.')) !==false)
    {
        $type = substr($file, $pos + 1);
        return in_array($type, $extnames);
    }
    else
    {
        return false;
    }
}

