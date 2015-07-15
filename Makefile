TOPDIR = .

SUBDIRS += conf
SUBDIRS += scripts
SUBDIRS += www

all:
	@make install
	@make apache-start
	@make db-start

install:
	@mkdir -p dev-env
	for i in ${SUBDIRS}; do \
		make -C $$i install; \
	done

clean:
	@make apache-stop
	@make db-stop
	@rm -fr dev-env

db-stop:
	@echo "Stopping mysql"

db-start:
	@echo "Starting mysql"

apache-stop:
	@echo "Stopping apache"

apache-start:
	@echo "Starting apache"

