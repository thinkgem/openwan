<?php
// $Id: files.php 907 2010-08-11 07:44:06Z thinkgem $

/**
 * Files 封装来自 files 数据表的记录及领域逻辑
 * @author WangZhen <thinkgem@163.com>
 */
class Files extends QDB_ActiveRecord_Abstract
{

    /**
     * 文件编目信息 虚拟属性
     */
//    private $_catalogInfo;

    function getCatalogInfo(){
//        $fileName = $this->getPath().$this->_props['name'].'.'.Q::ini('appini/catalog/fileInfoExt', 'inf');
//        //读取文件编目信息
//        $handle = @fopen($fileName, 'a+');
//        if($handle){
//            $filesize = @filesize($fileName);
//            if ($filesize > 0){
//                $json = @fread ($handle, $filesize);
//                $catalogInfo = Helper_JSON::decode($json);
//            }else{
//                $catalogInfo = array();
//            }
//            @fclose ($handle);
//        }
        $catalogInfo = $this->_props['catalog_info'] == null ? array() : Helper_JSON::decode($this->_props['catalog_info']);
        //获得编目信息分类
        $catalog = Catalog::find('path like ? and enabled=1', '%,'.($this->_props['type'] + 1).',%')->order('weight desc')->asArray()->getAll();
        $catalog = Helper_Array::toTree($catalog, 'id', 'parent_id', 'children');
        //合并编目信息
        foreach($catalog as $k => $v){
            if(isset($catalogInfo[$v['name']])){
                foreach($v['children'] as $_k => $_v){
                    if (!isset($catalogInfo[$v['name']][$_v['name']])){
                        $catalogInfo[$v['name']][$_v['name']] = '';
                    }
                }
            }else{
                foreach($v['children'] as $_k => $_v){
                    $catalogInfo[$v['name']][$_v['name']] = '';
                }
            }
        }
        return $catalogInfo;
    }
    
    function setCatalogInfo($value){
//        $this->_catalogInfo = $value;
        $this->catalog_info = Helper_JSON::encode($value);
    }

    function getPath(){
        return rtrim(Q::ini('appini/upload/filePath'), '/\\') . DS . $this->_props['path'];
    }

    function setPath($value){
        $this->_props['path'] = str_replace(rtrim(Q::ini('appini/upload/filePath'), '/\\') . DS, '', $value);
    }

    function getSizeFormatted(){
        return Helper_Util::formattedFileSize($this->_props['size']);
    }

    function getCatalogInfoFormatted(){
        return Helper_Util::substr(preg_replace('/[{}"(.*)]/', '$1', $this->_props['catalog_info']), 60);
    }

    protected function _before_save() {
        //自动设置文件类型
        if (stripos(Q::ini('appini/upload/videoFileTypes'), '.'.$this->_props['ext'].';')){
            $this->_props['type'] = 1;
        }else if (stripos(Q::ini('appini/upload/audioFileTypes'), '.'.$this->_props['ext'].';')){
            $this->_props['type'] = 2;
        }else if (stripos(Q::ini('appini/upload/imageFileTypes'), '.'.$this->_props['ext'].';')){
            $this->_props['type'] = 3;
        }else if (stripos(Q::ini('appini/upload/richFileTypes'), '.'.$this->_props['ext'].';')){
            $this->_props['type'] = 4;
        }else{
            throw new QDB_ActiveRecord_ValidateFailedException(array('type'=>'文件类型错误'), $this);
        }
    }

//    protected  function _after_save(){
//        //保存文件编目信息
//        if(isset($this->_catalogInfo)){
//            $fileName = $this->getPath().$this->_props['name'].'.'.Q::ini('appini/catalog/fileInfoExt', 'inf');
//            $handle = fopen($fileName, 'w');
//            $value = array_merge(array($this->_props), $this->_catalogInfo);
//            fwrite($handle, Helper_JSON::encode($value));
//            fclose ($handle);
//        }
//    }

    static function getFileStream($id){
        $level = $this->_view['currentUser']['level_id'];
        $group_id = $this->_view['currentUser']['group_id'];
        $file = Files::find('id=? and level<=? and (groups like "%,?,%" or groups="all") and is_download=1', $id, $level, $group_id)->getOne();
        if ($file->isNewRecord()){
            return '您没有下载权限';
        }
        $output = new QView_Output($file->title.'.'.$file->ext);
        if(strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")){
            $output->setOutputFilename(urlencode($file->title.'.'.$file->ext));
        }
        $output->addFile(rtrim($file->path, '/\\').DS.$file->name.'.'.$file->ext);
        return $output;
    }

