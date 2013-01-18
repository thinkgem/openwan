<?php
// $Id: q.php 2682 2010-02-06 10:33:51Z dualface $

/**
 * 定义 QeePHP 核心类，并初始化框架基本设置
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: q.php 2682 2010-02-06 10:33:51Z dualface $
 * @package core
 */

/**
 * QeePHP 框架基本库所在路径
 */
define('Q_DIR', dirname(__FILE__));

/**
 * DIRECTORY_SEPARATOR 的简写
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * CURRENT_TIMESTAMP 定义为当前时间，减少框架调用 time() 的次数
 */
define('CURRENT_TIMESTAMP', time());

global $G_CLASS_FILES;
if (empty($G_CLASS_FILES))
{
    require Q_DIR . '/_config/qeephp_class_files.php';
}

/**
 * 类 Q 是 QeePHP 框架的核心类，提供了框架运行所需的基本服务
 *
 * 类 Q 提供 QeePHP 框架的基本服务，包括：
 *
 * -  设置的读取和修改；
 * -  类定义文件的搜索和载入；
 * -  对象的单子模式实现，以及对象注册和检索；
 * -  统一缓存接口；
 * -  基本工具方法。
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: q.php 2682 2010-02-06 10:33:51Z dualface $
 * @package core
 */
class Q
{
    /**
     * 指示应用程序运行模式
     */
    // 开发运行模式
    const RUN_MODE_DEVEL  = 'devel';
    // 生产部署模式
    const RUN_MODE_DEPLOY = 'deploy';
    // 测试模式
    const RUN_MODE_TEST   = 'test';

    /**
     * 对象注册表
     *
     * @var array
     */
    private static $_objects = array();

    /**
     * 类搜索路径
     *
     * @var array
     */
    private static $_class_path = array();

    /**
     * 类搜索路径的选项
     *
     * @var array
     */
    private static $_class_path_options = array();

    /**
     * 应用程序设置
     *
     * @var array
     */
    private static $_ini = array();

    /**
     * 返回 QeePHP 版本号
     *
     * @return string QeePHP 版本号
     */
    static function version()
    {
        return '2.1';
    }

    /**
     * 获取指定的设置内容
     *
     * $option 参数指定要获取的设置名。
     * 如果设置中找不到指定的选项，则返回由 $default 参数指定的值。
     *
     * @code php
     * $option_value = Q::ini('my_option');
     * @endcode
     *
     * 对于层次化的设置信息，可以通过在 $option 中使用“/”符号来指定。
     *
     * 例如有一个名为 option_group 的设置项，其中包含三个子项目。
     * 现在要查询其中的 my_option 设置项的内容。
     *
     * @code php
     * // +--- option_group
     * //   +-- my_option  = this is my_option
     * //   +-- my_option2 = this is my_option2
     * //   \-- my_option3 = this is my_option3
     *
     * // 查询 option_group 设置组里面的 my_option 项
     * // 将会显示 this is my_option
     * echo Q::ini('option_group/my_option');
     * @endcode
     *
     * 要读取更深层次的设置项，可以使用更多的“/”符号，但太多层次会导致读取速度变慢。
     *
     * 如果要获得所有设置项的内容，将 $option 参数指定为 '/' 即可：
     *
     * @code php
     * // 获取所有设置项的内容
     * $all = Q::ini('/');
     * @endcode
     *
     * @param string $option 要获取设置项的名称
     * @param mixed $default 当设置不存在时要返回的设置默认值
     *
     * @return mixed 返回设置项的值
     */
    static function ini($option, $default = null)
    {
        if ($option == '/') return self::$_ini;

        if (strpos($option, '/') === false)
        {
            return array_key_exists($option, self::$_ini)
                ? self::$_ini[$option]
                : $default;
        }

        $parts = explode('/', $option);
        $pos =& self::$_ini;
        foreach ($parts as $part)
        {
            if (!isset($pos[$part])) return $default;
            $pos =& $pos[$part];
        }
        return $pos;
    }

