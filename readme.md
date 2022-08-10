# Static

## What it is

A mini PHP framework for static PHP websites.

No back-office, no complex configuration or database to set up, all the content can be edited by modifying files and folders.

You won't even have to resize pictures, just drop them in the pictures folder and let the template resize them for you!

## Getting started

Download this project, upload it on your apache server and voilà! A basic website is already running, you can create content right away and start customizing its bare-bones theme.

## General structure

	📁 **.git** : ❌ you can delete this one ❌
	📁 **_core** : core files that make the framework ... work, don't touch that
	📁 **pages** : **the pages content go there, one sub-folder per page**
	📁 **blogs** : **contains the blog posts, one sub-folder per post**
	📁 **img** : **contains all the pictures you want to use on your website (except for the favicon), sub-folders will be created for resized-versions**
	📄 **base.php** : base template for the whole website (including the header, head, menu, footer and content wrapper)
	📄 **404.php** : template for the 404 error page
	📄 **config.yml** : **helps you set data that will be used through and accessible from the whole website such as : the website name, contact info or menus links.**
	📄 **.htaccess** : redirects all requests to the index.php file so the custom router can handle them
	📄 **style.css** : **the general style sheet**
	📄 **index.php** : you don't want to edit that one
	📄 **favicon.ico** : the website's favicon
	📄 **.gitignore** : ❌ you can delete this one ❌
	📄 **readme.md** : ❌ you can delete this one ❌

## Adding content, creating blog posts and pages

To create a blog post or page, you just have to create a new sub-folder under either /blogs/ or /pages/. The folder should be named after the exact slug you want to appear in the search bar for this element.

Such as :

Pages URL  : www.website.com/page-slug/
Blog posts URL : www.website.com/blog/post-slug/

Where page-slug and post-slug are the corresponding folder's name.

You can add an empty folder and create the files you need from scratch or copy the entire local /_boilerplate/ folder and rename it then tweak the files it contains.

**mandatory files**

	- content.md : the post's or page's text (don't write the page's heading there though, this one goes into the meta.yml file)
	- meta.yml : extra data that doesn't go in the content.md file, mainly meta-data such as date of creation, last update, meta-description, tags, tab title, but can also fit data such as the thumbnail's file name

**optional files**

They contain code you want to include for this page or post only.
	- template.php : allows you to create a specific template and add PHP code to run for this page or post only,
	- imports.html : imports in the form of HTML tags (style sheets or scripts), they'll be added to the <head> tag
	- scripts.js : plain JS code that will be added at the very end of the <body> tag
	- style.css : additional CSS that will be added in a <style> tag at the end of the body, just before the JavaScript

**Create drafts**

If you want to prevent drafts from being displayed on your website for now but still want them saved on the server, simply store their markdown files in the /pages/ or /blogs/ folder, they will be ignored. For the same result, you can also hide an existing piece of content by adding an underscore at the beginning of the name of its folder.

## Special pages and templates

**Home page**

You can find its files in the /__home_page/ folder. This folder works the same as a page or blog post folder except the template.php file is mandatory here.

**Generic template for pages and blog post**

/pages/single.php and /blog/single.php allow you to edit the way all pages and blog posts are displayed.

**Blog posts list and tag page**

/blogs/ contains extra folders for blog specific pages which are :
	- /__list/ : displays the list of the blog posts at www.website.com/blog/
	- /__tag/ : same as list except only the posts who possess a given tag are displayed, this page can be access by clicking on a tag name in the list, its URL looks like www.website.com/tag/tag-name

The files in those folders behave exactly like blog post files except the template.php file is mandatory for them to work properly.

##  Customizing templates

Each template can access the following variables :
	- data : array that contains all the data extracted from the website's folders, contains the following keys :
		- list : for the pages that display a list such as the tag or blog list page
		- meta : meta.yml file content as a PHP array
		- content : content.md text converted to HTML
		- excerpt : the first 55 words of the content
		- imports : imports.html content as is
		- js : scripts.js content as is
		- css : style.css content as is
		- js_intag : can be populated in the page / post-specific template itself with JavaScript code between <script> tags
		- css_intag : can be populated in the page / post-specific template itself with CSS code between <style> tags

## Pictures

All the pictures go into the /img/ folder. Just drop them at the root of the folder.

You can then use them in a template with the corresponding URL :
/img/pic-name.jpg

Or request a specific size like so :
/img/<width>/<height>/pic-name.jpg
(eg. /img/200/300/pic-name.jpg)

The file formats are currently supported :
	- jpg
	- png
	- svg
	- gif
	- ico
	- webp

Note : the svg files are never resized, an URL containing a height and width for an svg will simply yield the original file instead of a resized copy.
