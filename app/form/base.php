<?php
// $Id: base.php 895 2010-03-23 05:36:29Z thinkgem $
//封装表单
//$$$  riiyii@126.com
//$$$  QQ:605107424

class Form_Base extends QForm {
    public $has_file;
    public $must;
    function __construct($config_name='default',$udi =null,$model=null,$has_file = null,$must = false) {
        parent::__construct('form_'.$config_name, url($udi),'post');
        if($has_file) {
            $this->enctype = self::ENCTYPE_MULTIPART;
            $this->has_file = $has_file;
            $this->must = $must;
        }
        $file_name = $config_name.'_form.yaml';
        $filename  =  rtrim(dirname(__FILE__), '/\\'). DS . $file_name;
        $this->loadFromConfig(Helper_YAML::loadCached($filename));
        $this->addValidations(QDB_ActiveRecord_Meta::instance($model));
    }
    function setTypes($allow_type,$max_size) {
        Helper_Upload::setAllow($allow_type,$max_size) ;
    }
    function validate($data, & $failed = null) {
        $ret = parent::validate($data);
        if($this->has_file) {
            try {
                $errors = array();
                foreach($_FILES as $k=>$v) {

                    if(isset($v['error'])&&$v['error']==UPLOAD_ERR_NO_FILE) {
                        if($this->must) {
                            $errors[] = '请选择上传文件!';
                            break;
                        }
                        continue;
                    }

                    $fileExt = Helper_Upload::fileExt($v['name']);
                    if (!in_array(strtolower($fileExt),Helper_Upload::getTypes())) {
                        $errors[] = '上传文件的类型不符合要求';
                    }
                    if ($v['size']>Helper_Upload::getSize()) {
                        $errors[] = '上传文件的大小超过限制';
                    }
                }
                if (empty($errors)) {
                    return $ret;
                }
                $this[$this->has_file]->invalidate(implode(', ', $errors));
                return false;
            }
            catch (Exception $ex) {
                $this[$this->has_file]->invalidate($ex->getMessage());
                return false;
            }
        }
        else {
            return $ret;
        }
    }
}
?>