    static function getPreviewFileStream($id){
        $file = Files::find()->getById($id);
        if ($file->isNewRecord()){
            return;
        }
        $fileName = rtrim($file->path, '/\\').DS.$file->name.'.'.$file->ext;
        if($file->type == 1 || $file->type == 2){
            $fileNamePreview = rtrim($file->path, '/\\').DS.$file->name.'-preview.flv';
            $lock = $fileNamePreview.'.lock';
            while(true){if(!file_exists($lock)) break;}
            if(!file_exists($fileNamePreview) || !@filesize($fileNamePreview)){
                $lock = $fileNamePreview.'.lock';
                if(!file_exists($lock)) {
                    @ignore_user_abort(1);
                    @set_time_limit(0);
                    @touch($lock);
                    $ffmpegPath = Q::ini('appini/catalog/ffmpegPath');
                    $ffmpegParameter = Q::ini('appini/catalog/ffmpegParameter');
                    //$command = "$ffmpegPath -i \"$fileName\" -y -ab 56 -ar 22050 -r 15 -b 500 -s 320x240 \"$fileNamePreview\"";
                    //$command = "$ffmpegPath -i \"$fileName\" -y -ab 56 -ar 22050 -r 15 -s 320x240 \"$fileNamePreview\"";
                    $command = "$ffmpegPath -i \"$fileName\" $ffmpegParameter \"$fileNamePreview\"";
                    //$command .= "&& $ffmpegPath -i \"$fileName\" -y -f image2 -ss 8 -t 0.011 -s 320x240 \"$fileNamePreview.jpg\"";
                    @exec($command);
                    @unlink($lock);
                }
            }            
            $output = new QView_Output($file->title.'.'.$file->ext, 'video/x-flv');
            $output->addFile($fileNamePreview);
            return $output;
        }else if($file->type == 4){
            if ($file->ext == 'txt'){
                $handle = @fopen($fileName, 'r');
                $data = @fread ($handle, @filesize($fileName));
                @fclose ($handle);
                return Helper_Util::encoding($data, 'utf-8');
            }else{
                return '无法预览非文本文件（*.txt）格式';
            }
        }else{
            $output = new QView_Output($file->title);
            $output->addFile($fileName);
            if ($file->ext == 'jpg' || $file->ext == 'jpeg'){
                $output->setMimeType('image/jpeg');
            }
            return $output;
        }
    }

