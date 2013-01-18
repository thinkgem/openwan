# <?php die(); ?>

## 注意：书写时，缩进不能使用 Tab，必须使用空格

#############################
# 路由设置
#############################

# 简单的路由规则
#
# 例如:
# http://www.example.com/posts/view/23
#
# 这个 url 映射到 posts 控制器的 view 方法，最后的 23 则映射为 id 参数。
#
# 路由规则为：
# posts_view:
#   pattern: /posts/view/:id
#   config:
#     id: [0-9]+
#   defaults:
#     controller: posts
#     action: view
#



# 基于正则的路由规则
#
# 例如:
# http://www.example.com/contents-live-12423.html
#
# 这个 url 映射到 cms 模块的 contents 控制器的 view 方法。
# live 映射为 category 参数，而 12423 映射为 id 参数，最后的 html 映射为 format 参数。
#
# 路由规则为：
# contents:
#   regex: contents\-([a-z0-9]+)\-([0-9]+)(\.html)?
#   config:
#     $1: category
#     $2: id
#     $3: format
#   defaults:
#     module: cms
#     controller: contents
#     action: view
#     format: .html
#
# 路由规则使用了正则表达式，可以匹配任意形式的 url。
#


# 默认路由
#
# 当没有任何匹配的路由规则时，使用默认的路由规则。
#

_default_:
  pattern: /:controller/:action/*
  defaults:
    controller: default
    action: index

