<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('title'); ?> - 欢迎<?php $this->_endblock(); ?>

<?php $this->_block('contents'); ?>

	<div id="header">
	  <h1>开始我的 OpenWan 之旅</h1>
	  <h2>驾驭 OpenWan 从这里开始！</h2>
	</div>

	<div id="getting-started">          
	  <h1>快速开始</h1>
	  <h2>&nbsp;</h2>
	  <ol>
		<li>
		  <h2>素材上载</h2>
		  <p>&nbsp;</p>
		</li>
                <li>
		  <h2>媒资编目</h2>
		  <p>&nbsp;</p>
		</li>
                <li>
		  <h2>审核发布</h2>
		  <p>&nbsp;</p>
		</li>
                <li>
		  <h2>检索下载</h2>
		  <p>&nbsp;</p>
		</li>
                <li>
		  <h2>访问控制</h2>
		  <p>对整个系统的访问控制列表设置，包括：用户管理、用户组管理、角色管理、权限管理、浏览等级管理</p>
		</li>
                <li>
		  <h2>字典管理</h2>
                  <p>管理分类表，包括：资源库分类表、编目信息表。<strong>资源库分类表：</strong>管理资源库分类信息，具有无限级分类结构，有添加、修改、删除、排序等功能
                      <strong>编目信息表：</strong>管理媒资文件的编目信息分类表，包括：视频编目、音频编目、图片编目、富媒体编目，如：标题、类型、时长、来源、等信息。</p>
		</li>
	  </ol>
          <p class="important">
             OpenWan
          </p>
	</div>

<?php $this->_endblock(); ?>