    /**
     * 修改指定设置的内容
     *
     * 当 $option 参数是字符串时，$option 指定了要修改的设置项。
     * $data 则是要为该设置项指定的新数据。
     *
     * @code php
     * // 修改一个设置项
     * Q::changeIni('option_group/my_option2', 'new value');
     * @endcode
     *
     * 如果 $option 是一个数组，则假定要修改多个设置项。
     * 那么 $option 则是一个由设置项名称和设置值组成的名值对，或者是一个嵌套数组。
     *
     * @code php
     * // 假设已有的设置为
     * // +--- option_1 = old value
     * // +--- option_group
     * //   +-- option1 = old value
     * //   +-- option2 = old value
     * //   \-- option3 = old value
     *
     * // 修改多个设置项
     * $arr = array(
     *      'option_1' => 'value 1',
     *      'option_2' => 'value 2',
     *      'option_group/option2' => 'new value',
     * );
     * Q::changeIni($arr);
     *
     * // 修改后
     * // +--- option_1 = value 1
     * // +--- option_2 = value 2
     * // +--- option_group
     * //   +-- option1 = old value
     * //   +-- option2 = new value
     * //   \-- option3 = old value
     * @endcode
     *
     * 上述代码展示了 Q::changeIni() 的一个重要特性：保持已有设置的层次结构。
     *
     * 因此如果要完全替换某个设置项和其子项目，应该使用 Q::replaceIni() 方法。
     *
     * @param string|array $option 要修改的设置项名称，或包含多个设置项目的数组
     * @param mixed $data 指定设置项的新值
     */
    static function changeIni($option, $data = null)
    {
        if (is_array($option))
        {
            foreach ($option as $key => $value)
            {
                self::changeIni($key, $value);
            }
            return;
        }

        if (!is_array($data))
        {
            if (strpos($option, '/') === false)
            {
                self::$_ini[$option] = $data;
                return;
            }

            $parts = explode('/', $option);
            $max = count($parts) - 1;
            $pos =& self::$_ini;
            for ($i = 0; $i <= $max; $i ++)
            {
                $part = $parts[$i];
                if ($i < $max)
                {
                    if (!isset($pos[$part]))
                    {
                        $pos[$part] = array();
                    }
                    $pos =& $pos[$part];
                }
                else
                {
                    $pos[$part] = $data;
                }
            }
        }
        else
        {
            foreach ($data as $key => $value)
            {
                self::changeIni($option . '/' . $key, $value);
            }
        }
    }

    /**
     * 替换已有的设置值
     *
     * Q::replaceIni() 表面上看和 Q::changeIni() 类似。
     * 但是 Q::replaceIni() 不会保持已有设置的层次结构，
     * 而是直接替换到指定的设置项及其子项目。
     *
     * @code php
     * // 假设已有的设置为
     * // +--- option_1 = old value
     * // +--- option_group
     * //   +-- option1 = old value
     * //   +-- option2 = old value
     * //   \-- option3 = old value
     *
     * // 替换多个设置项
     * $arr = array(
     *      'option_1' => 'value 1',
     *      'option_2' => 'value 2',
     *      'option_group/option2' => 'new value',
     * );
     * Q::replaceIni($arr);
     *
     * // 修改后
     * // +--- option_1 = value 1
     * // +--- option_2 = value 2
     * // +--- option_group
     * //   +-- option2 = new value
     * @endcode
     *
     * 从上述代码的执行结果可以看出 Q::replaceIni() 和 Q::changeIni() 的重要区别。
     *
     * 不过由于 Q::replaceIni() 速度比 Q::changeIni() 快很多，
     * 因此应该尽量使用 Q::replaceIni() 来代替 Q::changeIni()。
     *
     * @param string|array $option 要修改的设置项名称，或包含多个设置项目的数组
     * @param mixed $data 指定设置项的新值
     */
    static function replaceIni($option, $data = null)
    {
        if (is_array($option))
        {
            self::$_ini = array_merge(self::$_ini, $option);
        }
        else
        {
            self::$_ini[$option] = $data;
        }
    }

