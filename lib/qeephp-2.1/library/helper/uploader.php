<?php
// $Id: uploader.php 2675 2009-12-15 02:38:29Z jerry $

/**
 * 定义 Helper_Uploader 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: uploader.php 2675 2009-12-15 02:38:29Z jerry $
 * @package helper
 */

/**
 * Helper_Uploader 类封装了针对上传文件的操作
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: uploader.php 2675 2009-12-15 02:38:29Z jerry $
 * @package helper
 */
class Helper_Uploader
{
	/**
     * 所有的 Helper_Uploader_File 对象实例
     *
     * @var QColl
	 */
	static protected $_files;

    /**
     * 指示是否已经完成了初始化
     *
     * @var boolean
     */
    static protected $_init = false;

    /**
     * 构造函数
     */
    function __construct()
    {
        if (self::$_init) return;

        self::$_init = true;
        self::$_files = new QColl('Helper_Uploader_File');

        foreach ($_FILES as $field_name => $postinfo)
        {
            if (!isset($postinfo['error'])) continue;
            if (is_array($postinfo['error']))
            {
                // 多文件上传
                foreach ($postinfo['error'] as $offset => $error)
                {
                    if ($error == UPLOAD_ERR_OK)
                    {
                        $file = new Helper_Uploader_File($postinfo, $field_name, $offset);
                        self::$_files["{$field_name}{$offset}"] = $file;
                    }
                }
            }
            else
            {
                if ($postinfo['error'] == UPLOAD_ERR_OK)
                {
                    self::$_files[$field_name] = new Helper_Uploader_File($postinfo, $field_name);
                }
            }
        }
    }

    /**
     * 确定服务器的最大上传限制（字节数）
     *
     * @return int 服务器允许的最大上传字节数
     */
    function allowedUploadSize()
    {
        $val = trim(ini_get('upload_max_filesize'));
        $last = strtolower($val{strlen($val) - 1});
        switch ($last)
        {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
        }
        return $val;
    }

	/**
     * 可用的上传文件对象数量
     *
	 * @return int 上传文件对象数量
	 */
	function filesCount()
    {
		return count(self::$_files);
	}

    /**
     * 获得所有上传文件对象
     *
     * @return array 包含所有上传文件对象的数组
     */
    function allFiles()
    {
        return self::$_files;
    }

    /**
     * 检查指定名字的上传对象是否存在
     *
     * @param string $name 要检查的名字
     *
     * @return boolean 指定名字的上传对象是否存在
     */
    function existsFile($name)
    {
        return isset(self::$_files[$name]);
    }

    /**
     * 取得指定名字的上传文件对象
     *
     * @param string $name 上传对象的名字
     *
     * @return Helper_Uploader_File 指定名字对应的文件对象
     */
    function file($name)
    {
        return self::$_files[$name];
    }

    /**
     * 移动所有上传文件到指定目录
     *
     * @param string $dest_dir 目的地目录
     */
    function moveAll($dest_dir)
    {
        $dest_dir = rtrim($dest_dir, '/\\') . DS;
        foreach (self::$_files as $file)
        {
            $dest = $dest_dir . $file->filename();
            $file->move($dest);
        }
    }
}

/**
 * Helper_Uploader 类封装一个上传的文件
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: uploader.php 2675 2009-12-15 02:38:29Z jerry $
 * @package helper
 */
class Helper_Uploader_File
{
	/**
	 * 上传文件信息
	 *
	 * @var array
	 */
	protected $_file = array();

	/**
	 * 上传文件对象的名字
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * 构造函数
	 *
	 * @param array $struct 文件信息结构
	 * @param string $name 上传对象名
	 * @param int $ix 索引
	 */
	function __construct(array $struct, $name, $ix = false)
	{
        if ($ix !== false)
        {
			$this->_file = array(
				'name'     => $struct['name'][$ix],
				'type'     => $struct['type'][$ix],
				'tmp_name' => $struct['tmp_name'][$ix],
				'error'    => $struct['error'][$ix],
				'size'     => $struct['size'][$ix],
			);
        }
        else
        {
			$this->_file = $struct;
		}

        $this->_file['full_path'] = $this->_file['tmp_name'];
        $this->_file['is_moved'] = false;
        $this->_name = $name;
	}

	/**
     * 返回上传文件对象的名字
	 *
	 * @return string 上传对象名
	 */
	function name()
	{
		return $this->_name;
	}

	/**
	 * 指示上传是否成功
	 *
	 * @return boolean 指示上传是否成功
	 */
	function isSuccessed()
	{
		return $this->_file['error'] == UPLOAD_ERR_OK;
	}

