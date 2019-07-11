Awake
=====

Awake is a Static Site Generator (SSG). It uses PHP as the templating engine, GNU Make as the task engine and Pandoc as the document converter.

This offers the following advantages:

1. Instead of using a limited templating language, the full power of PHP is available from any of the templates and the documents themselves.

2. Using GNU Make means using a proven tool to avoid useless work: changing the sources will always rebuild the site efficiently, without uselessly regenerating unaffected output files. It also means we can regenerate only a specific output file if so we need during development.

3. Input files can be are cleanly written in Markdown or reStructuredText for easy editing and versioning.

4. Having Pandoc as the document converter means that support for other input formats can be easily added to Awake.

Output file generation in done in two stages:

1. First, it takes the source Markdown or reStructuredText files and applies a layout template to set the content arrangement in the page. You can have as many layout templates as you need but each page can use only one.

2. Then, it applies a letterhead to the laid-out content to wrap it around whatever boilerplate you design for your site. You can have as many letterheads as you need but each page can use only one.

Usage
-----

To run under Debian it needs the php-cli, php-yaml, pandoc and make packages.

The source directory is src and awake copies everything into build, except:

* If it starts with an underscore, it's not copied.

* If it has an .md\[.php] or .rst\[.php] or .html.php suffix, it's
  processed with PHP and Pandoc.

Awake uses a 3-layer composition concept: content, layout and letterhead.
The content is laid out using the specified layout and then the letterhead
is applied. Any of the three can have PHP code but beware that layout and
letterhead templates is first processed by Pandoc so any PHP variable
references must use a double dollar sign, as single-dollar references will
be interpreted by Pandoc.

Performance
----------

I tested it on a project composed as follows (numbers are approximate):

* 80 .md/.md.php files (to be fully processed)
* 90 .png files (to be just copied)
* 2 layouts (to be used as templates)
* 1 letterhead (to be used as template)
* 2 css files (to be just copied)

Total: 175 files

Build time without cache: 18 seconds.

Build time with cache: 6 seconds.

Sample run
----------

    $ find src
    src
    src/_letterheads
    src/_letterheads/default.php.pdt
    src/index.md.php
    src/posts
    src/posts/20190528-this-is-a-post.md
    src/_config.yml
    src/_site.yml
    src/_layouts
    src/_layouts/default.php.pdt
    
    $ make
    ## Preparing .cache/config.php ...
    ## Preparing .cache/site.php ...
    ## Preparing .cache/posts.php ...
    ## Preparing .cache/pages.php ...
    ## Generating build/posts/20190528-this-is-a-post.html ...
    ## Generating build/index.html ...
    
    $ find build
    build
    build/index.html
    build/posts
    build/posts/20190528-this-is-a-post.html