    /**
     * 删除指定的设置
     *
     * Q::cleanIni() 可以删除指定的设置项目及其子项目。
     *
     * @param mixed $option 要删除的设置项名称
     */
    static function cleanIni($option)
    {
        if (strpos($option, '/') === false)
        {
            unset(self::$_ini[$option]);
        }
        else
        {
            $parts = explode('/', $option);
            $max = count($parts) - 1;
            $pos =& self::$_ini;
            for ($i = 0; $i <= $max; $i ++)
            {
                $part = $parts[$i];
                if ($i < $max)
                {
                    if (!isset($pos[$part]))
                    {
                        $pos[$part] = array();
                    }
                    $pos =& $pos[$part];
                }
                else
                {
                    unset($pos[$part]);
                }
            }
        }
    }

    /**
     * 载入指定类的定义文件，如果载入失败抛出异常
     *
     * @code php
     * Q::loadClass('Table_Posts');
     * @endcode
     *
     * $dirs 参数可以是一个以 PATH_SEPARATOR 常量分隔的字符串，
     * 也可以是一个包含多个目录名的数组。
     *
     * @code php
     * Q::loadClass('Table_Posts', array('/www/mysite/app', '/www/mysite/lib'));
     * @endcode
     *
     * @param string $class_name 要载入的类
     * @param string|array $dirs 指定载入类的搜索路径
     *
     * @return string|boolean 成功返回类名，失败返回 false
     */
    static function loadClass($class_name, $dirs = null, $throw = true)
    {
        if (class_exists($class_name, false) || interface_exists($class_name, false))
        {
            return $class_name;
        }

        global $G_CLASS_FILES;
        $class_name_l = strtolower($class_name);
        if (isset($G_CLASS_FILES[$class_name_l]))
        {
            require Q_DIR . DS . $G_CLASS_FILES[$class_name_l];
            return $class_name_l;
        }

        $filename = str_replace('_', DS, $class_name);
        if ($filename != $class_name)
        {
            $dirname = dirname($filename);
            if (!empty($dirs))
            {
	            if (!is_array($dirs))
                {
                    $dirs = explode(PATH_SEPARATOR, $dirs);
                }
            }
            else
            {
                $dirs = self::$_class_path;
            }
            $filename = basename($filename) . '.php';
            return self::loadClassFile($filename, $dirs, $class_name, $dirname, $throw);
        }
        else
        {
            return self::loadClassFile("{$filename}.php", self::$_class_path, $class_name, '', $throw);
        }
    }

    /**
     * 添加一个类搜索路径
     *
     * 如果要使用 Q::loadClass() 载入非 QeePHP 的类，需要通过 Q::import() 添加类类搜索路径。
     *
     * 要注意，Q::import() 添加的路径和类名称有关系。
     *
     * 例如类的名称为 Vendor_Smarty_Adapter，那么该类的定义文件存储结构就是 vendor/smarty/adapter.php。
     * 因此在用 Q::import() 添加 Vendor_Smarty_Adapter 类的搜索路径时，
     * 只能添加 vendor/smarty/adapter.php 的父目录。
     *
     * @code php
     * Q::import('/www/app');
     * Q::loadClass('Vendor_Smarty_Adapter');
     * // 实际载入的文件是 /www/app/vendor/smarty/adapter.php
     * @endcode
     *
     * 由于 QeePHP 的规范是文件名全小写，因此要载入文件名存在大小写区分的第三方库时，
     * 应该指定 import() 方法的第二个参数。
     *
     * @code php
     * Q::import('/www/app/vendor', true);
     * Q::loadClass('Zend_Mail');
     * // 实际载入的文件是 /www/app/vendor/Zend/Mail.php
     * @endcode
     *
     * @param string $dir 要添加的搜索路径
     * @param boolean $case_sensitive 在该路径中查找类文件时是否区分文件名大小写
     */
    static function import($dir, $case_sensitive = false)
    {
        $real_dir = realpath($dir);
        if ($real_dir)
        {
            $dir = rtrim($real_dir, '/\\');
            if (!isset(self::$_class_path[$dir]))
            {
                self::$_class_path[$dir] = $dir;
                self::$_class_path_options[$dir] = $case_sensitive;
            }
        }
    }

