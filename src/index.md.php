---
title: Welcome!
---

<? require(".cache/pages.php"); ?>
<? require(".cache/posts.php"); ?>

# Welcome to my new site!

## Pages

<? foreach ($pages as $p): ?>
* [<?= $p['title'] ?>](<?= $p['url'] ?>)
<? endforeach; ?>

## Posts

<? foreach ($posts as $p): ?>
* [<?= $p['title'] ?>](<?= $p['url'] ?>)
<? endforeach; ?>


