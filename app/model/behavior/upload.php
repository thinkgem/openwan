<?php
// $Id: upload.php 895 2010-03-23 05:36:29Z thinkgem $

class Model_Behavior_Upload extends QDB_ActiveRecord_Behavior_Abstract 
{
	/**
	 * 上传属性配置。
	 * 用法：
	 * @code php
	 * // 指定该 ActiveRecord 要使用的行为插件
     * 'behaviors' => 'upload',
     *
	 * // 指定行为插件的配置
     * 'behaviors_settings' => array
     * (
	 *    upload => array(
	 *    'upload_config' => array
	 *    (
	 * 		'photo' => array  //这里的photo就模型中属性
	 * 		(
	 * 			'post_key'				=> 'file_photo',  //表单中POST的文件域的名称
	 * 			'upload_dir' 			=> 'upload/photos', //上传的目录
	 * 			'http_dir'   			=> 'upload/photos', //URL访问地址
	 * 			'is_image'				=> true, //是不是图片上传
	 * 			'large'   				=> '250*170', //大图片的保存尺寸
	 * 			'thumb'   				=> '150*150', //缩略图的尺寸
	 * 			'image_dimension' 		=> '300*250', //图片的尺寸限制
	 * 			'allowed_filetypes'   	=> '.jpg,.gif,.jpeg,.png', //文件类型
	 * 			'required'   			=> true, //是否必需
	 * 			'max_size' 				=> 2048, //文件大小
	 * 			'errmsgs' 				=> array  //算定义错误信息
	 * 			(
	 * 				'required'   => '请上传头像',  
	 * 				'allowed_filetypes' => '不允许上传的图片类型', 
	 * 				'max_size' => '文件大小超过限制',
	 * 				'image_dimension' => '头像图片的尺寸必需是300*140',
	 * 			),
	 * 		),
	 *     ),
	 *   ),
	 * ),
	 * @var array
	 */
	protected $_settings = array
	(
		'upload_config' => array(),
	);
	
	function bind()
	{
		//AFTER_VALIDATE 是打算将验证和执行上传分开,不过暂时还没完善
		//$this->_addEventHandler(self::AFTER_VALIDATE, array($this, '_validate_upload'));
		
		$this->_addEventHandler(self::BEFORE_CREATE, array($this, '_exec_upload'));
		$this->_addEventHandler(self::BEFORE_UPDATE, array($this, '_exec_upload'));
		$this->_addEventHandler(self::AFTER_DESTROY, array($this, '_delete_upload'));
		
		foreach ($this->_settings['upload_config'] as $_prop => $config)
		{
			$this->_setPropSetter($_prop,array($this,'setUploadProp'),array($_prop));
			//为模型增加SRC虚拟属性 如：$user->photo_src
			$this->_setPropGetter($_prop.'_src',array($this,'getUploadPropSrc'),array($_prop));
			if(isset($config['thumb']))
			{
				//为模型增加缩略图虚拟属性
				$this->_setPropGetter($_prop.'_thumb',array($this,'getUploadPropThumb'),array($_prop));
				$this->_setPropGetter($_prop.'_thumb_src',array($this,'getUploadPropSrc'),array($_prop,true));
			}
		}
	}
	
	/**
	 * 设置 $_prop 属性
	 *  
	 * 
	 * @param QDB_ActiveRecord_Abstract $obj
	 * @param unknown_type $value
	 * @param unknown_type $_prop
	 */
	function setUploadProp(QDB_ActiveRecord_Abstract $obj,$value,$_prop)
	{
		//避免 Form_Upload 将 Helper_Uploader_File 赋值过来
		if(is_string($value))
		{
			$obj->changeProps(array($_prop=>$value),QDB::PROP,null,true);
		}
	}
	
	/**
	 * 获取 $_prop 的 src 属性
	 * 
	 * 
	 * @param QDB_ActiveRecord_Abstract $obj
	 * @param unknown_type $_prop
	 * @return unknown
	 */
	function getUploadPropSrc(QDB_ActiveRecord_Abstract $obj,$_prop,$is_thumb = false)
	{
		if(!empty($obj->$_prop))
		{
			$http_dir = rtrim($this->_settings['upload_config'][$_prop]['http_dir'], '/\\');
			if ($is_thumb)
			{
				return $http_dir . "/" . $obj->{$_prop.'_thumb'};
			}
			else 
			{
				return $http_dir . "/{$obj->$_prop}";
			}
			
		}
		
		return null;
	}
	
	/**
	 * 获取缩略图属性
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 * @param unknown_type $_prop
	 * @return unknown
	 */
	function getUploadPropThumb(QDB_ActiveRecord_Abstract $obj,$_prop)
	{
		if(!empty($obj->$_prop))
		{
			return str_replace('.jpg','-thumb.jpg',$obj->$_prop);
		}
		return null;
	}
	
	/**
	 * 上传文件验证
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 */
	function _validate_upload(QDB_ActiveRecord_Abstract $obj)
	{
		//本打算将验证和执行上传分开的。
	}
	
