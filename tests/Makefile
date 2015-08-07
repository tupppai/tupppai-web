# Top level makefile, the real shit is at src/Makefile
DB_USER := root
DB_PWD	:= XiaoNongNv52
DB_NAME := psgod
TESTDB	:= psgod_test
TESTLOGDB	:= psgod_log_test
LOG_DB_NAME := psgod_log
DB_CONNECT :=-hlocalhost -P3306 -u$(DB_USER) -p$(DB_PWD) $(DB_NAME)
LOG_DB_CONNECT :=-hlocalhost -P3306 -u$(DB_USER) -p$(DB_PWD) $(LOG_DB_NAME)

default: 
	rm -rf data
	mkdir data
	mysqldump --opt -d $(DB_NAME) -u$(DB_USER) -p$(DB_PWD) > ./data/$(DB_NAME).sql
	mysqldump --opt -d $(LOG_DB_NAME) -u$(DB_USER) -p$(DB_PWD) > ./data/$(LOG_DB_NAME).sql
	echo "drop database IF EXISTS $(TESTDB); create database $(TESTDB); use $(TESTDB);  source ./data/$(DB_NAME).sql;" | mysql $(DB_CONNECT)
	echo "drop database IF EXISTS $(TESTLOGDB); create database $(TESTLOGDB); use $(TESTLOGDB);  source ./data/$(LOG_DB_NAME).sql;" | mysql $(LOG_DB_CONNECT)
	phpunit
