<?php
// $Id: permissions.php 895 2010-03-23 05:36:29Z thinkgem $
/**
 * 列出控制器目录中的所有控制器及方法的类
**/
class Helper_Permissions
{
    /**
    * 控制器所在目录
    * @var dir $controller_dir
    */
    var $controller_dir;

    # 文件操作部分
    /** #############################################################################
    * 递归找到指定目录中的全部控制器和动作，并返回数组
    * @return array
    */
    function find_Permissions(){
        $ctrl_files = array_flip($this->_find_Controller_file($this->controller_dir));
        return $this->_find_in_file($ctrl_files);
    }

    /** #############################################################################
    * 递归找到指定目录中的全部PHP文件
    * @param dir $ddir
    * @param array $ctrl_files
    * @return array
    */
    function _find_Controller_file($ddir, $ctrl_files = array()){
        $handle=opendir($ddir);

        while ($file = readdir($handle)){
            $bdir = $ddir."/".$file;

            // 判断此文件是一个文件夹文件
            if ($file != '.' && $file != '..' && filetype($bdir) == 'dir' ){
               $ctrl_files = $this->_find_Controller_file($ddir."/".$file, $ctrl_files);
            // 还必须是个PHP文件,并且符合控制器命名规则 test_controller.php
            }elseif ($file != '.' && $file != '..' && substr(strtolower($file),-15)=='_controller.php' ){
               $ctrl_files[] = substr(str_replace($this->controller_dir,'',$ddir."/".$file),1);
            }
        }
        closedir($handle);
        return $ctrl_files;
    }

    /** #############################################################################
    * 找到contrller名称及action名称
    * @param array $ctrl_files
    * @return array
    */
    function _find_in_file($ctrl_files){
        $i = 0;
        foreach ($ctrl_files as $filename=>$value) {
            $matched = '';
            $file_content = php_strip_whitespace($this->controller_dir.$filename);
            $ctrl_files[$filename] = array();
            $controller_prefix = 'Controller';
            $action_prefix = 'action';

            // 匹配并保存控制器类命名空间和名称
            $controller_prefix = 'Controller';
            $pattern = '/class\s'.$controller_prefix.'_(.+?)\sextends/i';
            preg_match_all($pattern,$file_content,$matched);
            if(isset($matched[1][0])){
                //$ctrl_files[$filename]['controller'] = $matched[1][0];
                if (stripos( $filename , '/')){
                    $nsctl = explode('_', $matched[1][0] );
                    $ctrl_files[$filename]['namespace'] = strtolower($nsctl[0]);
                    $ctrl_files[$filename]['controller'] = $nsctl[1];//strtolower($nsctl[1]);
                }elseif (stripos( $matched[1][0] , '_')){
                    $nsctl = explode('_', $matched[1][0] );
                    $ctrl_files[$filename]['namespace'] = strtolower($nsctl[0]);
                    $ctrl_files[$filename]['controller'] = $nsctl[1];//strtolower($nsctl[1]);
                }else{
                    $ctrl_files[$filename]['namespace'] = 'default';
                    $ctrl_files[$filename]['controller'] = $matched[1][0];//strtolower($matched[1][0]);
                }
            }

            // 匹配并保存动作方法名称，直接返回数组
            $pattern = '/function\s'.$action_prefix.'(.+?)\(.+?\{(.+?)\}/';
            preg_match_all($pattern,$file_content,$matched);
            if(isset($matched[1])){
                foreach ($matched[1] as $value){
                    $ctrl_files[$filename]['action'][] = $value;//strtolower($value);
                }
                foreach ($matched[2] as $value){
                    $pattern2 = '/_pathway\->addStep\(\'(.+?)\'\);/';
                    preg_match_all($pattern2,$value,$matched2);
                    $ctrl_files[$filename]['aliasname'][] = empty($matched2[1])?'':$matched2[1][0];
                }
            }
        }
        return $ctrl_files;
    }