	/**
	 * 返回上传错误代码
	 *
	 * @return int 上传错误代码
	 */
	function errorCode()
	{
		return $this->_file['error'];
	}

	/**
	 * 指示上传文件是否已经从临时目录移出
	 *
	 * @return boolean 指示文件是否已经移动
	 */
	function isMoved()
	{
		return $this->_file['is_moved'];
	}

	/**
	 * 返回上传文件的原名
	 *
	 * @return string 上传文件的原名
	 */
	function filename()
	{
		return $this->_file['name'];
	}

	/**
	 * 返回上传文件不带"."的扩展名
	 *
	 * @return string 上传文件的扩展名
	 */
	function extname()
	{
        return pathinfo($this->filename(), PATHINFO_EXTENSION);
	}

	/**
	 * 返回上传文件的大小（字节数）
	 *
	 * @return int 上传文件的大小
	 */
	function filesize()
	{
		return $this->_file['size'];
	}

	/**
	 * 返回上传文件的 MIME 类型（由浏览器提供，不可信）
	 *
	 * @return string 上传文件的 MIME 类型
	 */
	function mimeType()
	{
		return $this->_file['type'];
	}

	/**
	 * 返回上传文件的临时文件名
	 *
	 * @return string 上传文件的临时文件名
	 */
	function tmpFilename()
	{
		return $this->_file['tmp_name'];
	}

	/**
	 * 获得文件的完整路径
	 *
	 * @return string 文件的完整路径
	 */
	function filepath()
	{
		return $this->_file['full_path'];
	}

	/**
	 * 检查上传的文件是否成功上传，并符合检查条件（文件类型、最大尺寸）
	 *
     * 文件类型以扩展名为准，多个扩展名以 , 分割，例如 “jpg, jpeg, png。”。
     *
     * 用法：
     * @code
     * // 检查文件类型和大小
     * if ($file->isValid('jpg, jpeg, png', 2048 * 1024))
     * {
     *     ....
     * }
     * @endcode
	 *
	 * @param string $allowed_types 允许的扩展名
	 * @param int $max_size 允许的最大上传字节数
	 *
	 * @return boolean 是否检查通过
	 */
	function isValid($allowed_types = null, $max_size = null)
	{
		if (!$this->isSuccessed()) return false;

		if ($allowed_types)
        {
            $allowed_types = Q::normalize($allowed_types);

            foreach ($allowed_types as $offset => $extname)
            {
                if ($extname{0} == '.') $extname = substr($extname, 1);
                $allowed_types[$offset] = strtolower($extname);
            }
            $allowed_types = array_flip($allowed_types);

            // when upload filename is chinese, basename() only return extension name, it will be make mistake follow step
            //$filename = strtolower(basename($this->filename()));
            $filename = $this->filename(); // jerry2801
            $extnames = Q::normalize($filename, '.');
            array_shift($extnames);
            $passed = false;

            for ($i = count($extnames) - 1; $i >= 0; $i--)
            {
                $checking_ext = implode('.', array_slice($extnames, $i));
                if (isset($allowed_types[$checking_ext]))
                {
                    $passed = true;
                    break;
                }
            }

            if (!$passed) return false;
		}

        if ($max_size > 0 && ($this->filesize() > $max_size))
        {
			return false;
		}

		return true;
	}

	/**
	 * 移动上传文件到指定位置和文件名
	 *
     * @param string $dest_path 目的地路径
     *
     * @return Helper_Uploader_File 连贯接口
	 */
	function move($dest_path)
	{
        if ($this->_file['is_moved'])
        {
            $ret = rename($this->filepath(), $dest_path);
        }
        else
        {
            $this->_file['is_moved'] = true;
            $ret = move_uploaded_file($this->filepath(), $dest_path);
        }
        if ($ret)
        {
            $this->_file['full_path'] = $dest_path;
        }
        return $this;
	}

    /**
     * 复制上传文件
     *
     * @param string $dest_path 目的地路径
     *
     * @return Helper_Uploader_File 连贯接口
     */
    function copy($dest_path)
    {
        copy($this->filepath(), $dest_path);
        return $this;
    }

    /**
     * 删除上传文件
     *
     * @return Helper_Uploader_File 连贯接口
     */
    function unlink()
    {
        unlink($this->filepath());
        return $this;
    }

    /**
     * 魔法方法
     *
     * @return string
     * @access private
     */
    function __toString()
    {
        return '';
    }
}

