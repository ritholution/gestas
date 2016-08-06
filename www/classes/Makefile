TOPDIR ?= $(realpath ../..)

include $(TOPDIR)/Config.mk

SUBDIRS  = action_mgmt.mod/
SUBDIRS += association_mgmt.mod/
SUBDIRS += member_mgmt.mod/
SUBDIRS += object_mgmt.mod/
SUBDIRS += project_mgmt.mod/
SRCS  = acl.php
SRCS += action.php
SRCS += association.php
SRCS += base.php
SRCS += contact.php
SRCS += content.php
SRCS += database.php
SRCS += date.php
SRCS += dni.php
SRCS += email.php
SRCS += file.php
SRCS += gdatabaseexception.php
SRCS += gestas.php
SRCS += gexception.php
SRCS += guserexception.php
SRCS += language.php
SRCS += log.php
SRCS += member.php
SRCS += menu.php
SRCS += menu_entry.php
SRCS += module.php
SRCS += module_management.php
SRCS += mysql.php
SRCS += object.php
SRCS += output.php
SRCS += password.php
SRCS += project.php
SRCS += telephone.php
SRCS += template_filter.php
SRCS += type_user.php
SRCS += user.php
SRCS += web.php

install:
	@echo "\\033[1;35m+++ Installing classes\\033[39;0m"
	@mkdir -p $(WWW_DIR)/classes
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done
	@for i in $(SRCS); do \
		install $$i $(WWW_DIR)/classes ; \
		perl -i -pne 's/%(\w+?)%/$$ENV{$$1}/ge' $(WWW_DIR)/classes/$$i ; \
	done

clean:
	@echo "\\033[1;35m+++ Cleaning classes\\033[39;0m"
	@for i in $(SUBDIRS) ; do $(MAKE) -C $$i clean ; done
