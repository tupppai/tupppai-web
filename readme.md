## App PHP projects 

## 切换中国composer镜像
http://pkg.phpcomposer.com/#tip2

## 读写分离优化，多数据库优化
http://fideloper.com/laravel-multiple-database-connections

## 依赖注入的解释
http://yuansir-web.com/2014/03/20/理解php-依赖注入laravel-ioc容器/
https://phphub.org/topics/789

## laravel 的功能介绍
http://zhuzhichao.com/post/2015/03/laravel-5-and-4-difference/

## 反射机制
http://php.net/manual/zh/book.reflection.php

## 代码峰哥
https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md

## 扩展3方库
http://overtrue.me/

## 多语言扩展
https://github.com/overtrue/laravel-lang

## nginx配置
https://gist.github.com/davzie/3938080

## lumen异常中断是不会写cookie的 #_# 坑

## lumen 将library注册为service
1. composer.json 中添加 library
2. Providers 中添加service
3. Facades 中添加service
4. bootstrap注册 直接use 使用


## Windows下安装phpunit

下载phpunit.phar, 复制到wamp的php。exe所在目录，执行php phpunit.phar即可。


## 使用Supervisor管理队列
http://yansu.org/2014/03/22/managing-your-larrvel-queue-by-supervisor.html

## 使用laravel-collection for lumen
http://laravelcollective.com/docs/5.1/html
https://github.com/vluzrmos/collective-html

## 使用elasticsearch 来做全局搜索索引
http://es.xiaoleilu.com/index.html
https://www.elastic.co/products/marvel

## 深度学习的书籍
http://www.iro.umontreal.ca/~bengioy/dlbook/


## 在laravel上使用elastic search
https://jellybool.com/post/setup-slasticsearch-on-your-website
http://blog.csdn.net/geloin/article/details/8933825

## 图谱科技，智能图片检索审核
https://open.tuputech.com/api/apiReview

## im 的便捷后台
http://docs.arrownock.com/

## 后台分页后续修改为：
https://github.com/lyonlai/bootstrap-paginator

## CI等框架如何获得Laravel的session
http://segmentfault.com/q/1010000003776645
http://www.thinksaas.cn/group/topic/395919/

# 用envoy进行部署
https://phphub.org/topics/24

# 如果mac 下 vim挂掉啦
DYLD_FORCE_FLAT_NAMESPACE=1 vim
http://blog.tbai.me/2015/04/%E8%A7%A3%E5%86%B3mac%E4%B8%8Bvim-caught-deadly-signal-abrt%E9%97%AE%E9%A2%98/

## 使用gitflow
http://nvie.com/posts/a-successful-git-branching-model/
https://www.sourcetreeapp.com/
http://danielkummer.github.io/git-flow-cheatsheet/index.zh_CN.html
测试gitflow 修改分支
测试终端gitflow

## 生产环境使用supervisor来进行damon监控

## redis用于计数器，排行榜等
http://neoremind.net/2012/05/redis典型应用场景/
## redis cache
http://laravel.com/docs/5.1/cache
提高命中绿:https://ruby-china.org/topics/22762
## redis 响应延迟排查
http://www.oschina.net/translate/redis-latency-problems-troubleshooting?lang=chs

## 发布流程？
git clone https://git.oschina.net/whenjonny/ps.git ps_bak
mv ps ps_bak
mv ps_release ps
cp ps_bak/vendor/* ps/vendor
cp ps_bak/.env ps/

## gulp安装部署

    1. 首先用淘宝的npm源咯
        http://npm.taobao.org/   
        sudo  npm install -g cnpm --registry=https://registry.npm.taobao.org
    2. 到publicsrc目录里面 
        cd public/src
        cnpm install

## 装了vpn
http://my.oschina.net/isnail/blog/363151

## 使用behat进行自动化测试
http://docs.behat.org/en/v2.5/quick_intro.html
