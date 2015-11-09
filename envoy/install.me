1. 下载 git 	http://git-scm.com/download/mac
2. 配置 zsh		https://github.com/robbyrussell/oh-my-zsh  最左边的剪头可以改成自己的名字的哦 ：）
3. 安装 vagrant http://www.vagrantup.com/downloads.html
4. 安装 virtual box 	http://download.virtualbox.org/virtualbox/5.0.8/VirtualBox-5.0.8-103449-OSX.dmg
5. 安装 vimrc 	https://github.com/whenjonny/vimrc
6. 安装 homestead 	    vagrant box add laravel/homestead 
＝＝＝＝＝＝＝这个比较坑爹，有1G多慢死撸(回头搭个源＝＝＝＝＝＝＝＝

	a. 输入 vagrant
	b. cd ~/.vagrant.d/boxes
	c. cp ~/www/tools/boxes.zip .
	d. unzip boxes.zip
	e. mv boxes/* .
	f. rm -rf boxes*

7. 安装 composer 	sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/bin
	alias composer="sudo php /usr/bin/composer.phar"
8. 安装 Laravel		composer global require "laravel/homestead=~2.0"
9. 将~/.composer/vendor/bin 放入环境变量，懒得话可以直接: ~/.composer/vendor/bin/homestead 执行
	~/.composer/vendor/bin/homestead init
10. 下载代码		cd ~/www; git clone https://git.oschina.net/whenjonny/ps.git （公要死要自己搞一下
11. 配置公钥		ssh-keygen -t rsa -C "tupppai@homestead"
12. 配置homestead	vim ~/.homestead/Homestead.yaml
folders:
     - map: ~/www
       to: /home/vagrant/Code
       type: "nfs"

sites:
     - map: www.ps.com
       to: /home/vagrant/Code
       hhvm: true
     - map: api.ps.com
       to: /home/vagrant/Code
       hhvm: true
     - map: admin.ps.com
       to: /home/vagrant/Code
       hhvm: true

databases:
     - psgod
     - psgod_bbs
     - psgod_log
	
14. cd ~/www/; git clone https://github.com/laravel/homestead.git
15. cd ~/www/homestead; vagrant up
＝＝＝＝＝＝＝ 稍等片刻 ，马上就有好吃的＝＝＝＝＝＝＝
16. 登陆虚拟机: vagrant ssh
17. 虚拟机的数据库和环境都搭建好了,但是代码是git下来的
    cp .env.example .env
    vim .env
    :%s/root/homestead
    :%s/xxx/secret
    :%s/admin.loiter.me/admin.ps.com
    :%s/api.loiter.me/api.ps.com
    :wq
    cd ~/www/ps/vendor; composer install ## 这里可能会出现composer太多次需要token的情况，问sky

18. cd ~/Code/ps/database/backups
    mysql -uhomestead -psecret 
    show databases;
    use psgod; source psgod.sql;
    use psgod_bbs; source psgod_bbs.sql;
    use psgod_log; source psgod_log.sql;

19. 修改vhost
    sudo vim /etc/nginx/sites-enabled/www.ps.com
    location /bbs/ {
        try_files $uri $uri/ /bbs/index.php?$query_string;
    }
    sudo service nginx restart
    
19. exit 退出虚拟机
20. sudo vim /etc/hosts
    192.168.10.10 admin.ps.com 
    192.168.10.10 www.ps.com 
    192.168.10.10 ps.com 
    192.168.10.10 api.ps.com 