    /**
     * 载入特定文件，并检查是否包含指定类的定义
     *
     * 该方法从 $dirs 参数提供的目录中查找并载入 $filename 参数指定的文件。
     * 然后检查该文件是否定义了 $class_name 参数指定的类。
     *
     * 如果没有找到指定类，则抛出异常。
     *
     * @code php
     * Q::loadClassFile('Smarty.class.php', $dirs, 'Smarty');
     * @endcode
     *
     * @param string $filename 要载入文件的文件名（含扩展名）
     * @param string|array $dirs 文件的搜索路径
     * @param string $class_name 要检查的类
     * @param string $dirname 是否在查找文件时添加目录前缀
     * @param string $throw 是否在找不到类时抛出异常
     */
    static function loadClassFile($filename, $dirs, $class_name, $dirname = '', $throw = true)
    {
        if (!is_array($dirs))
        {
            $dirs = explode(PATH_SEPARATOR, $dirs);
        }
        if ($dirname)
        {
            $filename = rtrim($dirname, '/\\') . DS . $filename;
        }
        $filename_l = strtolower($filename);

        foreach ($dirs as $dir)
        {
            if (isset(self::$_class_path[$dir]))
            {
                $path = $dir . DS . (self::$_class_path_options[$dir] ? $filename : $filename_l);
            }
            else
            {
                $path = rtrim($dir, '/\\') . DS . $filename;
            }

            if (is_file($path))
            {
                require $path;
                break;
            }
        }

        // 载入文件后判断指定的类或接口是否已经定义
        if (!class_exists($class_name, false) && ! interface_exists($class_name, false))
        {
            if ($throw)
            {
                throw new Q_ClassNotDefinedException($class_name, $path);
            }
            return false;
        }
        return $class_name;
    }

    /**
     * 载入指定的文件
     *
     * 该方法从 $dirs 参数提供的目录中查找并载入 $filename 参数指定的文件。
     * 如果文件不存在，则根据 $throw 参数决定是否抛出异常。
     *
     * 与 PHP 内置的 require 和 include 相比，Q::loadFile() 会多处下列特征：
     *
     * <ul>
     *   <li>检查文件名是否包含不安全字符；</li>
     *   <li>检查文件是否可读；</li>
     *   <li>文件无法读取时将抛出异常。</li>
     * </ul>
     *
     * @code php
     * Q::loadFile('my_file.php', $dirs);
     * @endcode
     *
     * @param string $filename 要载入文件的文件名（含扩展名）
     * @param array $dirs 文件的搜索路径
     * @param boolean $throw 在找不到文件时是否抛出异常
     *
     * @return mixed
     */
    static function loadFile($filename, $dirs = null, $throw = true)
    {
        if (preg_match('/[^a-z0-9\-_.]/i', $filename))
        {
            throw new Q_IllegalFilenameException($filename);
        }

        if (is_null($dirs))
        {
            $dirs = array();
        }
        elseif (is_string($dirs))
        {
            $dirs = explode(PATH_SEPARATOR, $dirs);
        }
        foreach ($dirs as $dir)
        {
            $path = rtrim($dir, '\\/') . DS . $filename;
            if (is_file($path)) return include $path;
        }

        if ($throw) throw new Q_FileNotFoundException($filename);
        return false;
    }

    /**
     * 返回指定对象的唯一实例
     *
     * Q::singleton() 完成下列工作：
     *
     * <ul>
     *   <li>在对象注册表中查找指定类名称的对象实例是否存在；</li>
     *   <li>如果存在，则返回该对象实例；</li>
     *   <li>如果不存在，则载入类定义文件，并构造一个对象实例；</li>
     *   <li>将新构造的对象以类名称作为对象名登记到对象注册表；</li>
     *   <li>返回新构造的对象实例。</li>
     * </ul>
     *
     * 使用 Q::singleton() 的好处在于多次使用同一个对象时不需要反复构造对象。
     *
     * @code php
     * // 在位置 A 处使用对象 My_Object
     * $obj = Q::singleton('My_Object');
     * ...
     * ...
     * // 在位置 B 处使用对象 My_Object
     * $obj2 = Q::singleton('My_Object');
     * // $obj 和 $obj2 都是指向同一个对象实例，避免了多次构造，提高了性能
     * @endcode
     *
     * @param string $class_name 要获取的对象的类名字
     *
     * @return object 返回对象实例
     */
    static function singleton($class_name)
    {
        $key = strtolower($class_name);
        if (isset(self::$_objects[$key]))
        {
            return self::$_objects[$key];
        }
        self::loadClass($class_name);
        return self::register(new $class_name(), $class_name);
    }