    /**
     * 返回对象的定义
     *
     * @static
     *
     * @return array
     */
    static function __define()
    {
        return array
        (
            // 指定该 ActiveRecord 要使用的行为插件
            'behaviors' => 'uniqueness,formatter',

            // 指定行为插件的配置
            'behaviors_settings' => array
            (
                # '插件名' => array('选项' => 设置),
                'uniqueness' => array(
                    'check_props' => 'name',
                    'error_messages' => array(
                        'name' => '此文件已上传'
                    ),
                ),
                'formatter' => array(
                    'props' => array(
                        'type' => array(
                            'formatter' => 'dict',
                            'dict' => array(
                                1 => '视频',
                                2 => '音频',
                                3 => '图片',
                                4 => '富媒体',
                            ),
                        ),
                        'status' => array(
                            'formatter' => 'dict',
                            'dict' => array(
                                0 => '新节目',
                                1 => '待审核',
                                2 => '已发布',
                                3 => '打回',
                                4 => '删除',
                            ),
                        ),
                        'upload_at' => array(
                            'formatter' => 'date',
                            'format' => 'Y-m-d',
                        ),
                        'catalog_at' => array(
                            'formatter' => 'date',
                            'format' => 'Y-m-d',
                        ),
                        'putout_at' => array(
                            'formatter' => 'date',
                            'format' => 'Y-m-d',
                        ),
//                        'description' => array(
//                            'formatter' => 'nl2br',
//                        ),
                    ),
                ),
            ),

            // 用什么数据表保存对象
            'table_name' => 'files',

            // 指定数据表记录字段与对象属性之间的映射关系
            // 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
            'props' => array
            (
                // 主键应该是只读，确保领域对象的“不变量”
                'id' => array('readonly' => true),

                /**
                 *  可以在此添加其他属性的设置
                 */
                # 'other_prop' => array('readonly' => true),

                /**
                 * 添加对象间的关联
                 */
                # 'other' => array('has_one' => 'Class'),

                'catalogInfo' => array('getter' => 'getCatalogInfo', 'setter' => 'setCatalogInfo'),
                'path' => array('getter' => 'getPath', 'setter' => 'setPath'),
                'size_formatted' => array('getter' => 'getSizeFormatted'),
                'catalog_info_formatted' => array('getter' => 'getCatalogInfoFormatted'),

                //归属上传用户
//                'upload_user' => array(QDB::BELONGS_TO => 'users', 'source_key' => 'upload_username', 'target_key' => 'username'),
                //归属分类
                'category' => array(QDB::BELONGS_TO => 'category', 'source_key' => 'category_id'),
//                //归属父分类
//                'parent' => array(QDB::BELONGS_TO => 'category', 'source_key' => 'parent_id'),
//                //拥有的子分类
//                'children' => array(QDB::HAS_MANY => 'category', 'target_key' => 'parent_id'),

            ),

            /**
             * 允许使用 mass-assignment 方式赋值的属性
             *
             * 如果指定了 attr_accessible，则忽略 attr_protected 的设置。
             */
            'attr_accessible' => '',

            /**
             * 拒绝使用 mass-assignment 方式赋值的属性
             */
            'attr_protected' => 'id',

            /**
             * 指定在数据库中创建对象时，哪些属性的值不允许由外部提供
             *
             * 这里指定的属性会在创建记录时被过滤掉，从而让数据库自行填充值。
             */
            'create_reject' => '',

            /**
             * 指定更新数据库中的对象时，哪些属性的值不允许由外部提供
             */
            'update_reject' => 'type,name,ext,size,path',

            /**
             * 指定在数据库中创建对象时，哪些属性的值由下面指定的内容进行覆盖
             *
             * 如果填充值为 self::AUTOFILL_TIMESTAMP 或 self::AUTOFILL_DATETIME，
             * 则会根据属性的类型来自动填充当前时间（整数或字符串）。
             *
             * 如果填充值为一个数组，则假定为 callback 方法。
             */
            'create_autofill' => array
            (
                # 属性名 => 填充值
                # 'is_locked' => 0,
//                'created' => self::AUTOFILL_TIMESTAMP ,
//                'updated' => self::AUTOFILL_TIMESTAMP ,
            ),

            /**
             * 指定更新数据库中的对象时，哪些属性的值由下面指定的内容进行覆盖
             *
             * 填充值的指定规则同 create_autofill
             */
            'update_autofill' => array
            (
//                'updated' => self::AUTOFILL_TIMESTAMP ,
            ),

            /**
             * 在保存对象时，会按照下面指定的验证规则进行验证。验证失败会抛出异常。
             *
             * 除了在保存时自动验证，还可以通过对象的 ::meta()->validate() 方法对数组数据进行验证。
             *
             * 如果需要添加一个自定义验证，应该写成
             *
             * 'title' => array(
             *        array(array(__CLASS__, 'checkTitle'), '标题不能为空'),
             * )
             *
             * 然后在该类中添加 checkTitle() 方法。函数原型如下：
             *
             * static function checkTitle($title)
             *
             * 该方法返回 true 表示通过验证。
             */
            'validations' => array
            (
                'category_id' => array
                (
                    array('is_int', '分类编号必须是一个整数'),

                ),

                'category_name' => array
                (
                    array('not_empty', '分类名称不能为空'),
                    array('max_length', 64, '分类名称不能超过 64 个字符'),

                ),

                'upload_username' => array
                (
                    array('not_empty', '上传用户名称不能为空'),
                    array('max_length', 64, '上传用户名称不能超过 64 个字符'),

                ),

                'catalog_username' => array
                (
                    array('max_length', 64, '编目用户名称不能超过 64 个字符'),

                ),

                'putout_username' => array
                (
                    array('max_length', 64, '发布用户名称不能超过 64 个字符'),

                ),

                'type' => array
                (
                    array('is_int', '文件类型（1：视频；2：音频；3：图片；4：富媒体）必须是一个整数'),

                ),

                'title' => array
                (
                    array('not_empty', '显示标题不能为空'),
                    array('max_length', 255, '显示标题不能超过 255 个字符'),

                ),

                'name' => array
                (
                    array('not_empty', '文件名不能为空'),
                    array('max_length', 255, '文件名不能超过 255 个字符'),

                ),

                'ext' => array
                (
                    array('not_empty', '扩展名不能为空'),
                    array('max_length', 16, '扩展名不能超过 16 个字符'),

                ),

                'path' => array
                (
                    array('not_empty', '存放路径不能为空'),
                    array('max_length', 255, '存放路径不能超过 255 个字符'),

                ),

                'status' => array
                (
                    array('is_int', '状态（0：新节目；1：待审核；2：已发布；3：打回；4：删除（回收站）；）必须是一个整数'),

                ),

                'level' => array
                (
                    array('is_int', '浏览等级必须是一个整数'),

                ),

                'groups' => array
                (
                    array('max_length', 255, '可浏览的用户组不能超过 255 个字符'),

                ),


            ),
        );
    }


/* ------------------ 以下是自动生成的代码，不能修改 ------------------ */

    /**
     * 开启一个查询，查找符合条件的对象或对象集合
     *
     * @static
     *
     * @return QDB_Select
     */
    static function find()
    {
        $args = func_get_args();
        return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);
    }

    /**
     * 返回当前 ActiveRecord 类的元数据对象
     *
     * @static
     *
     * @return QDB_ActiveRecord_Meta
     */
    static function meta()
    {
        return QDB_ActiveRecord_Meta::instance(__CLASS__);
    }


/* ------------------ 以上是自动生成的代码，不能修改 ------------------ */

}

