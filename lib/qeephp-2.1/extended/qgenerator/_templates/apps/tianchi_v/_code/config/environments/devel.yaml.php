# <?php die(); ?>

#############################
# 数据库元信息缓存设置
#############################

# 数据表元数据缓存时间（秒）
db_meta_lifetime:               10

# 指示是否缓存数据表的元数据
db_meta_cached:                 true


#############################
# 日志设置
#############################

# 指示用什么目录保存日志文件
log_writer_dir:                 %ROOT_DIR%/_tmp/log

# 指示用什么文件名保存日志
log_writer_filename:            devel.log

# 指示记录哪些优先级的日志（不符合条件的会直接过滤）
log_priorities:                 EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG

# 指示是否显示错误信息
error_display:                  true

# 指示是否显示友好的错误信息
error_display_friendly:         true

# 指示是否在错误信息中显示出错位置的源代码
error_display_source:           true

