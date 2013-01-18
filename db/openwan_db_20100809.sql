/*
SQLyog Ultimate v8.32 
MySQL - 5.0.67-community-nt : Database - openwan_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`openwan_db` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `openwan_db`;

/*Table structure for table `cs_counter` */

DROP TABLE IF EXISTS `cs_counter`;

CREATE TABLE `cs_counter` (
  `id` int(11) NOT NULL COMMENT '计数器编号',
  `maxid` int(11) NOT NULL COMMENT '最大文档编号',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档计数器，用于全文检索更新索引';

/*Data for the table `cs_counter` */

/*Table structure for table `ow_catalog` */

DROP TABLE IF EXISTS `ow_catalog`;

CREATE TABLE `ow_catalog` (
  `id` int(11) NOT NULL auto_increment COMMENT '编号',
  `parent_id` int(11) NOT NULL COMMENT '父编号',
  `path` varchar(255) NOT NULL COMMENT '层次路径',
  `name` varchar(64) NOT NULL COMMENT '显示名称',
  `description` varchar(255) NOT NULL default '' COMMENT '描述信息',
  `weight` int(11) NOT NULL default '0' COMMENT '权重',
  `enabled` tinyint(2) NOT NULL default '1' COMMENT '可用性',
  `created` int(11) NOT NULL COMMENT '创建时间',
  `updated` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `path` (`path`)
) ENGINE=MyISAM AUTO_INCREMENT=80 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='文件编目属性';

/*Data for the table `ow_catalog` */

insert  into `ow_catalog`(`id`,`parent_id`,`path`,`name`,`description`,`weight`,`enabled`,`created`,`updated`) values (1,-1,'-1,','编目信息','编目信息',0,1,1256175802,1256175802),(2,1,'-1,1,','视频编目','',0,1,1256175802,1256176962),(3,1,'-1,1,','音频编目','',0,1,1256175809,1256175809),(4,1,'-1,1,','图片编目','',0,1,1256175829,1256175829),(5,1,'-1,1,','富媒体编目','',0,1,1256175836,1256175836),(10,4,'-1,1,4,','地方放的','',0,1,1256176900,1256176900),(11,5,'-1,1,5,','爱爱爱','',0,1,1256176904,1256176904),(14,11,'-1,1,5,11,','11','',0,1,1256535126,1256535126),(15,11,'-1,1,5,11,','分割','',0,1,1256535377,1256535377),(16,11,'-1,1,5,11,','过后','',0,1,1256535382,1256535382),(29,4,'-1,1,4,','啊啊是','',0,1,1258711196,1258711196),(30,4,'-1,1,4,','版本','',0,1,1258711203,1258711203),(31,10,'-1,1,4,10,','断点','',0,1,1258711207,1258711207),(32,10,'-1,1,4,10,','搜索','',0,1,1258711212,1258711212),(33,29,'-1,1,4,29,','撒','',0,1,1258711218,1258711218),(34,29,'-1,1,4,29,','公告','',0,1,1258711223,1258711223),(35,29,'-1,1,4,29,','嗯嗯','',0,1,1258711227,1258711227),(36,30,'-1,1,4,30,','高高挂','',0,1,1258711231,1258711231),(37,30,'-1,1,4,30,','呵呵','',0,1,1258711236,1258711236),(49,5,'-1,1,5,','fgfdg','',0,1,1261557179,1261557179),(50,49,'-1,1,5,49,','ddd','',0,1,1261557183,1261557183),(57,3,'-1,1,3,','sdfasd','',3,1,1267156474,1267156625),(59,57,'-1,1,3,57,','cce','',0,1,1267156484,1267156484),(61,3,'-1,1,3,','基本信息','',6,1,1267157084,1267171867),(65,61,'-1,1,3,61,','地方','',5,1,1267170606,1267173637),(66,61,'-1,1,3,61,','标题','',0,1,1267170827,1267170827),(67,2,'-1,1,2,','题名','',0,1,1267597401,1267597430),(68,67,'-1,1,2,67,','正题名','',0,1,1267597423,1267597423),(69,67,'-1,1,2,67,','并列正题名','',0,1,1267597462,1267597462),(70,67,'-1,1,2,67,','副题名','',0,1,1267597479,1267597479),(71,67,'-1,1,2,67,','交替副题名','',0,1,1267597491,1267597491),(72,67,'-1,1,2,67,','题名说明','',0,1,1267597503,1267597503),(73,67,'-1,1,2,67,','系列题名','',0,1,1267597513,1267597513),(74,2,'-1,1,2,','主题','',0,1,1267597525,1267597582),(75,67,'-1,1,2,67,','并列题名','',0,1,1267597534,1267597534),(76,67,'-1,1,2,67,','分集总数','',0,1,1267597548,1267597548),(77,67,'-1,1,2,67,','分集次','',0,1,1267597559,1267597559),(78,74,'-1,1,2,74,','分类名','',0,1,1267597602,1267597602),(79,74,'-1,1,2,74,','主题词','',0,1,1267597613,1267597613);

/*Table structure for table `ow_category` */

DROP TABLE IF EXISTS `ow_category`;

CREATE TABLE `ow_category` (
  `id` int(11) NOT NULL auto_increment COMMENT '编号',
  `parent_id` int(11) NOT NULL COMMENT '父编号',
  `path` varchar(255) NOT NULL COMMENT '层次路径',
  `name` varchar(64) NOT NULL COMMENT '显示名称',
  `description` varchar(255) NOT NULL default '' COMMENT '描述信息',
  `weight` int(11) NOT NULL default '0' COMMENT '权重',
  `enabled` tinyint(2) NOT NULL default '1' COMMENT '可用性',
  `created` int(11) NOT NULL COMMENT '创建时间',
  `updated` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `path` (`path`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='资源库分类ȡ';

/*Data for the table `ow_category` */

insert  into `ow_category`(`id`,`parent_id`,`path`,`name`,`description`,`weight`,`enabled`,`created`,`updated`) values (1,-1,'-1,','资源分类','资源分类',0,1,1256096904,1256096904),(2,1,'-1,1,','央视教育','教育资源',22,1,1256109496,1280807538),(3,2,'-1,1,2,','体育教学','',0,1,1256125868,1280806847),(5,2,'-1,1,2,','百家讲坛','',0,1,1256125900,1280806864),(6,5,'-1,1,2,5,','三国','',2,1,1256125911,1280827825),(7,3,'-1,1,2,3,','跟着大师看奥运','人发',0,1,1256128410,1280807034),(9,1,'-1,1,','山东卫视','',0,1,1256170953,1280808367),(57,5,'-1,1,2,5,','史记之汉武帝','',3,1,1256528971,1280827751),(62,57,'-1,1,2,5,57,','继位大统','',0,1,1256529336,1280807340),(73,57,'-1,1,2,5,57,','武帝新政','',0,1,1256535437,1280807351),(81,5,'-1,1,2,5,','孔子','',0,0,1257157162,1280807377),(84,6,'-1,1,2,5,6,','说曹操','',0,1,1267602393,1280807273),(85,6,'-1,1,2,5,6,','重归一统','',0,1,1267602401,1280807266),(86,2,'-1,1,2,','走遍中国','',0,1,1280806874,1280806874),(87,2,'-1,1,2,','探索发现','',0,1,1280806883,1280806883),(88,2,'-1,1,2,','农业科教','',0,1,1280806895,1280806895),(89,2,'-1,1,2,','科普教育','',0,1,1280806911,1280806911),(90,2,'-1,1,2,','中国史话','',0,1,1280806939,1280806939),(91,2,'-1,1,2,','法律讲堂','',0,1,1280806949,1280806949),(92,2,'-1,1,2,','中华医药','',0,1,1280806961,1280806961),(93,1,'-1,1,','齐鲁频道','',0,1,1280806977,1280808387),(94,2,'-1,1,2,','综艺类','',0,1,1280806993,1280806993),(95,3,'-1,1,2,3,','时尚体育','',0,1,1280807048,1280807193),(96,95,'-1,1,2,3,95,','网球','',0,1,1280807064,1280807064),(97,95,'-1,1,2,3,95,','高尔夫','',0,1,1280807073,1280807073),(98,95,'-1,1,2,3,95,','马上生活','',0,1,1280807083,1280807083),(99,95,'-1,1,2,3,95,','毽秀','',0,1,1280807131,1280807131),(100,95,'-1,1,2,3,95,','办公室健身法','',0,1,1280807147,1280807147);

/*Table structure for table `ow_category_upload` */

DROP TABLE IF EXISTS `ow_category_upload`;

CREATE TABLE `ow_category_upload` (
  `id` int(11) NOT NULL auto_increment COMMENT '编号',
  `parent_id` int(11) NOT NULL COMMENT '父编号',
  `path` varchar(255) NOT NULL COMMENT '层次路径',
  `name` varchar(64) NOT NULL COMMENT '显示名称',
  `description` varchar(255) NOT NULL default '' COMMENT '描述信息',
  `weight` int(11) NOT NULL default '0' COMMENT '权重',
  `enabled` tinyint(2) NOT NULL default '1' COMMENT '可用性',
  `created` int(11) NOT NULL COMMENT '创建时间',
  `updated` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='上载库分类表';

/*Data for the table `ow_category_upload` */

/*Table structure for table `ow_files` */

DROP TABLE IF EXISTS `ow_files`;

CREATE TABLE `ow_files` (
  `id` bigint(20) unsigned NOT NULL auto_increment COMMENT '编号',
  `category_id` int(11) NOT NULL COMMENT '分类编号',
  `category_name` varchar(64) NOT NULL COMMENT '分类名称',
  `type` int(11) NOT NULL default '1' COMMENT '文件类型（1：视频；2：音频；3：图片；4：富媒体）',
  `title` varchar(255) NOT NULL COMMENT '显示标题',
  `name` varchar(255) NOT NULL COMMENT '文件名',
  `ext` varchar(16) NOT NULL COMMENT '扩展名',
  `size` bigint(20) NOT NULL default '0' COMMENT '文件大小',
  `path` varchar(255) NOT NULL COMMENT '存放路径',
  `status` int(11) NOT NULL COMMENT '状态（0：新节目；1：待审核；2：已发布；3：打回；4：删除（回收站）；）',
  `level` int(11) NOT NULL default '1' COMMENT '浏览等级',
  `groups` varchar(255) NOT NULL default 'all' COMMENT '可浏览的用户组',
  `is_download` tinyint(1) NOT NULL default '1' COMMENT '允许下载',
  `catalog_info` text NOT NULL COMMENT '编目信息',
  `upload_username` varchar(64) NOT NULL COMMENT '上传用户',
  `upload_at` int(11) NOT NULL COMMENT '上传时间',
  `catalog_username` varchar(64) default NULL COMMENT '编目用户',
  `catalog_at` int(11) default NULL COMMENT '编目时间',
  `putout_username` varchar(64) default NULL COMMENT '发布用户',
  `putout_at` int(11) default NULL COMMENT '发布时间',
  PRIMARY KEY  (`id`),
  KEY `category_id` (`category_id`),
  KEY `type` (`type`),
  KEY `title` (`title`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='文件表';

/*Data for the table `ow_files` */

insert  into `ow_files`(`id`,`category_id`,`category_name`,`type`,`title`,`name`,`ext`,`size`,`path`,`status`,`level`,`groups`,`is_download`,`catalog_info`,`upload_username`,`upload_at`,`catalog_username`,`catalog_at`,`putout_username`,`putout_at`) values (1,2,'央视教育',3,'youling.gif','4c069df8712426ca80e8d5141ad5ddc0','gif',11694,'data1\\3948fa6f16db63ce69cd2114bfbd93b1\\',2,1,'all',1,'{\"地方放的\":{\"断点\":\"\",\"搜索\":\"\"},\"啊啊是\":{\"撒\":\"\",\"公告\":\"\",\"嗯嗯\":\"\"},\"版本\":{\"高高挂\":\"\",\"呵呵\":\"\"}}','admin',1256109496,'admin',1281084383,'admin',1281084393),(2,2,'央视教育',3,'哈哈.GIF','2dca03711da23e80c454c64ee8b0939b','GIF',21732,'data1\\970737dd84d20270a644f64ed74d7074\\',2,1,'all',1,'','admin',1256109496,'admin',1280817770,'admin',1280820264),(3,3,'太太团',3,'loading1.gif','e698425fe815a76a7df6f66f48a86d20','gif',3413,'data1\\9c59fc5873ec95b4a7f839a2628f1a6e\\',0,1,'all',1,'','admin',1256109496,NULL,NULL,NULL,NULL),(4,3,'太太团',4,'lb.html','f5f1cbf7f2bf7d6b69a5c3316121a703','html',2389,'data1\\042e095b5512ffe3cadd51ce5246eacc\\',0,1,'all',1,'','admin',1256109496,NULL,NULL,NULL,NULL),(5,62,'太太团',4,'diy-page_6.zip','57e9cf4857e5395d45496ad8dbd79592','zip',841542,'data1\\a4903435a7e32debc225889ee4eea3de\\',0,1,'all',1,'','admin',1256109496,NULL,NULL,NULL,NULL),(6,62,'太太团',4,'gqzh09.zip','b586f25f1b197e2ec5287a18c85d0ff1','zip',52835,'data1\\f7225dbebfdf096084c9340c5bac4297\\',0,1,'all',1,'','admin',1256109496,NULL,NULL,NULL,NULL),(7,57,'用户',4,'jz.doc','15d6937f1e5d6031c7c193ebb45af6e1','doc',35840,'data1\\d6bb713834ddcff29e4a8d65b6bd8729\\',0,1,'all',1,'','admin',1256109496,NULL,NULL,NULL,NULL),(8,2,'网络学院',4,'1257660009249645-1257660009251279.doc','efbf9653630e8c247fc39948e4fd90e9','doc',691200,'data1\\dffaa4d7e36fc8407a12eec6ca90237b\\',0,1,'all',1,'','admin',1256109496,NULL,NULL,NULL,NULL),(9,2,'网络学院',4,'20091117025556高起专语文.doc','259eea78efb9d7b1076a779af0f86c36','doc',177664,'data1\\a5dc67d5e8f8331dc43336696db97d43\\',2,1,'all',1,'','admin',1256109496,NULL,NULL,'admin',1281086706),(10,2,'网络学院',4,'20091117025545高起专英语.doc','cc7f9a1b45847cb636d1779e843284a9','doc',301056,'data1\\5344388627ca039904d442710db710ed\\',2,1,'all',1,'','admin',1256109496,NULL,NULL,'admin',1281086470),(11,2,'网络学院',4,'20080529062725_4.doc','a2b7e76d32f7fe59ebb57ae27d2f4339','doc',107008,'data1\\a3aa164a92f07dacaba5c1c55d4b1cf9\\',2,1,'all',1,'','admin',1256109496,NULL,NULL,'admin',1281086896),(12,57,'用户',4,'20086619191.doc','84e2cff241344a528153fbf71202d583','doc',834048,'data1\\9960f24f60678a04ef8ca8eb7b74a8b0\\',0,1,'all',1,'','admin',1256109496,NULL,NULL,NULL,NULL),(15,2,'网络学院',4,'toad.ppt','b2bea8d3525787d41f62ea9b0f7c82d9','ppt',1742848,'data1\\1008daa3f12f3b2206bca9d3f097a083\\',1,1,'all',1,'','admin',1256109496,NULL,NULL,NULL,NULL),(16,9,'高高挂',2,'八只眼 - 掌声响起来.mp3','a7d7e952f6547f37a763c071d6e07dec','mp3',5028510,'data1\\3e7da1634afa7392a6e1d23e1c9fdfd9\\',2,1,'all',1,'','admin',1256109496,NULL,NULL,'admin',1281086358),(17,1,'资源分类',1,'巧立名目－牛群、李立山 巧立名目－牛群、李立山','c8106719bb49a9453cf9625f73188a7a','mp4',5015076,'data1\\a7ee84ae0b5e0b10143c147033019c4f\\',2,1,'all',0,'','admin',1256109496,'admin',1280733215,NULL,NULL),(18,1,'资源分类',1,'刘若英 - 光','44fcfacce58e2bfac8145435f42cbbf3','flv',16681878,'data1\\e97cb2e3707ead726b2703e994b63a23\\',2,1,'all',1,'','admin',1267757075,'admin',1280812416,'admin',1281086294),(20,3,'体育教学',1,'张惠妹 - 趁早','baf48f111ee2a918d8e7695ecb07cf76','flv',18679554,'data1\\2f9cb898ad6d1f580c40720e6452d87b\\',2,1,'all',1,'','admin',1267782562,'admin',1280813687,'admin',1281082419),(21,2,'网络学院',1,'by2 - PP别贴在椅上','3d074f6842b09d0aa618a36204d2ec3f','flv',12315534,'data1\\84bd2fdf773de86ba915a9de54e9a612\\',2,1,'all',1,'','admin',1267782869,'admin',1280805905,'admin',1280813694),(23,9,'高高挂',4,'数学','135a48d8581fb6b14ad98f700c384c86','doc',1233408,'data1\\9553185aac3325c62a2c2fece0619e91\\',2,1,',22,',1,'','admin',1267836425,NULL,NULL,'admin',1281086388),(24,9,'高高挂',4,'nyzhkffb-1','15fd85f6c852ad9c8718a416014b0554','doc',349696,'data1\\1e284e8cd9327f2319194bdde4ffc915\\',0,1,',33,',1,'','admin',1267836453,NULL,NULL,NULL,NULL),(25,3,'规范',1,'周杰伦-不能说的秘密','79553dc8390c3199e3f9b7e0820507f4','avi',58488108,'data1\\6ca873b16a77aa48178f83860e4e3375\\',2,1,',1,',1,'','admin',1267856424,'admin',1280805576,'admin',1281086218),(26,9,'高高挂',1,'文根英 - 夜来香','6e2b3d5fd597a1932f89f49dbc43a31f','asf',9010182,'data1\\9ecfe1a6d8621e682adcf3ac6fe454e1\\',2,3,',1,2,3,15,21,',1,'','admin',1267859738,'admin',1280805277,'admin',1280812358),(27,9,'高高挂',2,'蔡琴 - 你的眼神','8d028127914512eca146e7bf32c56067','mp3',4012373,'data1\\522812a1a1de772781641c97e3626546\\',0,1,'all',1,'','admin',1267859748,NULL,NULL,'admin',1280732153),(28,9,'高高挂',4,'韩雪 - 想起','ec055ace7b8a89026662b0d30b264e06','lrc',796,'data1\\44b1d7a3aee205eccde6dddda6a58429\\',0,1,'all',1,'','admin',1267859751,NULL,NULL,NULL,NULL),(31,9,'高高挂',4,'BY2 - 我知道','5bf2b42ae7204aedc75994aae7e62c18','lrc',1454,'data1\\9abb078abfb2b35221de935119d211cf\\',0,1,'all',1,'','yc75',1269063910,NULL,NULL,NULL,NULL),(32,2,'网络学院',4,'陈琳-爱就爱了','94ea77b5191581213787c26c13004351','lrc',948,'data1\\b8c0821fdcc4779367d1a680fe4fceb2\\',0,1,'all',1,'','yc75',1269064002,NULL,NULL,NULL,NULL),(33,9,'高高挂',4,'八只眼 - 掌声响起来','2c031d8020bebb6d5cdfa060ff73a426','lrc',882,'data1\\9c7a8ce7e1127ea192b8bb54402dfb15\\',0,1,'all',1,'','admin',1269321862,NULL,NULL,NULL,NULL),(34,9,'山东卫视',2,'东来东往 - 不开心与没烦恼','5dec7f34ec01574d0c9fc35366cb84ae','mp3',5521441,'data1\\4f92c1fa114d1ab54d80615c635031f0\\',2,4,'all',1,'','admin',1280732302,'admin',1280817762,'admin',1281083784),(35,9,'高高挂',2,'BY2 - 我知道','7ec8c4f55d25e70a1879ecd99fdb4e51','mp3',9997525,'data1\\79fc4b0e8cc830f880d6b4086c8ad31e\\',0,1,'all',1,'','admin',1280732887,'admin',1280732925,NULL,NULL),(36,9,'高高挂',4,'蔡依林 - 惯性背叛','3c3acf3f829fe9040b7dcd903a50cb87','lrc',1213,'data1\\d8ef480e78d23fdec1328e8bbf8eb3af\\',0,1,'all',1,'','admin',1280733259,'admin',1280733275,NULL,NULL),(37,9,'山东卫视',3,'我们','86dc0277168c14656e7c2d0d22787191','jpg',21554,'data1\\7d0d573df4035dccd93bef8919eabd0f\\',2,1,'all',1,'','admin',1280819853,'admin',1280819896,'admin',1281081083),(38,9,'山东卫视',4,'3D网络虚拟现实平台的行业应用','97f2db815d8b146fcadb965872c14de7','txt',2316,'data1\\d351993661967206d1e101511130943a\\',0,1,'all',1,'','admin',1280820820,NULL,NULL,NULL,NULL),(39,9,'山东卫视',2,'陈琳-爱就爱了','3f9a46d5cb4f4740629fcf2e6976a8be','mp3',4002087,'data1\\9bc623fcf69ee24e991b3914a678e74e\\',0,1,'all',1,'','admin',1280821288,NULL,NULL,NULL,NULL),(40,93,'齐鲁频道',2,'陈奕迅 - 明年今日','d105f2d542682ea57e75e71308a7d0cf','mp3',4948223,'data1\\a1c743ce6378aad0eaf67d03266dc818\\',0,1,'all',1,'','admin',1280821892,NULL,NULL,NULL,NULL),(41,93,'齐鲁频道',2,'成 龙 - 壮志在我胸','bd45a0def5321e5f0439064ef4e27f28','mp3',4444365,'data1\\4f1b380d72355abbb2b4bfa693a51f1d\\',0,1,'all',1,'','admin',1280822130,NULL,NULL,NULL,NULL),(42,93,'齐鲁频道',2,'朋友','c9991d62a78f6cefa28eb0fe72005516','wav',15854876,'data1\\c2ee752bfb3016c33d28c04b8bf842b1\\',0,1,'all',1,'','admin',1280822423,NULL,NULL,NULL,NULL),(43,9,'山东卫视',4,'成 龙 - 壮志在我胸','6e98da07267dacd6ccec515216c537c9','lrc',1043,'data1\\050e5c969130330b216ad24083c7ee38\\',2,1,'all',1,'{\"爱爱爱\":{\"11\":\"找找看\",\"分割\":\"\",\"过后\":\"\"},\"fgfdg\":{\"ddd\":\"\"}}','admin',1280826169,'admin',1281086956,'admin',1281086981),(44,9,'山东卫视',4,'蔡琴 - 你的眼神','f3c19de6b6f6ba196494174865ccf77a','lrc',872,'data1\\75309ffb43a347b83ec0b88e0e2cac57\\',0,1,'all',1,'','admin',1280826263,NULL,NULL,NULL,NULL),(45,9,'山东卫视',4,'蔡琴 - 如梦令','70e36fb6cd90dd4cc9cbe697ccbd101c','lrc',523,'data1\\5a27a69bb756be542714f344782f97e0\\',0,1,'all',1,'','admin',1280826273,NULL,NULL,NULL,NULL),(46,9,'山东卫视',2,'范玮琪 - 是非题','8318bc6bd6e901de14a9a2358dfb473b','mp3',3717712,'data1\\c4c6f29e23fd932dd321a490e3969dd4\\',0,1,'all',1,'','admin',1280826294,NULL,NULL,NULL,NULL),(47,9,'山东卫视',2,'蔡琴 - 驿动的心','53e88597f602e4a29144e34c4f9f12d7','mp3',3250851,'data1\\5723c2f178a3874f35163721e90e1043\\',0,1,'all',1,'','admin',1280826307,NULL,NULL,NULL,NULL),(48,9,'山东卫视',4,'蔡琴 - 驿动的心','e9c12ef6b6d93eb1ea570f52d71daec3','lrc',751,'data1\\620aa7d2670fcab7793fa4d700c2e185\\',2,1,'all',1,'{\"爱爱爱\":{\"11\":\"\",\"分割\":\"\",\"过后\":\"\"},\"fgfdg\":{\"ddd\":\"\"}}','admin',1280826318,'admin',1281084703,'admin',1281084726),(49,9,'山东卫视',2,'蔡依林 - 惯性背叛','d6923b80e7681f29cc0cc5b56e91e029','mp3',4335116,'data1\\3f4570e4c5c7d37062694ae74492710a\\',0,1,'all',1,'','admin',1280826319,NULL,NULL,NULL,NULL),(50,9,'山东卫视',4,'彩虹的微笑 王心凌','8399be67324143497630d5712b1ffa83','lrc',1253,'data1\\c05216caf3c9758b24e05614f039c331\\',2,1,'all',1,'{\"爱爱爱\":{\"11\":\"\",\"分割\":\"\",\"过后\":\"\"},\"fgfdg\":{\"ddd\":\"\"}}','admin',1280826405,'admin',1281084558,'admin',1281084567),(51,9,'山东卫视',2,'彩虹的微笑 王心凌','ecd3ca93ffc062778ff55489aec04a9e','mp3',2689280,'data1\\40dd913547d6bf6ebaaab2b670673b9b\\',0,1,'all',1,'','admin',1280826411,NULL,NULL,NULL,NULL),(52,9,'山东卫视',2,'白头到老 金木','60de0bc39d41ba9d7c015de444c8c161','mp3',5459474,'data1\\27829ba1242dd85a19d0521ff3f0023f\\',2,1,'all',1,'','admin',1280826468,'admin',1281075825,'admin',1281075838),(53,9,'山东卫视',2,'Vae 许嵩 - 浅唱','c53880655d639212681b4fac665d55c2','mp3',3958534,'data1\\f7c612974f122fb956b2cd8f3d610f12\\',2,1,'all',1,'{\"基本信息\":{\"地方\":\"\",\"标题\":\"\"},\"sdfasd\":{\"cce\":\"\"}}','admin',1280826594,'admin',1281087236,'admin',1281087244),(54,9,'山东卫视',2,'Ievan Polkka','3db0e2ebd433322e6514f960bb4c21ca','MP3',5943168,'data1\\5e555ead157c66ca25aee23398d67384\\',2,1,'all',1,'{\"基本信息\":{\"地方\":\"\",\"标题\":\"\"},\"sdfasd\":{\"cce\":\"\"}}','admin',1280826633,'admin',1281084353,'admin',1281084361),(55,9,'山东卫视',2,'Gullia_Oops_Jaime_Pas_Langlais','585bd3e8b7fdc27aeff7eb6b2aab13a8','MP3',1413537,'data1\\25b4de2c7da8053bf209529a64547157\\',2,1,'all',1,'{\"基本信息\":{\"地方\":\"\",\"标题\":\"\"},\"sdfasd\":{\"cce\":\"\"}}','admin',1280826660,'admin',1281084242,'admin',1281084250),(56,93,'齐鲁频道',4,'费玉清 周杰伦 - 千里之外','626e58f7eaef22d4d636e37071965582','lrc',1593,'data1\\c596e70a1f9a715a472b0505e5fd31b0\\',2,1,'all',1,'','admin',1281002966,'admin',1281002989,'admin',1281002999),(57,9,'山东卫视',2,'好一朵美丽的茉莉花','520b4a3b6cfbd8f2e1c6050ce68e263b','wav',13611068,'data1\\3376c4436f1b028c0232bc3913cfa6b8\\',2,1,'all',1,'','admin',1281003112,'admin',1281003139,'admin',1281003147),(58,93,'齐鲁频道',2,'刘德华 - 真永远','3c1ac62d4c9b3b3ca3da1c6598b1e0a3','mp3',4050407,'data1\\93a0d13e8927ea6ab1461e5b47e3930a\\',2,1,'all',1,'','admin',1281003201,'admin',1281003228,'admin',1281003237),(59,93,'齐鲁频道',2,'電影原聲帶 - 湘伦小雨四手联弹','0d1cca01be6ba71416ffaee5b2b534db','mp3',913299,'data1\\a3f1c2752e20d22d1251c3fadc79fc39\\',2,1,'all',1,'','admin',1281072842,'admin',1281072865,'admin',1281072877),(60,93,'齐鲁频道',4,'范玮琪 - 最初的梦想','311ddce16b5eaf6874eaa20d54799d62','lrc',1703,'data1\\eac21c071be101e253498482a6112d97\\',2,1,'all',1,'','admin',1281074595,'admin',1281074606,'admin',1281074614),(61,9,'山东卫视',2,'刘若英 - 光','8c78d82ddd9666b72c281d51462ae905','mp3',3809741,'data1\\b436ab050d3644a480d930d43d58a926\\',2,1,'all',1,'','admin',1281075689,'admin',1281075717,'admin',1281075738),(62,93,'齐鲁频道',4,'何洁 黄雅莉 - 花儿开了','f485cdcea1dbd05428fd1d2c16d81cfd','lrc',1271,'data1\\a339f74a07d32c3a5cc1822a504c406c\\',2,1,'all',1,'{\"爱爱爱\":{\"11\":\"ffffffff\",\"分割\":\"ffffffffffffffffffffffff\",\"过后\":\"fffffffffffffff\"},\"fgfdg\":{\"ddd\":\"fffffffffff\"}}','admin',1281077711,'admin',1281077742,'admin',1281077756),(63,9,'山东卫视',4,'荷塘月色','15835749f789473d8a62b4b784f639d1','lrc',1487,'data1\\2e2ca526fc76feb6a5056c2170a25f72\\',2,1,'all',1,'{\"爱爱爱\":{\"11\":\"\",\"分割\":\"\",\"过后\":\"\"},\"fgfdg\":{\"ddd\":\"\"}}','admin',1281081005,'admin',1281081015,'admin',1281081025),(64,93,'齐鲁频道',4,'思乡曲','23e7e54713110a79599dacfbe76a8c8c','lrc',699,'data1\\56b16d8aa8290188576b42ae7e3eb3f9\\',2,1,'all',1,'{\"爱爱爱\":{\"11\":\"原因有隐隐约约原因有\",\"分割\":\"周杰\",\"过后\":\"\"},\"fgfdg\":{\"ddd\":\"光芒\"}}','admin',1281086735,'admin',1281086761,'admin',1281086770);

/*Table structure for table `ow_files_counter` */

DROP TABLE IF EXISTS `ow_files_counter`;

CREATE TABLE `ow_files_counter` (
  `id` int(11) NOT NULL COMMENT '计数器编号',
  `file_id` int(11) NOT NULL COMMENT '文件编号',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件计数器，用于全文检索更新索引';

/*Data for the table `ow_files_counter` */

insert  into `ow_files_counter`(`id`,`file_id`) values (1,53);

/*Table structure for table `ow_groups` */

DROP TABLE IF EXISTS `ow_groups`;

CREATE TABLE `ow_groups` (
  `id` int(11) NOT NULL auto_increment COMMENT '编号',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `description` varchar(255) NOT NULL default '' COMMENT '描述',
  `quota` int(11) NOT NULL default '1000' COMMENT '用户磁盘配额（MB）',
  `weight` int(11) NOT NULL default '0' COMMENT '权重',
  `enabled` tinyint(2) NOT NULL default '1' COMMENT '可用性',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='用户组表';

/*Data for the table `ow_groups` */

insert  into `ow_groups`(`id`,`name`,`description`,`quota`,`weight`,`enabled`) values (1,'超级管理员','超级管理员',1000,99999,1),(2,'管理员','管理员',1000,99998,1),(3,'普通用户','普通用户',1000,99997,1),(12,'SSS','sdf',1000,0,0),(13,'df','dsf',1000,0,0),(14,'sdf','dsf',1000,0,0),(15,'fg','',1000,0,1),(16,'f','',1000,0,1),(17,'dfg','',1000,0,1),(18,'dfg','',1000,0,1),(19,'sdf','',1000,0,1),(20,'dsf','',1000,0,1),(21,'dsf','',1000,0,1),(22,'aaa','',1000,0,1),(23,'dddd','',1000,0,1),(24,'ccc','',1000,0,1);

/*Table structure for table `ow_groups_has_category` */

DROP TABLE IF EXISTS `ow_groups_has_category`;

CREATE TABLE `ow_groups_has_category` (
  `group_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY  (`group_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `ow_groups_has_category` */

insert  into `ow_groups_has_category`(`group_id`,`category_id`) values (2,1),(2,2),(2,5),(2,9),(2,57),(2,73),(2,81);

/*Table structure for table `ow_groups_has_roles` */

DROP TABLE IF EXISTS `ow_groups_has_roles`;

CREATE TABLE `ow_groups_has_roles` (
  `group_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY  (`group_id`,`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `ow_groups_has_roles` */

insert  into `ow_groups_has_roles`(`group_id`,`role_id`) values (2,2),(3,1),(3,2),(3,3),(3,4),(3,5),(3,6);

/*Table structure for table `ow_levels` */

DROP TABLE IF EXISTS `ow_levels`;

CREATE TABLE `ow_levels` (
  `id` int(11) NOT NULL auto_increment COMMENT '编号',
  `name` varchar(64) NOT NULL COMMENT '等级名称',
  `description` varchar(255) NOT NULL default '' COMMENT '描述',
  `weight` int(11) NOT NULL default '0' COMMENT '权重',
  `enabled` tinyint(2) NOT NULL default '1' COMMENT '可用性',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='阅读等级表';

/*Data for the table `ow_levels` */

insert  into `ow_levels`(`id`,`name`,`description`,`weight`,`enabled`) values (1,'公开','公开',1,1),(2,'私有','私有',2,0),(3,'秘密','秘密',3,1),(4,'机密','机密',4,1),(5,'绝密','绝密',5,1);

/*Table structure for table `ow_permissions` */

DROP TABLE IF EXISTS `ow_permissions`;

CREATE TABLE `ow_permissions` (
  `id` int(11) NOT NULL auto_increment COMMENT '编号',
  `namespace` varchar(64) NOT NULL default 'default' COMMENT '命名空间',
  `controller` varchar(64) NOT NULL default 'default' COMMENT '控制器',
  `action` varchar(64) NOT NULL default 'index' COMMENT '操作',
  `aliasname` varchar(64) NOT NULL default '' COMMENT '注释名',
  `rbac` varchar(32) NOT NULL default 'ALC_NULL' COMMENT '系统角色',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='操作权限表';

/*Data for the table `ow_permissions` */

insert  into `ow_permissions`(`id`,`namespace`,`controller`,`action`,`aliasname`,`rbac`) values (1,'admin','AclManager','Index','访问控制管理','ACL_NULL'),(2,'admin','AclManager','User','用户管理','ACL_NULL'),(3,'admin','AclManager','UserAdd','添加用户','ACL_NULL'),(4,'admin','AclManager','UserEdit','编辑用户','ACL_NULL'),(5,'admin','AclManager','Group','用户组管理','ACL_NULL'),(6,'admin','AclManager','GroupView','用户组详情','ACL_NULL'),(7,'admin','AclManager','GroupBind','用户组绑定权限','ACL_NULL'),(8,'admin','AclManager','Role','管理角色','ACL_NULL'),(9,'admin','AclManager','RoleView','角色详情','ACL_NULL'),(10,'admin','AclManager','RoleBind','角色绑定权限','ACL_NULL'),(11,'admin','AclManager','Permission','管理权限','ACL_NULL'),(12,'admin','AclManager','PermissionRefresh','更新权限列表','ACL_NULL'),(13,'admin','AclManager','MakeAclFile','更新权限文件','ACL_NULL'),(14,'admin','AclManager','Level','浏览等级管理','ACL_NULL'),(15,'admin','AclManager','LevelView','浏览等级详情','ACL_NULL'),(16,'admin','Default','Index','管理首页','ACL_EVERYONE'),(17,'admin','Default','Login','登录','ACL_NO_ROLE'),(18,'admin','Default','Logout','退出','ACL_HAS_ROLE'),(19,'admin','DictManager','Index','字典管理','ACL_NULL'),(20,'admin','DictManager','Category','资源库分类','ACL_NULL'),(21,'admin','DictManager','CategoryAdd','添加资源库分类','ACL_NULL'),(22,'admin','DictManager','CategoryEdit','编辑资源库分类','ACL_NULL'),(23,'admin','DictManager','CategoryDel','删除资源库分类','ACL_NULL'),(24,'admin','DictManager','Catalog','文件编目信息','ACL_NULL'),(25,'admin','DictManager','CatalogAdd','添加文件编目信息','ACL_NULL'),(26,'admin','DictManager','CatalogEdit','编辑文件编目信息','ACL_NULL'),(27,'admin','DictManager','CatalogDel','删除文件编目信息','ACL_NULL'),(28,'admin','FileCatalog','Index','素材管理','ACL_NULL'),(29,'admin','FilePutOut','Index','审核发布','ACL_NULL'),(30,'admin','FileSearch','Index','检索下载','ACL_NULL'),(31,'admin','FileUpload','Index','素材上载','ACL_NULL'),(32,'admin','UserCenter','Index','个人中心','ACL_HAS_ROLE'),(33,'admin','UserCenter','ChangeInfo','个人信息','ACL_HAS_ROLE'),(34,'admin','UserCenter','ChangePassword','修改密码','ACL_HAS_ROLE'),(35,'default','Default','Index','首页','ACL_EVERYONE'),(37,'admin','AclManager','UserDel','删除用户','ACL_NULL'),(38,'admin','FileCatalog','Video','视频编目','ACL_NULL'),(39,'admin','FileCatalog','Audio','音频编目','ACL_NULL'),(40,'admin','FileCatalog','Image','图片编目','ACL_NULL'),(41,'admin','FileCatalog','Rich','富媒体编目','ACL_NULL');

/*Table structure for table `ow_roles` */

DROP TABLE IF EXISTS `ow_roles`;

CREATE TABLE `ow_roles` (
  `id` int(11) NOT NULL auto_increment COMMENT '编号',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `description` varchar(255) NOT NULL default '' COMMENT '描述',
  `weight` int(11) NOT NULL default '0' COMMENT '权重',
  `enabled` tinyint(2) NOT NULL default '1' COMMENT '可用性',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='角色表';

/*Data for the table `ow_roles` */

insert  into `ow_roles`(`id`,`name`,`description`,`weight`,`enabled`) values (1,'ADMIN','超级管理员',99999,1),(2,'SYSTEM','系统管理员',99998,1),(3,'NORMAL','正常用户',99997,1),(4,'FREEZE','冻结用户',99996,0),(5,'REPEAL','废除用户',99995,0),(6,'UNCHECKED','待审核用户',99994,0);

/*Table structure for table `ow_roles_has_permissions` */

DROP TABLE IF EXISTS `ow_roles_has_permissions`;

CREATE TABLE `ow_roles_has_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY  (`role_id`,`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `ow_roles_has_permissions` */

insert  into `ow_roles_has_permissions`(`role_id`,`permission_id`) values (2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,10),(2,11),(2,12),(2,13),(2,14),(2,15),(2,16),(2,17),(2,18),(2,19),(2,20),(2,21),(2,22),(2,23),(2,24),(2,25),(2,26),(2,27),(2,28),(2,29),(2,30),(2,31),(2,32),(2,33),(2,34),(2,35),(2,37),(2,38),(2,39),(2,40),(2,41),(3,3);

/*Table structure for table `ow_users` */

DROP TABLE IF EXISTS `ow_users`;

CREATE TABLE `ow_users` (
  `id` int(11) NOT NULL auto_increment COMMENT '编号',
  `group_id` int(11) NOT NULL COMMENT '用户组编号',
  `level_id` int(11) NOT NULL COMMENT '阅读等级编号',
  `username` varchar(32) NOT NULL COMMENT '用户名',
  `password` varchar(64) NOT NULL COMMENT '密码',
  `nickname` varchar(64) NOT NULL COMMENT '昵称',
  `sex` tinyint(2) NOT NULL default '0' COMMENT '性别（0：保密；1：男；2：女）',
  `birthday` varchar(64) default NULL COMMENT '生日',
  `address` varchar(255) default NULL COMMENT '地址',
  `email` varchar(64) default NULL COMMENT '电子邮箱',
  `duty` varchar(64) default NULL COMMENT '职务',
  `office_phone` varchar(64) default NULL COMMENT '办公电话',
  `home_phone` varchar(64) default NULL COMMENT '家庭电话',
  `mobile_phone` varchar(64) default NULL COMMENT '手机',
  `description` varchar(255) default NULL COMMENT '个人简介',
  `enabled` tinyint(2) NOT NULL default '1' COMMENT '可用性',
  `register_at` int(11) NOT NULL default '0' COMMENT '注册时间',
  `register_ip` char(15) NOT NULL default '0.0.0.0' COMMENT '注册IP',
  `login_count` int(11) NOT NULL default '0' COMMENT '登录次数',
  `login_at` int(11) NOT NULL default '0' COMMENT '登录时间',
  `login_ip` char(15) NOT NULL default '0.0.0.0' COMMENT '登录IP',
  PRIMARY KEY  (`id`),
  KEY `group_id` (`group_id`),
  KEY `username` (`username`),
  KEY `password` (`password`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='用户表';

/*Data for the table `ow_users` */

insert  into `ow_users`(`id`,`group_id`,`level_id`,`username`,`password`,`nickname`,`sex`,`birthday`,`address`,`email`,`duty`,`office_phone`,`home_phone`,`mobile_phone`,`description`,`enabled`,`register_at`,`register_ip`,`login_count`,`login_at`,`login_ip`) values (1,1,5,'admin','$1$kI0.dK0.$mZfeLOhcTZ.xHq5uw8fk3.','宇琛',0,'7.5','jinan','thinkgem@gmail.com','zhiyuan','88888888','88888888','15054197120','caizhiguorenjingmnenggan',1,1256952622,'192.168.1.13',111,1280816355,'127.0.0.1'),(2,2,5,'yc75','$1$EG1.745.$z6/JA8BuucealnqG4Dmip.','yc',0,'','','','','','','','',1,1256952707,'192.168.1.13',24,1269056178,'192.168.1.13'),(4,2,4,'aaaa','$1$9D5.cS3.$BXgvoR25HEH9Ml11DIHAN.','aaaa',0,'','','','','','','','',1,1256954460,'192.168.1.13',0,0,'0.0.0.0'),(5,3,1,'bbbb','$1$/91.ad/.$ylPmFvOvdYyxB0Gqr57560','bbbb',0,'','','','','','','','',1,1256954486,'192.168.1.13',0,0,'0.0.0.0'),(6,3,1,'eeeee','$1$TP0.gw0.$ig/02d79vMQQaCd2YMWTG/','eeeee',0,'','','','','','','','',1,1257126591,'192.168.1.13',0,0,'0.0.0.0');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
