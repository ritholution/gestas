TOPDIR ?= $(realpath ../../..)

include $(TOPDIR)/Config.mk

SUBDIRS =
SRCS = module.php

install:
	@echo "\\033[1;35m+++ Installing module\\033[39;0m"
	@mkdir -p $(WWW_DIR)/classes/action_mgmt.mod
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done
	@for i in $(SRCS); do \
		install $$i $(WWW_DIR)/classes/action_mgmt.mod ; \
		perl -i -pne 's/%(\w+?)%/$$ENV{$$1}/ge' $(WWW_DIR)/classes/action_mgmt.mod/$$i ; \
	done

clean:
	@echo "\\033[1;35m+++ Cleaning module\\033[39;0m"
	@for i in $(SUBDIRS) ; do $(MAKE) -C $$i clean ; done
