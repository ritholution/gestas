TOPDIR ?= $(realpath ..)

include $(TOPDIR)/Config.mk

SUBDIRS = db

install:
	@echo "\\033[1;35m+++ Installing scripts\\033[39;0m"
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done

clean:
	@echo "\\033[1;35m+++ Cleaning scripts\\033[39;0m"
	@for i in ${SUBDIRS}; do \
		make -C $$i clean; \
	done
