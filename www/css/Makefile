TOPDIR ?= $(realpath ../..)

include $(TOPDIR)/Config.mk

SUBDIRS = 
SRCS = general.min.css

%.min.css: %.css
	@echo "Minifying css..."
	@uglifycss $< > $@
	@echo "${CHECK} Done"

install:
	@echo "\\033[1;35m+++ Installing css\\033[39;0m"
	@mkdir -p $(WWW_DIR)/css
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done
	@for i in $(SRCS); do \
		make $$i; \
		install $$i $(WWW_DIR)/css; \
		perl -i -pne 's/%(\w+?)%/$$ENV{$$1}/ge' $(WWW_DIR)/css/$$i; \
	done

clean:
	@echo "\\033[1;35m+++ Cleaning css\\033[39;0m"
	@for i in $(SUBDIRS) ; do $(MAKE) -C $$i clean ; done
