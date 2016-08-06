TOPDIR ?= $(realpath .)

include $(TOPDIR)/Config.mk

SUBDIRS  = conf
SUBDIRS += scripts
SUBDIRS += www

.PHONY: rall all clean start-environment stop-environment db-start db-stop mysql-start mysql-stop postgresql-start postgresql-stop apache-start apache-stop

rall all:
	@make install
	@make start-environment

install:
	@mkdir -p $(BUILD_DIR)
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done

rw:
	@make -C www install

clean: stop-environment
	@echo "\\033[1;35m+++ Cleaning files and directories.\\033[39;0m"
	@for i in $(SUBDIRS) ; do $(MAKE) -C $$i clean; done
	@rm -rf $(LOG_DIR)
	@rm -fr $(BUILD_DIR)

$(BUILD_DIR): install

start-environment: apache-start db-start
	@mkdir -p $(LOG_DIR)
	@touch $(LOG_DIR)/$(LOG_FILE)

stop-environment: apache-stop db-stop

db-start:
	@echo "\\033[1;35m+++ Starting db\\033[39;0m"
	@make $(DB_ENGINE)-start

db-stop:
	@echo "\\033[1;35m+++ Stoping db\\033[39;0m"
	@make $(DB_ENGINE)-stop

mysql-start: $(BUILD_DIR)
	@echo -n "\\033[1;35m+++ Starting mysql\\033[39;0m "
	@if [ ! -f $(MYSQL_PID) ]; then \
		rm -rf $(MYSQL_DATA) > /dev/null; \
		$(BIN)/mysql_install_db --user=$(USER) --ldata=$(MYSQL_DATA) > /dev/null 2> /dev/null; \
		mkdir -p $(MYSQL_LOGDIR); \
		mkdir -p $(BUILD_DIR)/tmp; \
		$(SBIN)/mysqld --defaults-extra-file=$(MYSQL_CONF) -P $(MYSQL_PORT) -h $(MYSQL_DATA) --socket=$(MYSQL_SOCKET) --pid-file=$(MYSQL_PID) > /dev/null 2>&1 & \
		ps_alive=0; \
		for i in 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20; do \
                sleep 1; \
				if [ -f $(MYSQL_PID) ]; then pid=`cat $(MYSQL_PID)`; fi; \
				if [ -f $(MYSQL_PID) ] && `ps $${pid}` > /dev/null 2>&1; then ps_alive=1; break; fi; \
                echo -n "\\033[1;35m.\\033[39;0m"; \
        done; \
		if [ $${ps_alive} ]; then \
			$(BIN)/mysqladmin --protocol=TCP -P $(MYSQL_PORT) -h $(MYSQL_HOST) -u root create $(DATABASE) ; \
			$(BIN)/mysql --protocol=TCP -P $(MYSQL_PORT) -h $(MYSQL_HOST) -u root $(DATABASE) < $(MYSQL_SCHEMA) ; \
			for i in $(DB_DATA_FILES); do \
				$(BIN)/mysql  --protocol=TCP -P $(MYSQL_PORT) -h $(MYSQL_HOST) -u root $(DATABASE) < $$i ; \
			done; \
		elif [ ! $${ps_alive} ]; then \
			echo "\\033[1;35m+++ Failed to start mysql\\033[39;0m"; \
		fi; \
	fi;
	@echo;

mysql-stop:
	@echo "\\033[1;35m+++ Stopping mysql\\033[39;0m"
	@if [ -f $(MYSQL_PID) ]; then \
		kill -3 `cat $(MYSQL_PID)` 2>/dev/null; \
	fi

postgresql-start: $(BUILD_DIR)
	@echo "\\033[1;35m+++ Starting postgres\\033[39;0m"
	@if [ ! -f $(BUILD_DIR)/postmaster.pid ]; then \
		rm -rf $(PGSQL_DATA) > /dev/null; \
		mkdir -p $(PGSQL_LOGDIR); \
		$(PGSQL_BIN)/initdb --pgdata=$(PGSQL_DATA) --auth="ident" > /dev/null; \
		install conf/pg_hba.conf $(PGSQL_DATA); \
		$(PGSQL_BIN)/postgres -c config_file=${CONF_DIR}/postgresql.conf -k $(PGSQL_DATA) -D $(PGSQL_DATA) 1> $(PGSQL_LOG) < /dev/null 2>&1 & \
		echo $$! > $(BUILD_DIR)/postmaster.pid; \
		while ! $(BIN)/psql -h $(PGSQL_DATA) -p $(PGSQL_PORT) -c "select current_timestamp" template1 > /dev/null 2>&1; do \
			/bin/sleep 1; \
			echo -n "\\033[1;35m.\\033[39;0m"; \
		done; \
		$(BIN)/createdb -h $(PGSQL_DATA) -p $(PGSQL_PORT) $(DATABASE); \
		$(BIN)/psql -q -h $(PGSQL_DATA) -p $(PGSQL_PORT) $(DATABASE) -f $(PGSQL_SCHEMA) > /dev/null 2>&1; \
		for i in $(DB_DATA_FILES); do \
			$(BIN)/psql -q -h $(PGSQL_DATA) -p $(PGSQL_PORT) $(DATABASE) -f $$i > /dev/null 2>&1; \
		done; \
	fi

