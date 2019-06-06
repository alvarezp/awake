PREFIX ?= usr/local

HELPERS = html2html md2html rst2html yaml2phparrayloader yaml2phploader

AWAKE_DST = $(DESTDIR)/$(PREFIX)/bin/awake
HELPERS_DST = $(addprefix $(DESTDIR)/$(PREFIX)/share/lib/awake/,$(HELPERS))
CONFIG_DST = $(DESTDIR)/etc/awakeconf.mk

.PHONY: all
all: ;

$(dir $(AWAKE_DST)):
	mkdir -p $@

$(AWAKE_DST): awake | $(dir $(AWAKE_DST))
	cp $< $@

$(DESTDIR)/$(PREFIX)/share/lib/awake:
	mkdir -p $@

$(HELPERS_DST): $(DESTDIR)/$(PREFIX)/share/lib/awake/%: helpers/% | $(DESTDIR)/$(PREFIX)/share/lib/awake
	cp $< $@

$(CONFIG_DST): $(HELPERS_DST)
	echo HELPERDIR=$(DESTDIR)/$(PREFIX)/share/lib/awake > $@

.PHONY: install
install: $(AWAKE_DST) $(HELPERS_DST) $(CONFIG_DST) ;

.PHONY: uninstall
uninstall:
	rm -f $(AWAKE_DST)
	rm -f $(HELPERS_DST)

.PHONY: purge
purge: uninstall
	rm -r $(CONFIG_DST)
