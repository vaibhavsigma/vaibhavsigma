=== Embed React app ===
Contributors: pavex
Tags: embed, react, reactapp
Requires at least: 4.0
Tested up to: 5.1
Stable tag: 4.0.8
License: GPLv2 or later

Embed React application into your Wordpress post.

== Description ==

This is a simple way how to embed react application in to your post. In my blog, a wanted include small application, but typical react app is a single page app, hardly linked in index.html. He needed a simple way to solve it. The result is visible on my blog, for example: [Embeding Google Photos album](https://www.publicalbum.org/blog/embedding-google-photos-albums).

== How to do it ==

You need compiled React app hosted on public url. Find filenames after building an application. If you using create-ract-app script, all what you need, find in "static" directory. Assemble file names into a shortcode like this:

`[reactapp id="root" js="pathto/static/js/main.000000.js" css="pathto/static/css/main.000000.css"]`
`[reactapp id="custom_id" "pathto/static/js/main.000000.js" "pathto/static/css/main.000000.css" "pathto/static/css/another.css"]`

- id - ID of application root element
- js[n] - path to Java script bundle
- css[n] - path to Stylesheet
- (files are detected automaticly by extension js/css)

That's all.
When application was updated, you must setup new links to shortcode.
If you want to more information about this plugin, you may visit [Embedding a React Application to WordPress post](https://www.publicalbum.org/blog/embedding-react-app-wordpress-post) post in my blog.

= 1.0.1 =
*Release Date - 18 January 2019*

* Multiple script insert for new react build compatibility.

= 1.0.0 =

* React application shortcode.