    /**
     * 以特定名字在对象注册表中登记一个对象
     *
     * 开发者可以将一个对象登记到对象注册表中，以便在应用程序其他位置使用 Q::registry() 来查询该对象。
     * 登记时，如果没有为对象指定一个名字，则以对象的类名称作为登记名。
     *
     * @code php
     * // 注册一个对象
     * Q::register(new MyObject());
     * .....
     * // 稍后取出对象
     * $obj = Q::regitry('MyObject');
     * @endcode
     *
     * 当 $persistent 参数为 true 时，对象将被放入持久存储区。
     * 在下一次执行脚本时，可以通过 Q::registry() 取出放入持久存储区的对象，并且无需重新构造对象。
     *
     * 利用这个特性，开发者可以将一些需要大量构造时间的对象放入持久存储区，
     * 从而避免每一次执行脚本都去做对象构造操作。
     *
     * 使用哪一种持久化存储区来保存对象，由设置 object_persistent_provier 决定。
     * 该设置指定一个提供持久化服务的对象名。
     *
     * @code php
     * if (!Q::isRegistered('MyObject'))
     * {
     *      Q::registry(new MyObject(), 'MyObject', true);
     * }
     * $app = Q::registry('MyObject');
     * @endcode
     *
     * @param object $obj 要登记的对象
     * @param string $name 用什么名字登记
     * @param boolean $persistent 是否将对象放入持久化存储区
     *
     * @return object
     */
    static function register($obj, $name = null, $persistent = false)
    {
        if (!is_object($obj))
        {
            // LC_MSG: Type mismatch. $obj expected is object, actual is "%s".
            throw new QException(__('Type mismatch. $obj expected is object, actual is "%s".',
                                    gettype($obj)));
        }

        // TODO: 实现对 $persistent 参数的支持
        if (is_null($name))
        {
            $name = get_class($obj);
        }
        $name = strtolower($name);
        self::$_objects[$name] = $obj;
        return $obj;
    }

    /**
     * 查找指定名字的对象实例，如果指定名字的对象不存在则抛出异常
     *
     * @code php
     * // 注册一个对象
     * Q::register(new MyObject(), 'obj1');
     * .....
     * // 稍后取出对象
     * $obj = Q::regitry('obj1');
     * @endcode
     *
     * @param string $name 要查找对象的名字
     *
     * @return object 查找到的对象
     */
    static function registry($name)
    {
        $name = strtolower($name);
        if (isset(self::$_objects[$name]))
        {
            return self::$_objects[$name];
        }
        // LC_MSG: No object is registered of name "%s".
        throw new QException(__('No object is registered of name "%s".', $name));
    }

    /**
     * 检查指定名字的对象是否已经注册
     *
     * @param string $name 要检查的对象名字
     *
     * @return boolean 对象是否已经登记
     */
    static function isRegistered($name)
    {
        $name = strtolower($name);
        return isset(self::$_objects[$name]);
    }

