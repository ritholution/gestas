TOPDIR ?= $(realpath ..)

include $(TOPDIR)/Config.mk

SUBDIRS = ssl

SRCS += apache2-vhosts.conf
SRCS += apache2.conf
SRCS += postgresql.conf
SRCS += my.cnf
SRCS += php.ini

install:
	@echo "\\033[1;35m+++ Installing configurations\\033[39;0m"
	@mkdir -p $(CONF_DIR)
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done
	@for i in $(SRCS); do \
		install $$i $(CONF_DIR) ; \
		perl -i -pne 's/%(\w+?)%/$$ENV{$$1}/ge' $(CONF_DIR)/$$i ; \
	done

clean:
	@echo "\\033[1;35m+++ Cleaning conf\\033[39;0m"
	@for i in ${SUBDIRS}; do \
		make -C $$i clean; \
	done