    /** #############################################################################
    * 将文件中找到的数组结构整理成简单数组的格式
    * @return array
    */
    function makeAllActionsEasy($allActionsarr){
        $i = 0;
        foreach ($allActionsarr as $ActionsArr) {
            foreach ($ActionsArr['action'] as $key=>$oneAction){
                $allActionsEasy[$i]['nca'] = $ActionsArr['namespace']."_".$ActionsArr['controller']."_".$oneAction;
                $allActionsEasy[$i]['namespace'] = $ActionsArr['namespace'];
                $allActionsEasy[$i]['controller'] = $ActionsArr['controller'];
                $allActionsEasy[$i]['action'] = $oneAction;
                $allActionsEasy[$i]['aliasname'] = $ActionsArr['aliasname'][$key];
                $i++;
            }
        }
        return $allActionsEasy;
    }

    # 数据操作部分
    /** #############################################################################
     * 更新数据库中的Permissions
     * @param dir $ctrlDir
     * @return bool
     */
    function updatePermissions($updateCtrlDir = '') {
        if (is_dir($updateCtrlDir) && stripos( $updateCtrlDir , '/app/controller/')) {
            $newDbActions = $newPermissions = $delPermissions = array();
            $this->controller_dir = $updateCtrlDir;
            // 得到指定目录中的全部Actions
            $allActionsarr = $this->find_Permissions();
            $allActionsarr = $this->makeAllActionsEasy($allActionsarr);
            foreach ($allActionsarr as $key=>$value){
                $allActions[] = $value['nca'];
            }
            //return $allPermissions;
            // 得到数据库中的全部Actions
            $dbActions = Permissions::find()->all()->asArray()->query();
            // 整理成简单数组的格式
            foreach ($dbActions as $oneActions) {
                $newDbActions[$oneActions['id']] = $oneActions['namespace']."_".$oneActions['controller']."_".$oneActions['action'];
            }
            // 不在数据库中的Actions将输入数据库，先用array_diff取得差值，键名保持不变。
            $newPermissions = array_diff($allActions, $newDbActions);
            if (count($newPermissions)){
                // 构造$newPermissions的键名数组,用来构成输入数据库的数组
                $creatStr = array();
                foreach ($newPermissions as $key=>$newPermission) {
                    //$permissionName = explode('_', $newPermission);
                    $insert_data = new Permissions(array(
                                        "namespace" => $allActionsarr[$key]['namespace'],
                                        "controller" => $allActionsarr[$key]['controller'],
                                        "action" => $allActionsarr[$key]['action'],
                                        "aliasname" => $allActionsarr[$key]['aliasname'],
                    ));
                    $insert_data->save();
                }
            }
            // 在数据库中却在文件中没有的Actions将从数据库删除
            $delPermissions = array_diff($newDbActions, $allActions);
            if (count($delPermissions)){
                // 构造要删除的Actions的id列表
                $delConditions = array();
                foreach ($delPermissions as $key => $delPermission) {
                    Permissions::meta()->destroyWhere('id = ?',$key);
                }
            }
            return true;
        }else{
            return false;
        }
    }

    /** #############################################################################
     * 取得数据库中的命名空间和控制器，并生成二维数组
     * @param dir $ctrlDir
     * @return array
     */
    static function getNamespaceControllerArrs() {
        $nc = Permissions::find() ->setColumns('namespace,controller') -> distinct(true)->asArray() -> getAll();
        $ncArr = array();
        foreach ($nc as $value){
            $ncArr[$value['namespace']][] = $value['controller'];
        }
        return $ncArr;
    }

    /** #############################################################################
     * 取得数据库中的所有权限信息，并生成多维数组
     * @param dir $ctrlDir
     * @return array
     * 结果1返回无id的等级数组，结果2返回有id的等级数组
     * 结果1： array ( [namespace] => array( [controler] => array( [action] => 'aliasname' ) ) )
     * 结果2：array ( [namespace] => array( [controler] => array( [action] => array('key'=>'aliasname') ) ) )
     */
    static function getAllPermissions() {
        $ap_db = Permissions::find()->all()->asArray()->query();
        $ap_arr = array();
        foreach ($ap_db as $value){
            if (empty($value['aliasname'])){
                $value['aliasname'] = $value['action'];
            }
            //$ap_arr[$value['namespace']][$value['controller']][$value['action']] = $value['aliasname'] ;
            $ap_arr[$value['namespace']][$value['controller']][$value['action']] = array( 
                            'id' => $value['id'],
                            'aliasname' => $value['aliasname'],
                            'rbac' => $value['rbac']
                            );
        }
        //dump($ap_arr);
        return $ap_arr;
    }
    
}
?>