    /**
     * 读取指定的缓存内容，如果内容不存在或已经失效，则返回 false
     *
     * 在操作缓存数据时，必须指定缓存的 ID。每一个缓存内容都有一个唯一的 ID。
     * 例如数据 A 的缓存 ID 是 data-a，而数据 B 的缓存 ID 是 data-b。
     *
     * 在大量使用缓存时，应该采用一定的规则来确定缓存 ID。下面是一个推荐的方案：
     *
     * <ul>
     *   <li>首先按照缓存数据的性质确定前缀，例如 page、db 等；</li>
     *   <li>然后按照数据的唯一索引来确定后缀，并和前缀一起组合成完整的缓存 ID。</li>
     * </ul>
     *
     * 按照这个规则，缓存 ID 看上去类似 page.news.1、db.members.userid。
     *
     * Q::cache() 可以指定 $policy 参数来覆盖缓存数据本身带有的策略。
     * 具体哪些策略可以使用，请参考不同缓存服务的文档。
     *
     * $backend_class 用于指定要使用的缓存服务对象类名称。例如 QCache_File、QCache_APC 等。
     *
     * @code php
     * $data = Q::cache($cache_id);
     * if ($data === false)
     * {
     *     $data = ....
     *     Q::writeCache($cache_id, $data);
     * }
     * @endcode
     *
     * @param string $id 缓存的 ID
     * @param array $policy 缓存策略
     * @param string $backend_class 要使用的缓存服务
     *
     * @return mixed 成功返回缓存内容，失败返回 false
     */
    static function cache($id, array $policy = null, $backend_class = null)
    {
        static $obj = null;

        if (is_null($backend_class))
        {
            if (is_null($obj))
            {
                $obj = self::singleton(self::ini('runtime_cache_backend'));
            }
            return $obj->get($id, $policy);
        }
        else
        {
            $cache = self::singleton($backend_class);
            return $cache->get($id, $policy);
        }
    }

    /**
     * 将变量内容写入缓存，失败抛出异常
     *
     * $data 参数指定要缓存的内容。如果 $data 参数不是一个字符串，则必须将缓存策略 serialize 设置为 true。
     * $policy 参数指定了内容的缓存策略，如果没有提供该参数，则使用缓存服务的默认策略。
     *
     * 其他参数同 Q::cache()。
     *
     * @param string $id 缓存的 ID
     * @param mixed $data 要缓存的数据
     * @param array $policy 缓存策略
     * @param string $backend_class 要使用的缓存服务
     */
    static function writeCache($id, $data, array $policy = null, $backend_class = null)
    {
        static $obj = null;

        if (is_null($backend_class))
        {
            if (is_null($obj))
            {
                $obj = self::singleton(self::ini('runtime_cache_backend'));
            }
            $obj->set($id, $data, $policy);
        }
        else
        {
            $cache = self::singleton($backend_class);
            $cache->set($id, $data, $policy);
        }
    }

    /**
     * 删除指定的缓存内容
     *
     * 通常，失效的缓存数据无需清理。但有时候，希望在某些操作完成后立即清除缓存。
     * 例如更新数据库记录后，希望删除该记录的缓存文件，以便在下一次读取缓存时重新生成缓存文件。
     *
     * @code php
     * Q::cleanCache($cache_id);
     * @endcode
     *
     * @param string $id 缓存的 ID
     * @param array $policy 缓存策略
     * @param string $backend_class 要使用的缓存服务
     */
    static function cleanCache($id, array $policy = null, $backend_class = null)
    {
        static $obj = null;

        if (is_null($backend_class))
        {
            if (is_null($obj))
            {
                $obj = self::singleton(self::ini('runtime_cache_backend'));
            }
            $obj->remove($id, $policy);
        }
        else
        {
            $cache = self::singleton($backend_class);
            $cache->remove($id, $policy);
        }
    }

    /**
     * 对字符串或数组进行格式化，返回格式化后的数组
     *
     * $input 参数如果是字符串，则首先以“,”为分隔符，将字符串转换为一个数组。
     * 接下来对数组中每一个项目使用 trim() 方法去掉首尾的空白字符。最后过滤掉空字符串项目。
     *
     * 该方法的主要用途是将诸如：“item1, item2, item3” 这样的字符串转换为数组。
     *
     * @code php
     * $input = 'item1, item2, item3';
     * $output = Q::normalize($input);
     * // $output 现在是一个数组，结果如下：
     * // $output = array(
     * //   'item1',
     * //   'item2',
     * //   'item3',
     * // );
     *
     * $input = 'item1|item2|item3';
     * // 指定使用什么字符作为分割符
     * $output = Q::normalize($input, '|');
     * @endcode
     *
     * @param array|string $input 要格式化的字符串或数组
     * @param string $delimiter 按照什么字符进行分割
     *
     * @return array 格式化结果
     */
    static function normalize($input, $delimiter = ',')
    {
        if (!is_array($input))
        {
            $input = explode($delimiter, $input);
        }
        $input = array_map('trim', $input);
        return array_filter($input, 'strlen');
    }

