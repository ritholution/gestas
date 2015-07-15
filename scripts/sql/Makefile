TOPDIR = ../..

SUBDIRS +=

install:
	@echo "Installing sql scripts"
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done
