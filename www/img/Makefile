TOPDIR ?= $(realpath ../..)

include $(TOPDIR)/Config.mk

SUBDIRS =
SRCS  = header.png
SRCS += logogestas-71x100.png
SRCS += logo_junta_azul_70x45.png
SRCS += logo_junta_azul.png
SRCS += logo_opentia_150x29.png
SRCS += logo_opentia_mediano.png
SRCS += sello_cssW3C_mini.jpg
SRCS += sello_html1-0W3C_mini.jpg
SRCS += sello_wai-W3C_mini.jpg

install:
	@echo "\\033[1;35m+++ Installing images\\033[39;0m"
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done

clean:
	@echo "\\033[1;35m+++ Cleaning images\\033[39;0m"
	@for i in $(SUBDIRS) ; do $(MAKE) -C $$i clean ; done