    /**
     * 创建一个用户界面控件对象
     *
     * 使用 Q::control() 方法，可以很容易的创建指定类型的用户界面控件对象。
     *
     * @param string $type 用户界面控件对象的类型
     * @param string $id 控件ID
     * @param array $attrs 要传递给控件的附加属性
     *
     *
     * @return QUI_Control_Abstract 创建的用户界面控件对象
     */
    static function control($type, $id = null, $attrs = array())
    {
        $id = empty($id) ? strtolower($type) : strtolower($id);
        $class_name = "Control_{$type}";
        return new $class_name($id, $attrs);
    }

    /**
     * 用于 QeePHP 的类自动载入，不需要由开发者调用
     *
     * @param string $class_name
     */
    static function autoload($class_name)
    {
        self::loadClass($class_name, null, false);
    }

    /**
     * 注册或取消注册一个自动类载入方法
     *
     * 该方法参考 Zend Framework。
     *
     * @param string $class 提供自动载入服务的类
     * @param boolean $enabled 启用或禁用该服务
     */
    static function registerAutoload($class = 'Q', $enabled = true)
    {
        if (!function_exists('spl_autoload_register'))
        {
            require_once Q_DIR . '/qexception.php';
            throw new QException('spl_autoload does not exist in this PHP installation');
        }

        if ($enabled === true)
        {
            spl_autoload_register(array($class, 'autoload'));
        }
        else
        {
            spl_autoload_unregister(array($class, 'autoload'));
        }
    }

}

/**
 * QeePHP 内部使用的多语言翻译函数
 *
 * 应用程序应该使用 QTranslate 组件实现多语言界面。
 *
 * @return $msg
 */
function __()
{
    $args = func_get_args();
    $msg = array_shift($args);
    $language = strtolower(Q::ini('error_language'));
    $messages = Q::loadFile('lc_messages.php', Q_DIR . '/_lang/' . $language, false);
    if (isset($messages[$msg]))
    {
        $msg = $messages[$msg];
    }
    array_unshift($args, $msg);
    return call_user_func_array('sprintf', $args);
}

/**
 * 转换 HTML 特殊字符，等同于 htmlspecialchars()
 *
 * @param string $text
 *
 * @return string
 */
function h($text)
{
    return htmlspecialchars($text);
}

/**
 * 输出转义后的字符串
 *
 * @param string $text
 */
function p($text)
{
    echo htmlspecialchars($text);
}

/**
 * QDebug::dump() 的简写，用于输出一个变量的内容
 *
 * @param mixed $vars 要输出的变量
 * @param string $label 输出变量时显示的标签
 * @param boolean $return 是否返回输出内容
 *
 * @return string
 */
function dump($vars, $label = null, $return = false)
{
    if ($return) ob_start();
    QDebug::dump($vars, $label);
    if ($return)
    {
        $return = ob_get_clean();
        return $return;
    }
}

/**
 * QContext::url() 方法的简写，用于构造一个 URL 地址
 *
 * url() 方法的参数比较复杂，请参考 QContext::url() 方法的详细说明。
 *
 * @param string $udi UDI 字符串
 * @param array|string $params 附加参数数组
 * @param string $route_name 路由名
 * @param array $opts 控制如何生成 URL 的选项
 *
 * @return string 生成的 URL 地址
 */
function url($udi, $params = null, $route_name = null, array $opts = null)
{
    return QContext::instance()->url($udi, $params, $route_name, $opts);
}

/**
 * 设置对象的自动载入
 */
Q::registerAutoload();

