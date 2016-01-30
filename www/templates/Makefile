TOPDIR ?= $(realpath ../..)

include $(TOPDIR)/Config.mk

SUBDIRS =

SRCS  = association.html
SRCS += association_validate.html
SRCS += change_password.html
SRCS += login.html
SRCS += member_registration.html
SRCS += modify_association_data.html
SRCS += new_association.html
SRCS += project_entry.html
SRCS += recover_exception.html
SRCS += signup_request.html
SRCS += success.html
SRCS += association_option.html
SRCS += cancel_member_entry.html
SRCS += exception.html
SRCS += member_entry.html
SRCS += menu_entry.html
SRCS += modify_member_data.html
SRCS += new_mail.html
SRCS += project_list.html
SRCS += request_entry.html
SRCS += signup_validate_entry.html
SRCS += template1.html
SRCS += association_validate_entry.html
SRCS += cancel_member.html
SRCS += login_action.html
SRCS += member_list.html
SRCS += menu.html
SRCS += new_association_extended.html
SRCS += new_user.html
SRCS += project_request.html
SRCS += request_list.html
SRCS += signup_validate.html

install:
	@echo "\\033[1;35m+++ Installing templates\\033[39;0m"
	@mkdir -p $(WWW_DIR)/templates
	@for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done
	@for i in $(SRCS); do \
		install $$i $(WWW_DIR)/templates ; \
		perl -i -pne 's/%(\w+?)%/$$ENV{$$1}/ge' $(WWW_DIR)/templates/$$i ; \
	done

clean:
	@echo "\\033[1;35m+++ Cleaning templates\\033[39;0m"
	@for i in $(SUBDIRS) ; do $(MAKE) -C $$i clean ; done
