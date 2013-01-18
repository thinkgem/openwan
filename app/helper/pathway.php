<?php
// $Id: pathway.php 895 2010-03-23 05:36:29Z thinkgem $
/**
 * 很方便的路径工具
 * @Modify 2009年4月27日
 * @author Shaird
 */
class Helper_Pathway {
    
    private $_pathway = array();
    private $_title;

    /**
     * 增加路径
     *
     * @param string $title
     * @param string $url
     */
    public function addStep($title,$url='') {
        $title = $this->strcut($title,15);
        $this->_pathway[] = array('title'=>$title,'url'=>$url);
        $this->_title = $title;
    }

    /**
     * 获取路径
     *
     * @return string
     */
    public function getPathway() {
        $pathway = '<div id="pathway">';
        foreach ($this->_pathway as $path) {
            if(strlen($path['url'])>0)
                $pathway.= '<a href="'.$path['url'].'">'.$path['title'].'</a> >> ';
            else
                $pathway.= $path['title'].' >> ';
        }
        $pathway = substr($pathway,0,-3);
        $pathway .= '</div>';
        return $pathway;
    }

    /**
     * 获取标题
     *
     * @return string
     */
    public function getTitle() {
        return $this->_title;
    }
    
    /**
     * 截取字符串
     */
    protected function strcut($string,$length,$etc='...') {
        $result= '';
        $string = html_entity_decode(trim(strip_tags($string)),ENT_QUOTES,'UTF-8');
        $strlen = strlen($string);

        for($i=0; (($i<$strlen)&& ($length> 0));$i++) {
            $number=strpos(str_pad(decbin(ord(substr($string,$i,1))), 8, '0', STR_PAD_LEFT), '0');
            if($number) {
                if($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            }
            else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }

        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');

        if($i<$strlen) {
            $result .= $etc;
        }
        return $result;
    }
}
/*
addStep($title,$url='')  //作用是增加路径
getPathway()  //获取路径
getTitle()  //获取路径标题，用来管理页面的标题。 很方便

使用方法如下：

1 把这个文件放到 Helper 下

2 修改 controller 下 abstract.php
加入参数 $_pathway;

   1. protected $_pathway;

复制代码
3 在 __constract（） 下加入

   1. //建立路径
   2. $this->_pathway = new Helper_MyPathway();
   3. $this->_pathway->addStep('主页',url());

复制代码
4 在 _before_execute() 里面加入

   1. $this->_view['pathway']=$this->_pathway;

复制代码
5 自动建立页面标题。
在 layout 模板的 title 处加入

   1. <?php echo $pathway->getTitle() ?>

复制代码
6 为Controller 加入统一路径
在 abstract.php 下加入空方法

   1. function init(){}

复制代码
在 execute() 里面添加 init() 的执行

   1.     function execute($action_name, array $args = array())
   2.     {
   3.         $action_method = "action{$action_name}";
   4.         // 执行指定的动作方法
   5.         $this->_before_execute();
   6.         $this->init();
   7.         ....
   8.         ....

复制代码
这样就可以在controller 重载 init() 方法来做统一事件。
例如： 为这个controller 加入 问题中心这个路径

   1. function init(){
   2.   $this->_pathway->addStep('问题中心',url('question/question'));
   3. }

复制代码
7 为单个action 加入自己的路径
方法和上面一样。加入自己的步骤就可以了。
例如：

   1. $this->_pathway->addStep($question_title); //不设置URL的话，路径就没有超链接。

复制代码
8 显示路径
在view 里面

   1. <div id="pathway">
   2.     <?php echo $pathway->getPathway();?>
   3. </div>

复制代码
根据需求也可以把这个现实路径放在layout模板里面。
这样的话，以后的标题更新 和 路径更新，就只需要一个简单的 addStep 就可以了。

*/
?>