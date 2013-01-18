<?php
/**
 * 定义 QGenerator_Application 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: application.php 1937 2009-01-05 19:09:40Z dualface $
 * @package core
 */

/**
 * 类 QGenerator_Application 用于创建应用程序
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: application.php 1937 2009-01-05 19:09:40Z dualface $
 * @package core
 */
class QGenerator_Application
{
	/**
	 * 复制文件时要动态替换的内容
	 *
	 * @var array
	 */
	private $content_search = array('%QEEPHP_INST_DIR%', '%APPID%');
	private $content_replace = array();

	/**
	 * 创建应用程序
	 *
	 * @param string $appid 应用程序名称
	 * @param string $dir 应用程序所在目录
	 * @param string $tpl 要使用的应用程序模板
	 * @param string $skeleton_dir 应用程序模板所在目录
	 */
	function generate($appid, $dir, $tpl = null, $skeleton_dir = null)
	{
	    if (empty($skeleton_dir))
	    {
	        $skeleton_dir = dirname(__FILE__) . '/_templates/apps';
	    }

	    if (empty($tpl)) $tpl = 'tianchi';
		$tpl_dir = $skeleton_dir . DS . $tpl;
		if (!file_exists($tpl_dir . DS . 'readme.txt'))
		{
		    throw new QGenerator_Exception(__('Specified tpl "%s" notexistent.', $tpl));
		}

		$appid = strtolower(preg_replace('/[^a-z0-9_]+/i', '', $appid));
		if (empty($appid))
		{
			throw new QGenerator_Exception(__('Invalid appname "%s".', $appid));
		}

		if (!is_dir($dir))
		{
			throw new QGenerator_Exception(__('Invalid dir "%s".', $dir));
		}

		clearstatcache();
		$dir = rtrim(realpath($dir), '/\\');
		$appdir = $dir . DS . $appid;

		if (is_dir($appdir))
		{
		    throw new QGenerator_Exception(__('Application dir "%s" already exists.', $appdir));
		}

		if (!mkdir($appdir, 0777))
		{
			throw new QGenerator_Exception(__('Creation application dir "%s" failed.', $appdir));
		}

		$this->content_replace = array(str_replace(DS, '/', dirname(Q_DIR)), $appid);

		echo "Building application {$appid}......\n\n";
		$this->_copydir($tpl_dir, $appdir);
		echo "\nSuccessed.\n\n";
	}

	/**
	 * 拷贝目录
	 *
	 * @param string $source
	 * @param string $target
	 */
	protected function _copydir($source, $target)
	{
		$source = rtrim($source, '/\\') . DS;
		$target = rtrim($target, '/\\') . DS;
		$h = opendir($source);
		$skip = array('.', '..', '.svn', '.cvs');
		while (($file = readdir($h)) !== false)
		{
		    if (in_array($file, $skip)) continue;
			$path = $source . $file;
			echo '  create ', $target . $file;
			echo "\n";
			if (is_dir($path))
            {
				mkdir($target . $file, 0777);
				$this->_copydir($path, $target . $file);
			}
            else
            {
                $filesize = filesize($path);
                if ($filesize)
                {
                    $fp = fopen($path, 'rb');
                    $content = fread($fp, $filesize);
                    fclose($fp);
                }
                else
                {
                    $content = '';
                }

                $extname = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                if ($extname == 'php' || $extname == 'yaml')
                {
				    $content = str_replace($this->content_search, $this->content_replace, $content);
				    $content = str_replace(array("\n\r", "\r\n", "\r"), "\n", $content);
                }

                $fp = fopen($target . $file, 'wb');
                fwrite($fp, $content);
                fclose($fp);
                chmod($target . $file, 0666);

				unset($content);
			}
		}
		closedir($h);
	}
}
