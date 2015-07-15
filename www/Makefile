TOPDIR = ..

SUBDIRS += css
SUBDIRS += classes
SUBDIRS += img
SUBDIRS += js

install:
	@echo "Installing code"
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done
