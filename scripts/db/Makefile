TOPDIR ?= $(realpath ../..)

include $(TOPDIR)/Config.mk

SUBDIRS =

SRCS  = schema-mysql.sql
SRCS += initialize-data.sql

install:
	@echo "\\033[1;35m+++ Installing sql\\033[39;0m"
	@mkdir -p $(DB_SCRIPTS_DIR)
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done
	@for i in ${SRCS}; do \
	    install $$i $(DB_SCRIPTS_DIR); \
		perl -i -pne 's/%(\w+?)%/$$ENV{$$1}/ge' $(DB_SCRIPTS_DIR)/$$i ; \
	done

clean:
	@echo "\\033[1;35m+++ Cleaning sql\\033[39;0m"
	@for i in ${SUBDIRS}; do \
		make -C $$i clean; \
	done
