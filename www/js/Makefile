TOPDIR ?= $(realpath ../..)

include $(TOPDIR)/Config.mk

SUBDIRS =
SRCS =
CHECK_JS =

check-js: $(CHECK_JS)
	@echo "Running JSHint on javascript..."
	@for i in $(CHECK_JS); do \
		jshint $$i --config .jshintrc ; \
	done
	@echo "${CHECK} Done"

%.min.js: %.js
	@echo "Compiling and minifying javascript..."
	@+make check-js
	@uglifyjs -o $@ $<
	@echo "${CHECK} Done"

install:
	@echo "\\033[1;35m+++ Installing js\\033[39;0m"
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done

clean:
	@echo "\\033[1;35m+++ Cleaning js\\033[39;0m"
	@for i in $(SUBDIRS) ; do $(MAKE) -C $$i clean; done
