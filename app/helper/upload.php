<?php
// $Id: upload.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * 图片上传处理助手类
 * @eamil riiyii@126.com
 * @copyright riiyii (QQ:605107424)
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version 1.0
 * @package helper
 */

abstract class Helper_Upload {

	static public $allowFileTypes = array('jpg','gif','png','jpeg');//类型
	static public $maxFileSize = 8000000;  //大小

	static public function getTypes()
	{
		return self::$allowFileTypes;
	}
	static public function getSize()
	{
		return self::$maxFileSize;
	}

	static public function setAllow($allow_type,$max_size) 
	{	
		self::$maxFileSize = intval($max_size)*1024*1024;
		if (!is_array($allow_type)) {
			self::$allowFileTypes = explode(',', $allow_size);
		} else {
			self::$allowFileTypes = $allow_size;
		}
	}

	//获取文件后缀
	static function fileExt($fileName, $withDot=false)
	{
		$fileName = basename($fileName);
		$pos = strrpos($fileName, '.');
		if ($pos===false) {
			$result = '';
		} else {
			$result = ($withDot) ? substr($fileName, $pos) : substr($fileName, $pos+1);
		}
		return $result;
	}
	
	/**
	 * 上传文件
	 * @param string $fileField  要上传的文件如$_FILES['file']
	 * @param string $destFolder 上传的目录，会自动建立
	 * @return int string 上传后文件路径
	 * @access public
	 * 如果未上传文件,返回 null
	 */
	static function upload($fileField, $destFolder = './') {

		switch ($fileField['error']) {
			case UPLOAD_ERR_OK : //其值为 0，没有错误发生，文件上传成功。
			$upload_succeed = true;
			break;
			case UPLOAD_ERR_INI_SIZE : //其值为 1，上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。
			case UPLOAD_ERR_FORM_SIZE : //其值为 2，上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。
			$errorMsg = '文件上传失败！失败原因：文件大小超出限制！';
			$errorCode = -103;
			$upload_succeed = false;
			break;
			case UPLOAD_ERR_PARTIAL : //值：3; 文件只有部分被上传。
			$errorMsg = '文件上传失败！失败原因：文件只有部分被上传！';
			$errorCode = -101;
			$upload_succeed = false;
			break;
			case UPLOAD_ERR_NO_FILE : //值：4; 没有文件被上传。
			$errorMsg = '文件上传失败！失败原因：没有文件被上传！';
			$errorCode = -102;
			$upload_succeed = false;
			$no_file = true;
			break;
			case UPLOAD_ERR_NO_TMP_DIR : //其值为 6，找不到临时文件夹。PHP 4.3.10 和 PHP 5.0.3 引进。
			$errorMsg = '文件上传失败！失败原因：找不到临时文件夹！';
			$errorCode = -102;
			$upload_succeed = false;
			break;
			case UPLOAD_ERR_CANT_WRITE : //其值为 7，文件写入失败。PHP 5.1.0 引进。
			$errorMsg = '文件上传失败！失败原因：文件写入失败！';
			$errorCode = -102;
			$upload_succeed = false;
			break;
			default : //其它错误
			$errorMsg = '文件上传失败！失败原因：其它！';
			$errorCode = -100;
			$upload_succeed = false;
			break;
		}
		if(isset($no_file)) {return;exit;}
		if ($upload_succeed) {
			if ($fileField['size']>self::$maxFileSize) {
				$errorMsg = '文件上传失败！失败原因：文件大小超出限制！';
				$errorCode = -103;
				$upload_succeed = false;
			}
			if ($upload_succeed) {
				$fileExt  = self::fileExt($fileField['name']);
				if (!in_array(strtolower($fileExt),self::$allowFileTypes)) {
					$errorMsg = '文件上传失败！失败原因：文件类型不被允许！';
					$errorCode = -104;
					$upload_succeed = false;
				}
			}
		}
		if ($upload_succeed) {
			    $destFolder  = rtrim($destFolder, '/\\').'/'.date('Y-m').'/';
			if (!is_dir($destFolder) && $destFolder!='./' && $destFolder!='../') {
				$dirname = '';
				$folders = explode('/',$destFolder);
				foreach ($folders as $folder) {
					$dirname .= $folder . '/';
					if ($folder!='' && $folder!='.' && $folder!='..' && !is_dir($dirname)) {
						mkdir($dirname,777);
						fclose(fopen($dirname.'/index.html','w'));
					}
				}
				chmod($destFolder,0777);
			}

			//确定文件名
			$fileFullName = date('YmdHis').rand(0,9999).md5($fileField['tmp_name']) . '.' . $fileExt;
			//上传
			if (move_uploaded_file($fileField['tmp_name'], $destFolder . $fileFullName)) {

				//上传成功,返回文件名
				return $destFolder.$fileFullName;

			} else {

				$errorMsg = '文件上传失败！失败原因：本地文件系统读写权限出错！';
				$errorCode = -105;
				$upload_succeed = false;
			}
		}
		if (!$upload_succeed) {
			throw new Exception($errorMsg);
		}

	}
	/**
	 * 生成缩略图
	 * @param string $srcfile     要处理的图片
	 * @param string $thumbwidth  缩略图的宽度
	 * @param string $thumbheight 缩略图的高度
	 * @return string $desfile    生成的文件名称
	 * @access public
	 * 缩略图的高度和宽度智能判断,生成图片和原图片同个目录中,后缀名统一为.thumb.jpg
	 */
	static function makethumb($srcfile,$thumbwidth= 100,$thumbheight= 100,$maxthumbwidth= 250,$maxthumbheight= 250) 
	{
		//判断文件是否存在
		if (!file_exists($srcfile)) {
			return '请选择操作图片!';
		}
		$dstfile = $srcfile.'.thumb.jpg';
		//缩略图大小
		$tow = intval($thumbwidth);
		$toh = intval($thumbheight);
		if($tow < 60) $tow = 60;
		if($toh < 60) $toh = 60;

		$make_max = 0;
		$maxtow = intval($maxthumbwidth);
		$maxtoh = intval($maxthumbheight);
		if($maxtow >= 300 && $maxtoh >= 300) {
			$make_max = 1;
		}
		
		//获取图片信息
		$im = '';
		if($data = getimagesize($srcfile)) {
			if($data[2] == 1) {
				$make_max = 0;//gif不处理
				if(function_exists("imagecreatefromgif")) {
					$im = imagecreatefromgif($srcfile);
				}
			} elseif($data[2] == 2) {
				if(function_exists("imagecreatefromjpeg")) {
					$im = imagecreatefromjpeg($srcfile);
				}
			} elseif($data[2] == 3) {
				if(function_exists("imagecreatefrompng")) {
					$im = imagecreatefrompng($srcfile);
				}
			}
		}
		if(!$im) return '';
		
		$srcw = imagesx($im);
		$srch = imagesy($im);
		
		$towh = $tow/$toh;
		$srcwh = $srcw/$srch;
		if($towh <= $srcwh){
			$ftow = $tow;
			$ftoh = $ftow*($srch/$srcw);
			
			$fmaxtow = $maxtow;
			$fmaxtoh = $fmaxtow*($srch/$srcw);
		} else {
			$ftoh = $toh;
			$ftow = $ftoh*($srcw/$srch);
			
			$fmaxtoh = $maxtoh;
			$fmaxtow = $fmaxtoh*($srcw/$srch);
		}
		if($srcw <= $maxtow && $srch <= $maxtoh) {
			$make_max = 0;//不处理
		}
		if($srcw > $tow || $srch > $toh) {
			if(function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled") && @$ni = imagecreatetruecolor($ftow, $ftoh)) {
				imagecopyresampled($ni, $im, 0, 0, 0, 0, $ftow, $ftoh, $srcw, $srch);
				//大图片
				if($make_max && @$maxni = imagecreatetruecolor($fmaxtow, $fmaxtoh)) {
					imagecopyresampled($maxni, $im, 0, 0, 0, 0, $fmaxtow, $fmaxtoh, $srcw, $srch);
				}
			} elseif(function_exists("imagecreate") && function_exists("imagecopyresized") && @$ni = imagecreate($ftow, $ftoh)) {
				imagecopyresized($ni, $im, 0, 0, 0, 0, $ftow, $ftoh, $srcw, $srch);
				//大图片
				if($make_max && @$maxni = imagecreate($fmaxtow, $fmaxtoh)) {
					imagecopyresized($maxni, $im, 0, 0, 0, 0, $fmaxtow, $fmaxtoh, $srcw, $srch);
				}
			} else {
				return '';
			}
			if(function_exists('imagejpeg')) {
				imagejpeg($ni, $dstfile);
				//大图片
				if($make_max) {
					imagejpeg($maxni, $srcfile);
				}
			} elseif(function_exists('imagepng')) {
				imagepng($ni, $dstfile);
				//大图片
				if($make_max) {
					imagepng($maxni, $srcfile);
				}
			}
			imagedestroy($ni);
			if($make_max) {
				imagedestroy($maxni);
			}
		}
		imagedestroy($im);

		if(!file_exists($dstfile)) {
			return '无目标文件!';
		} else {
			return $dstfile;
		}
	}

	/**
	 * 上传缩略综合
	 * @param string $desc           存放的目录
	 * @param string $w              缩略图的宽度
	 * @param string $h              缩略图的高度
	 * @return string $unlink_old    缩略后是否删除源文件
	 * @access public
	 * 缩略图的高度和宽度智能判断,生成图片和原图片同个目录中,后缀名统一为.thumb.jpg
	 */
	static function uploader($desc,$w=null,$h=null,$unlink_old=false,$mw=250,$mh=250)
	{		
		$file = array();
		foreach($_FILES as $k=>$v) {
			$src = self::upload($v,$desc);
			$file[$k]['pic_src']  = $src;
			if(empty($src)) {unset($file[$k]['pic_src']);}
			if(($w||$h)&&$src) {
				$thumb_src  = self::makethumb($src,$w,$h,$mw,$mh); 
				$file[$k]['thumb_src']  = $thumb_src; 
				if($unlink_old) {@unlink($src);unset($file[$k]['pic_src']);}
			}
			
		}
		return $file;
	}
	}
?>