TOPDIR?=$(realpath ../..)

include $(TOPDIR)/Config.mk

SUBDIRS =
SRCS = functions.php

install: $(SRCS)
	@echo "\\033[1;35m+++ Installing include files\\033[39;0m"
	@mkdir -p $(WWW_DIR)/include
	@for i in $(SUBDIRS) ; do \
		$(MAKE) -C $$i install; \
	done
	@for i in $(SRCS); do \
		install $$i $(WWW_DIR)/include; \
		perl -i -pne 's/%(\w+?)%/$$ENV{$$1}/ge' $(WWW_DIR)/include/$$i; \
	done

clean:
	@echo "\\033[1;35m+++ Cleaning include files\\033[39;0m"
	@for i in $(SUBDIRS) ; do \
		$(MAKE) -C $$i clean; \
	done
