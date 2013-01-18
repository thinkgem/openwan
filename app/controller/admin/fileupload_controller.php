<?php
// $Id: fileupload_controller.php 906 2010-08-06 09:52:59Z thinkgem $

/**
 * Controller_Admin_FileUpload 文件上载控制器
 * @author WangZhen <thinkgem@163.com>
 */
class Controller_Admin_FileUpload extends Controller_Admin_Abstract
{
        protected function _before_execute(){
            parent::_before_execute();
//            $this->_pathway->addStep('素材上载');
        }
	function actionIndex(){
            $this->_pathway->addStep('素材上载');            
            if($this->_context->isPOST()){
                if (isset($_POST["PHPSESSID"])) {
                    session_id($_POST["PHPSESSID"]);
                }
                if (!isset($_FILES["filedata"]) || !is_uploaded_file($_FILES["filedata"]["tmp_name"]) || $_FILES["filedata"]["error"] != 0) {
                    return '上传失败！';
                }
                $filePath = rtrim(Q::ini('appini/upload/filePath'), '/\\') . DS;                
                Helper_Filesys::mkdirs($filePath);
                //获得上传文件夹
                $dir = 'data1'; $i = 0; $handle = opendir($filePath);
                while (($name = readdir($handle))){
                    if($name != "." && $name != ".."){
                        if(is_dir($filePath . $name) && substr($name, 0, 4) == 'data'){
                            $i++;
                            $dir = $name;
                        }
                    }
                }                
                closedir($handle);
                if($i == 0) Helper_Filesys::mkdirs($filePath.$dir);
                //判断文件中的文件是否超出限制
                $j = 0; $handle = opendir($filePath.$dir);
                while (($name = readdir($handle))){
                    if($name != "." && $name != ".."){
                        $j++;
                    }
                }
                closedir($handle);
                if ($j > 65535) $dir = 'data'.($i+1);
                //得到编码后的文件夹及文件名
                $fileNameMd5 = md5($_FILES["filedata"]["name"] . '-' . microtime(true));
                $filePath .= $dir . DS . $fileNameMd5 . DS;//保存路径名
                $fileName = md5_file($_FILES["filedata"]["tmp_name"]);//文件名
                $fileExt = pathinfo($_FILES["filedata"]["name"], PATHINFO_EXTENSION);//扩展名
                //保存到数据库
                $file = new Files();
                $file->category_id = $this->_context->category_id;
                $file->category_name = $this->_context->category_name;                
                $file->title = substr($_FILES["filedata"]["name"], 0, strrpos($_FILES["filedata"]["name"], '.'));
                $file->name = $fileName;
                $file->ext = $fileExt;
                $file->size = $_FILES["filedata"]["size"];
                $file->path = $filePath;
                $file->status = 0;
                $file->catalog_info = '';
                $file->upload_username = $this->_view['currentUser']['username'];
                $file->upload_at = time();                
                try{
                    $file->save();
                }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                    if(isset($ex->validate_errors['name'])) return $ex->validate_errors['name'];
                    else if(isset($ex->validate_errors['type'])) return $ex->validate_errors['type'];
                    else return '上传失败！'.$ex;
                }
                //保存上传文件
                Helper_Filesys::mkdirs($filePath);
                if (!move_uploaded_file($_FILES["filedata"]["tmp_name"], $filePath.$fileName.'.'.$fileExt)){
                    $file->destroy();//保存文件失败回滚数据
                    return '上传失败！';
                }
                //返回成功结果
                return 'true_'.url('admin::filecatalog/preview', array('id'=>$file->id()));
            }else{
                $categoryId = $this->_context->category_id;
                $categoryId = isset($categoryId) ? $categoryId : 1;
                $category = Category::find()->getById($categoryId);
                $this->_view['category'] = $category;
                $categoryIds = Category::getChildrenIds($categoryId);
                if(count($categoryIds)){//获得历史上传
                    $files = Files::find('category_id in (?) and upload_username=?', $categoryIds, $this->_view['currentUser']['username'])->order('upload_at desc')->top(13)->getAll();
                    $this->_view['files'] = $files;
                }
            }
        }
}


