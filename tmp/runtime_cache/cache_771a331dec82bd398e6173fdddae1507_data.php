<?php return array (
  'expired' => 1358513975,
  'data' => 
  array (
    'runtime_session_provider' => NULL,
    'runtime_session_start' => true,
    'runtime_cache_dir' => 'D:\\GitHub\\openwan/tmp/runtime_cache',
    'runtime_cache_backend' => 'QCache_PHPDataFile',
    'runtime_response_header' => true,
    'error_display' => true,
    'error_display_friendly' => true,
    'error_display_source' => true,
    'error_language' => 'zh_cn',
    'assert_enabled' => true,
    'assert_warning' => true,
    'assert_exception' => false,
    'dispatcher_url_mode' => 'rewrite',
    'routes_cache_lifetime' => 1,
    'acl_default' => 
    array (
      'allow' => 'ACL_EVERYONE',
    ),
    'acl_global' => 
    array (
      'all_controllers' => 
      array (
        'allow' => 'ADMIN',
      ),
      'admin' => 
      array (
        'all_controllers' => 
        array (
          'allow' => 'ADMIN',
        ),
        'aclmanager' => 
        array (
          'actions' => 
          array (
            'all_actions' => 
            array (
              'allow' => 'ADMIN',
            ),
            'index' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'user' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'useradd' => 
            array (
              'allow' => 'ADMIN,SYSTEM,NORMAL',
            ),
            'useredit' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'group' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'groupview' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'groupbind' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'role' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'roleview' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'rolebind' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'permission' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'permissionrefresh' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'makeaclfile' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'level' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'levelview' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'userdel' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
          ),
        ),
        'default' => 
        array (
          'actions' => 
          array (
            'index' => 
            array (
              'allow' => 'ACL_EVERYONE',
            ),
            'login' => 
            array (
              'allow' => 'ACL_NO_ROLE',
            ),
            'logout' => 
            array (
              'allow' => 'ACL_HAS_ROLE',
            ),
          ),
        ),
        'dictmanager' => 
        array (
          'actions' => 
          array (
            'all_actions' => 
            array (
              'allow' => 'ADMIN',
            ),
            'index' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'category' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'categoryadd' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'categoryedit' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'categorydel' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'catalog' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'catalogadd' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'catalogedit' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
            'catalogdel' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
          ),
        ),
        'filecatalog' => 
        array (
          'actions' => 
          array (
            'all_actions' => 
            array (
              'allow' => 'ADMIN',
            ),
            'index' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
          ),
        ),
        'fileputout' => 
        array (
          'actions' => 
          array (
            'all_actions' => 
            array (
              'allow' => 'ADMIN',
            ),
            'index' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
          ),
        ),
        'filesearch' => 
        array (
          'actions' => 
          array (
            'all_actions' => 
            array (
              'allow' => 'ADMIN',
            ),
            'index' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
          ),
        ),
        'fileupload' => 
        array (
          'actions' => 
          array (
            'all_actions' => 
            array (
              'allow' => 'ADMIN',
            ),
            'index' => 
            array (
              'allow' => 'ADMIN,SYSTEM',
            ),
          ),
        ),
        'usercenter' => 
        array (
          'actions' => 
          array (
            'index' => 
            array (
              'allow' => 'ACL_HAS_ROLE',
            ),
            'changeinfo' => 
            array (
              'allow' => 'ACL_HAS_ROLE',
            ),
            'changepassword' => 
            array (
              'allow' => 'ACL_HAS_ROLE',
            ),
          ),
        ),
      ),
      'default' => 
      array (
        'actions' => 
        array (
          'index' => 
          array (
            'allow' => 'ACL_EVERYONE',
          ),
        ),
      ),
    ),
    'db_log_enabled' => true,
    'db_default_dsn' => NULL,
    'db_meta_lifetime' => 10,
    'db_meta_cached' => true,
    'db_meta_cache_backend' => 'QCache_PHPDataFile',
    'i18n_response_charset' => 'utf-8',
    'i18n_multi_languages' => false,
    'l10n_default_timezone' => 'Asia/Shanghai',
    'log_enabled' => true,
    'log_priorities' => 'EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG',
    'log_cache_chunk_size' => 64,
    'log_writer_dir' => 'D:\\GitHub\\openwan/log',
    'log_writer_filename' => 'devel.log',
    'session_cookie_name' => 'openwan_sess',
    'acl_session_key' => 'acl_openwan_userdata',
    'db_dsn_pool' => 
    array (
      'devel' => 
      array (
        'driver' => 'mysql',
        'host' => 'localhost:3306',
        'login' => 'root',
        'password' => NULL,
        'database' => 'openwan_db',
        'charset' => 'utf8',
        'prefix' => 'ow_',
      ),
      'test' => 
      array (
        '_use' => 'devel',
      ),
      'deploy' => 
      array (
        '_use' => 'devel',
      ),
      'default' => 
      array (
        'driver' => 'mysql',
        'host' => 'localhost:3306',
        'login' => 'root',
        'password' => NULL,
        'database' => 'openwan_db',
        'charset' => 'utf8',
        'prefix' => 'ow_',
      ),
    ),
    'appini' => 
    array (
      'site' => 
      array (
        'title' => 'OpenWan 媒资管理系统',
      ),
      'upload' => 
      array (
        'filePath' => 'D:\\GitHub\\openwan\\data',
        'fileSizeLimit' => '100 MB',
        'videoFileTypes' => '*.mpeg;*.mpg;*.mp4;*.avi;*.dat;*.vob;*.mov;*.rm;*.rmvb;*.flv;*.mxf;*.wmv;*.asf;*.swf;*.rt;',
        'audioFileTypes' => '*.wav;*.wma;*.mp3;',
        'imageFileTypes' => '*.jpeg;*.jpg;*.gif;*.cr2;*.nef;*.tiff;*.jpeg;*.bmp;*.png;',
        'richFileTypes' => '*.txt;*.rtf;*.asci;*.ascii;*.wml;*.smi;*.asx;*.lrc;*.doc;*.docx;*.wps;*.pm;*.ppt;*.pptx;*.ag;*.cdr;*.xls;*.xlsx;*.csv;*.rar;*.zip;*.7z;*.gz;*.gz2;*.hqx;*.tar;*.arj;*.dwg;*.ppm;*.vsd;*.pdf;*.pst;*.eml;*.xml;*.html;*.htm;*.db;*.mdb;*.accdb;*.iso;*.chm;',
        'fileTypesDescription' => '所有支持的文件',
      ),
      'catalog' => 
      array (
        'fileInfoExt' => 'inf',
        'ffmpegPath' => 'D:\\GitHub\\openwan\\lib\\ffmpeg\\ffmpeg.exe',
        'ffmpagParameter' => '-y -ab 56 -ar 22050 -r 15 -s 320x240',
      ),
      'search' => 
      array (
        'sphinxApi' => 'D:\\GitHub\\openwan\\lib\\sphinx\\sphinxapi.php',
        'sphinxHost' => 'localhost',
        'sphinxPort' => 3312,
        'sphinxLimit' => 4,
        'sphinxDelta' => 'D:\\GitHub\\openwan\\csft\\bin\\indexer.exe --config D:\\GitHub\\openwan\\csft\\etc\\csft.conf delta --rotate && D:\\GitHub\\openwan\\csft\\bin\\indexer.exe --config D:\\GitHub\\openwan\\csft\\etc\\csft.conf --merge main delta --merge-dst-range deleted 0 0 --rotate',
      ),
      'nav' => 
      array (
        'top' => 
        array (
          0 => 
          array (
            'title' => '素材上载',
            'udi' => 'admin::fileUpload',
          ),
          1 => 
          array (
            'title' => '媒资编目',
            'udi' => 'admin::fileCatalog',
          ),
          2 => 
          array (
            'title' => '审核发布',
            'udi' => 'admin::filePutOut',
          ),
          3 => 
          array (
            'title' => '检索下载',
            'udi' => 'admin::fileSearch',
          ),
          4 => 
          array (
            'title' => '访问控制',
            'udi' => 'admin::aclManager',
          ),
          5 => 
          array (
            'title' => '字典管理',
            'udi' => 'admin::dictManager',
          ),
          6 => 
          array (
            'title' => '数据管理',
            'udi' => 'admin::dataManager',
          ),
          7 => 
          array (
            'title' => '个人中心',
            'udi' => 'admin::userCenter',
          ),
        ),
        'left' => 
        array (
          'fileCatalog' => 
          array (
            0 => 
            array (
              'title' => '视频编目',
              'udi' => 'admin::fileCatalog/index',
              'args' => 'type/1',
            ),
            1 => 
            array (
              'title' => '音频编目',
              'udi' => 'admin::fileCatalog/index',
              'args' => 'type/2',
            ),
            2 => 
            array (
              'title' => '图片编目',
              'udi' => 'admin::fileCatalog/index',
              'args' => 'type/3',
            ),
            3 => 
            array (
              'title' => '富媒体编目',
              'udi' => 'admin::fileCatalog/index',
              'args' => 'type/4',
            ),
          ),
          'filePutout' => 
          array (
            0 => 
            array (
              'title' => '视频审核发布',
              'udi' => 'admin::filePutout/index',
              'args' => 'type/1',
            ),
            1 => 
            array (
              'title' => '音频审核发布',
              'udi' => 'admin::filePutout/index',
              'args' => 'type/2',
            ),
            2 => 
            array (
              'title' => '图片审核发布',
              'udi' => 'admin::filePutout/index',
              'args' => 'type/3',
            ),
            3 => 
            array (
              'title' => '富媒体审核发布',
              'udi' => 'admin::filePutout/index',
              'args' => 'type/4',
            ),
          ),
          'fileSearch' => 
          array (
            0 => 
            array (
              'title' => '全部资料',
              'udi' => 'admin::fileSearch/index',
              'args' => 'type/0',
            ),
            1 => 
            array (
              'title' => '视频资料',
              'udi' => 'admin::fileSearch/index',
              'args' => 'type/1',
            ),
            2 => 
            array (
              'title' => '音频资料',
              'udi' => 'admin::fileSearch/index',
              'args' => 'type/2',
            ),
            3 => 
            array (
              'title' => '图片资料',
              'udi' => 'admin::fileSearch/index',
              'args' => 'type/3',
            ),
            4 => 
            array (
              'title' => '富媒体资料',
              'udi' => 'admin::fileSearch/index',
              'args' => 'type/4',
            ),
          ),
          'userCenter' => 
          array (
            0 => 
            array (
              'title' => '个人资料',
              'udi' => 'admin::userCenter/changeInfo',
            ),
            1 => 
            array (
              'title' => '修改密码',
              'udi' => 'admin::userCenter/changePassword',
            ),
          ),
          'aclManager' => 
          array (
            0 => 
            array (
              'title' => '添加用户',
              'udi' => 'admin::aclManager/userAdd',
            ),
            1 => 
            array (
              'title' => '用户管理',
              'udi' => 'admin::aclManager/user',
            ),
            2 => 
            array (
              'title' => '用户组管理',
              'udi' => 'admin::aclManager/group',
            ),
            3 => 
            array (
              'title' => '角色管理',
              'udi' => 'admin::aclManager/role',
            ),
            4 => 
            array (
              'title' => '权限管理',
              'udi' => 'admin::aclManager/permission',
            ),
            5 => 
            array (
              'title' => '浏览等级管理',
              'udi' => 'admin::aclManager/level',
            ),
          ),
          'dictManager' => 
          array (
            0 => 
            array (
              'title' => '资源库分类表',
              'udi' => 'admin::dictManager/category',
            ),
            1 => 
            array (
              'title' => '编目信息表',
              'udi' => 'admin::dictManager/catalog',
            ),
          ),
          'dataManager' => 
          array (
            0 => 
            array (
              'title' => '数据迁移',
              'udi' => 'admin::dataManager/migration',
            ),
          ),
        ),
        'bottom' => 
        array (
          0 => 
          array (
            'title' => '关于我们',
            'udi' => 'default::about',
          ),
        ),
      ),
    ),
    'routes' => 
    array (
      'admin' => 
      array (
        'pattern' => '/admin/:controller/:action/*',
        'defaults' => 
        array (
          'namespace' => 'admin',
          'controller' => 'default',
          'action' => 'index',
        ),
      ),
      '_default_' => 
      array (
        'pattern' => '/:controller/:action/*',
        'defaults' => 
        array (
          'controller' => 'default',
          'action' => 'index',
        ),
      ),
    ),
  ),
);