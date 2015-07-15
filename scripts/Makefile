TOPDIR = ..

SUBDIRS += sql

install:
	@echo "Installing scripts"
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done