	/**
	 * 执行上传文件
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 */
	function _exec_upload(QDB_ActiveRecord_Abstract $obj)
	{
		if (empty($this->_settings['upload_config']))
		{
			return ;
		}
		
		$uploader = new Helper_Uploader();
		$error = array();
		
		foreach ($this->_settings['upload_config'] as $file_id => $config)
		{
			//必需
			$post_key = isset($config['post_key']) ? $config['post_key'] : $file_id;
			if (!$uploader->existsFile($post_key))
            {
                if(isset($config['required']) && $config['required'])
                {
                    if ($obj->id() && !empty($obj->$file_id))
                    {
                    	$obj->willChanged($file_id);
                    }
                    else
                    {
                    	$error[$post_key]['required'] = $config['errmsgs']['required'];
                    }
                    
                }
                continue;
            }
            
            $file = $uploader->file($post_key);
            $filename = $file->filename();
            $extname = $file->extname();

            // 验证文件类型
            if(isset($config['allowed_filetypes']) && $config['allowed_filetypes'])
            {
                if(!$file->isValid($config['allowed_filetypes']))
                {
                    $error[$post_key]['allowed_filetypes'] = $config['errmsgs']['allowed_filetypes'];
                    continue;
                }
            }
            else if($file->isValid('.php'))
            {
                $error[$post_key]['allowed_filetypes'] = "PHP 文件是不允许上传的.";
                continue;
            }
            
            //验证文件大小
            if(isset($config['max_size']) && $config['max_size'] > 0)
            {
            	if ($file->filesize() > $config['max_size'] * 1024)
            	{
            		$error[$post_key]['max_size'] = $config['errmsgs']['max_size'];
            		continue;
            	}
            }
            
            // 验证图片尺寸
            if(isset($config['image_dimension']) && $config['image_dimension'])
            {
                list($width, $height, $type, $attr) = getimagesize($file->filepath());
				
                if(!$width || !$height)
                {
                    continue;
                }
                
				list($dim_width,$dim_height) = explode('*',$config['image_dimension']);
				
				
                if(((isset($dim_width) && $dim_width > 0)
                    && ($dim_width != $width)) || ((isset($dim_height) && $dim_height > 0)
                    && ($dim_height != $height)))
                {
                	$error[$post_key]['image_dimension'] = $config['errmsgs']['image_dimension'];
                    continue;
                }
            }
            
	        $dir = rtrim($config['upload_dir'], '/\\') . DS;
	        $date = date('Y-m');
	        $dest_dir = $dir . $date;
	        Helper_Filesys::mkdirs($dest_dir);
	        $md5 = md5($filename . '-' . microtime(true));
			$_prop_filename = '';
	        
	        //如果上图片
	        if ((isset($config['is_image']) && $config['is_image']) 
	        	|| isset($config['large']) || isset($config['thumb']))
	        {
	        	$pic_filename =  $md5 . '.jpg';
			    $image = Helper_Image::createFromFile($file->filepath(), $file->extname());
			    
		        // 生成大图
		        if (isset($config['large']))
		        {
		        	list($w,$h) = explode('*',$config['large']);
			        $image->crop($w, $h);
			    }
			    
			    $image->saveAsJpeg($dest_dir . DS . $pic_filename);
			    $_prop_filename = "{$date}/{$pic_filename}";
		        
			    
		        // 生成缩略图
		        if (isset($config['thumb']))
		        {
		        	$thumb_filename = $md5 . '-thumb.jpg';
		        	list($w,$h) = explode('*',$config['thumb']);
			        $image->crop($w, $h);
			        $image->saveAsJpeg($dest_dir . DS . $thumb_filename);
			    }
			    
		        // 销毁图片资源并删除上传文件
		        $image->destroy();
	        	$file->unlink();
	        }
	        else 
	        {
	        	$file_name = $md5.'.'.$extname;
	        	$file->move($dest_dir . DS . $file_name);
	        	$_prop_filename = "{$date}/{$file_name}";
	        }
	        
	        //如果是更新，删除原有的
	        if ($obj->id() && !empty($obj->$file_id))
            {
               $this->_delete_upload_file($obj,$file_id);
            }
            
            //向属性负值
            $obj->$file_id = $_prop_filename;
		}
		
		if (!empty($error))
        {
            throw new QDB_ActiveRecord_ValidateFailedException($error, $obj);
        }
	}
	
	/**
	 * 删除已上传的文件
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 */
	function _delete_upload(QDB_ActiveRecord_Abstract $obj)
	{
		foreach ($this->_settings['upload_config'] as $_prop => $config)
		{
			$this->_delete_upload_file($obj,$_prop);
		}
	}
	
	/**
	 * 删除已上传文件
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 * @param unknown_type $_prop
	 */
	function _delete_upload_file(QDB_ActiveRecord_Abstract $obj,$_prop)
	{
		if (empty($obj->$_prop))
		{
			return ;
		}
		$config = $this->_settings['upload_config'][$_prop];
		if (isset($config['thumb']))
		{
			@unlink($config['upload_dir'] . DS . $obj->{$_prop.'_thumb'});
		}
		@unlink($config['upload_dir'] . DS . $obj->$_prop);
	}
}
