TOPDIR ?= $(realpath ..)

include $(TOPDIR)/Config.mk

SUBDIRS  = css
SUBDIRS += classes
SUBDIRS += img
SUBDIRS += include
SUBDIRS += js
SUBDIRS += templates

SRCS  = index.php
SRCS += configurations.php

install:
	@echo "\\033[1;35m+++ Installing www\\033[39;0m"
	@mkdir -p $(WWW_DIR)
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done
	@for i in $(SRCS); do \
		install $$i $(WWW_DIR) ; \
		perl -i -pne 's/%(\w+?)%/$$ENV{$$1}/ge' $(WWW_DIR)/$$i ; \
	done

clean:
	@echo "\\033[1;35m+++ Cleaning www\\033[39;0m"
	@for i in $(SUBDIRS) ; do \
		$(MAKE) -C $$i clean; \
	done
