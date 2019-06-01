HELPERDIR=./helpers

.SUFFIXES:
.DELETE_ON_ERROR:

.DEFAULT_GOAL := all

SHELL=/bin/bash

rwildcard = $(foreach d,$(wildcard $1*),$(call rwildcard,$d/,$2) $(filter $(subst *,%,$2),$d))
ALL_SOURCES := $(filter-out src/_%,$(call rwildcard,src/,*))
ALL_PARENTS := $(dir $(ALL_SOURCES))
ALL_SOURCES := $(filter-out $(ALL_PARENTS:%/=%),$(ALL_SOURCES))

TARGETS_FROM_MD_PHP := $(patsubst src/%.md.php,build/%.html,$(filter src/%.md.php,$(ALL_SOURCES)))
TARGETS_FROM_MD := $(patsubst src/%.md,build/%.html,$(filter src/%.md,$(ALL_SOURCES)))

ALL_TARGETS := $(patsubst src/%,build/%,$(ALL_SOURCES))
ALL_TARGETS := $(patsubst %.md.php,%.html,$(ALL_TARGETS))
ALL_TARGETS := $(patsubst %.md,%.html,$(ALL_TARGETS))

EXISTING_FILES := $(call rwildcard,build/,*)
EXISTING_FILES_PARENTS := $(dir $(EXISTING_FILES))
EXISTING_FILES := $(filter-out $(EXISTING_FILES_PARENTS:%/=%),$(EXISTING_FILES))
DANGLING_TARGETS := $(filter-out $(ALL_TARGETS),$(EXISTING_FILES))

POSTS := $(wildcard src/_posts/*) $(wildcard src/posts/*)
PAGES := $(filter %.md %.md.php,$(filter-out src/_posts src/posts/% $(POSTS),$(ALL_SOURCES)))


GLOBAL_METADATA := .cache/config.php .cache/site.php .cache/posts.php .cache/pages.php

-include $(ALL_TARGETS:build/%=.cache/include/%)

.PHONY: serve server
serve server:
	@php -S localhost:8000 -t build/

.PHONY: all
all: .cache $(GLOBAL_METADATA) $(ALL_TARGETS) ;

.cache:
	@mkdir -p .cache

.cache/config.php: src/_config.yml
	$(info ## Preparing $@ ...)
	@$(HELPERDIR)/yaml2phploader config _config.yml > $@

.cache/site.php: src/_site.yml
	$(info ## Preparing $@ ...)
	@$(HELPERDIR)/yaml2phploader site _site.yml > $@

.cache/posts.php: $(POSTS)
	$(info ## Preparing $@ ...)
	@$(HELPERDIR)/yaml2phparrayloader posts $^ > $@

.cache/pages.php: $(PAGES)
	$(info ## Preparing $@ ...)
	@$(HELPERDIR)/yaml2phparrayloader pages $^ > $@

## For .MD: skip directly to Stage 2
# Stage 2: Apply layout
$(TARGETS_FROM_MD): build/%.html: src/%.md
	$(info ## Generating $@ ...)
	@test -d $(@D) || mkdir -p $(@D) && php -d short_open_tag=1 -f $(HELPERDIR)/md2html $* $@ $^ > "$@"

$(TARGETS_FROM_MD_PHP): build/%.html: src/%.md.php
	$(info ## Generating $@ ...)
	@test -d $(@D) || mkdir -p $(@D) && php -d short_open_tag=1 -f $(HELPERDIR)/md2html $* $@ $^ > "$@"

build/%: src/%
	$(info ## Copying $@ ...)
	@test -d $(@D) || mkdir -p $(@D) && cp $< $@

.PHONY: clean
clean:
	@echo \#\# Cleaning ...
	rm -fr .cache
	rm -fr build
	
.PHONY: tidy
tidy:
	@echo \#\# Tidying up ...
	rm -fr $(DANGLING_TARGETS)
