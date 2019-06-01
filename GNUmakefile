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
PROCESSED_TARGETS := $(TARGETS_FROM_MD_PHP) $(TARGETS_FROM_MD)

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
	@helpers/yaml2phploader config _config.yml > $@

.cache/site.php: src/_site.yml
	$(info ## Preparing $@ ...)
	@helpers/yaml2phploader site _site.yml > $@

.cache/posts.php: $(POSTS)
	$(info ## Preparing $@ ...)
	@helpers/yaml2phparrayloader posts $^ > $@

.cache/pages.php: $(PAGES)
	$(info ## Preparing $@ ...)
	@helpers/yaml2phparrayloader pages $^ > $@

.cache/my/%.html: src/%.md.php
	$(info ## Preparing $@ ...)
	@test -d $(@D) || mkdir -p $(@D)
	@helpers/yaml2phploader my $*.md.php $*.html > "$@"

.cache/my/%.html: src/%.md
	$(info ## Preparing $@ ...)
	@test -d $(@D) || mkdir -p $(@D)
	@helpers/yaml2phploader my $*.md $*.html > "$@"

## For .MD.PHP
# Stage 1: preprocess
$(TARGETS_FROM_MD_PHP:build/%.html=.cache/preprocessed-sources/%.md): .cache/preprocessed-sources/%.md: src/%.md.php .cache/my/%.html
	$(info ## Preparing $@ ...)
	@test -d $(@D) || mkdir -p $(@D)
	@rm -f .cache/include/$*.html
	@php -d short_open_tag=1 -f helpers/preprocess-source $* $@ $^ > "$@"

# Stage 2: Apply layout
$(TARGETS_FROM_MD_PHP:build/%.html=.cache/applied-laidouts/%.html.php): .cache/applied-laidouts/%.html.php: .cache/preprocessed-sources/%.md .cache/my/%.html
	$(info ## Preparing $@ ...)
	@test -d $(@D) || mkdir -p $(@D)
	@helpers/apply-layout $* $@ $^ > "$@"

## For .MD: skip directly to Stage 2
# Stage 2: Apply layout
$(TARGETS_FROM_MD:build/%.html=.cache/applied-laidouts/%.html.php): .cache/applied-laidouts/%.html.php: src/%.md .cache/my/%.html
	$(info ## Preparing $@ ...)
	@test -d $(@D) || mkdir -p $(@D)
	@helpers/apply-layout $* $@ $^ > "$@"

# GENERIC for the rest of the steps
# Stage 3: Postprocess laidout
$(PROCESSED_TARGETS:build/%.html=.cache/polished-laidouts/%.md): .cache/polished-laidouts/%.md: .cache/applied-laidouts/%.html.php .cache/my/%.html
	$(info ## Preparing $@ ...)
	@test -d $(@D) || mkdir -p $(@D)
	@php -d short_open_tag=1 -f helpers/polish-laidout $* $@ $^ > "$@"

# Stage 4: Apply letterhead
$(PROCESSED_TARGETS:build/%.html=.cache/applied-letterhead/%.php): .cache/applied-letterhead/%.php: .cache/polished-laidouts/%.md .cache/my/%.html
	$(info ## Preparing $@ ...)
	@test -d $(@D) || mkdir -p $(@D)
	@helpers/apply-letterhead $* $@ $^ > "$@"

# Stage 5: Polish letter
$(PROCESSED_TARGETS): build/%.html: .cache/applied-letterhead/%.php .cache/my/%.html
	$(info ## Preparing $@ ...)
	@test -d $(@D) || mkdir -p $(@D)
	@php -d short_open_tag=1 -f helpers/polish-letter $* $@ $^ > "$@"

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
