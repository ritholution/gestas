TOPDIR = ..

SUBDIRS += css
SUBDIRS += js

install:
	@echo "Installing..."
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done