postgresql-stop:
	@if [ -f $(BUILD_DIR)/postmaster.pid ]; then \
		echo -n "\\033[1;35m+++ Stopping postgres\\033[39;0m "; \
		while kill -INT `cat $(BUILD_DIR)/postmaster.pid` 2>/dev/null; do echo -n "\\033[1;35m.\\033[39;0m "; sleep 1; done; echo; \
	fi

apache-start: $(BUILD_DIR)
	@echo "\\033[1;35m+++ Starting HTTP daemon\\033[39;0m"
	@if [ ! -f $(HTTPD_PIDFILE) ]; then \
		if [ ! -d $(HTTPD_LOGDIR) ] ; then mkdir -p $(HTTPD_LOGDIR) ; fi; \
		$(HTTPD) -f $(HTTPD_CONFIG); \
	fi

apache-stop:
	@echo "\\033[1;35m+++ Stopping HTTP daemon\\033[39;0m"
	@if [ -f $(RUN_DIR)/apache2.pid ]; then \
		$(HTTPD) -f $(CONF_DIR)/apache2.conf -k stop; \
	fi

help:
	@echo "\033[1;35mmake all\\033[39;0m - build, install and bring up environment."
	@echo "\033[1;35mmake clean\\033[39;0m - bring down and remove environment."
	@echo "\033[1;35mmake install\\033[39;0m - install environment."
	@echo "\033[1;35mmake start-environment\\033[39;0m - bring up environment."
	@echo "\033[1;35mmake stop-environment\\033[39;0m - bring down environment."
	@echo "\033[1;35mmake db-start\\033[39;0m - bring up db servers."
	@echo "\033[1;35mmake db-stop\\033[39;0m - bring down db servers."
	@echo "\033[1;35mmake mysql-start\\033[39;0m - bring up mysql server."
	@echo "\033[1;35mmake mysql-stop\\033[39;0m - bring down mysql server."
	@echo "\033[1;35mmake postgresql-start\\033[39;0m - bring up postgresql server."
	@echo "\033[1;35mmake postgresql-stop\\033[39;0m - bring down postgresql server."
	@echo "\033[1;35mmake apache-start\\033[39;0m - bring up apache server."
	@echo "\033[1;35mmake apache-stop\\033[39;0m - bring down apache server."
#	@echo "\033[1;35mmake doc\\033[39;0m - generate the phpdoc documentation."
#	@echo "\033[1;35mmake tests\\033[39;0m - run the tests."
#	@echo "\033[1;35mmake selenium-start\\033[39;0m - bring up selenium daemon."
#	@echo "\033[1;35mmake selenium-stop\\033[39;0m - bring down selenium daemon."
#	@echo "\033[1;35mmake phantom-start\\033[39;0m - bring up phantomjs daemon."
#	@echo "\033[1;35mmake phantom-stop\\033[39;0m - bring down phantomjs daemon."

info:
	@echo "To connect to postgresql database: \033[1;35mpsql -h $(PGSQL_DATA) $(DATABASE)\\033[39;0m"
	@echo "To connect to mysql database (tcp): \033[1;35mmysql --protocol=TCP -P $(MYSQL_PORT) -u root $(DATABASE)\\033[39;0m"
	@echo "To connect to mysql database (socket): \033[1;35mmysql --socket=$(MYSQL_SOCKET) -u root $(DATABASE)\\033[39;0m"
	@echo "Development environment: \033[1;35mhttp://$(HOST):$(HTTP_PORT)\\033[39;0m"
	@echo "Development environment SSL: \033[1;35mhttps://$(HOST):$(HTTPS_PORT)\\033[39;0m"

#doc:
#	@mkdir -p $(DOC_DIR)/phpdoc/log
#	@phpdoc

# Tests
#selenium-start:
#	@$(MAKE) -C $(TOPDIR)/tests selenium-start

#selenium-stop:
#	@$(MAKE) -C $(TOPDIR)/tests selenium-stop

#phantom-start:
#	@$(MAKE) -C $(TOPDIR)/tests phantom-start

#phantom-stop:
#	@$(MAKE) -C $(TOPDIR)/tests phantom-stop

tests: install start-environment
	@echo "\\033[1;35m+++ Running tests\\033[39;0m"
#	@$(MAKE) -C $(TOPDIR)/tests tests
#	phpdbg -qrr phpunit
