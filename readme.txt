=== Activity Plus Reloaded for BuddyPress ===
Contributors: buddydev
Original Contributors: wpmudev
Tags: BuddyPress, Activity Stream, BuddyPress Activity, BuddyPress Activity Upload, Embed Video, Embed Link, Upload Photo, Upload Photos, Share Media, Sharing Media, Social Media
Requires at least: 5.0
Tested up to: 6.4.2
Stable tag: 1.1.1

Activity Plus Reloaded for BuddyPress allows embedding of oEmbed videos and media as well as external links in your activities.

== Description ==

Activity Plus Reloaded for BuddyPress gives your social network all the features and ease of Facebook when it comes to uploading and sharing media!

It is a fork of now unmaintained [BuddyPress Activity Plus](https://wordpress.org/support/plugin/buddypress-activity-plus/)
The plugin adds 3 new buttons to your BuddyPress activity stream.  Enabling you to attach photos, videos, and even share web links with everyone on your network!

Here's the quick overview of this plugin's features:
 * Upload a photo (or multiple) directly from your computer to the activity stream
 * Embed a video from popular sites such as youtube and vimeo by copying the link
 * Embed a link to any site - the site title and description will automatically be pulled in
 * Embedding a link also allows you to choose a thumbnail image from a list of images on the site's homepage
 * Works perfectly with any theme based on the BuddyPress Default theme

Blog Post :[Introducing BuddyPress Activity Plus Reloaded](https://buddydev.com/introducing-buddypress-activity-plus-reloaded/)

= Credit =
Activity Plus Reloaded for BuddyPress is a fork of *BuddyPress Activity Plus*(now abandoned) by @wpmudev. We have refactored it to wok with current BuddyPress/WordPress.
 and we plan to maintain and further develop it.
 We would like to express our sincere gratitude to the @wpmudv team for their cooperation in getting this plugin back.

If you are looking to optimize media, We recommend [Smush](https://wordpress.org/plugins/wp-smushit/) to optimize your BuddyPress media.

= Contribute =
The plugin is available on gihub. You can contribute by sending pull request, reporting errors and helping others.
Github repository: [https://github.com/buddydev/bp-activity-plus-reloaded](https://github.com/buddydev/bp-activity-plus-reloaded)
Support & reporting Issues: [BuddyDev Forums](https://buddydev.com/support/forums/)

== Installation ==
1.  Download the plugin file
2.  Unzip the file into a folder on your hard drive
3.  Upload the `/bp-activity-plus-reloaded/` folder to the `/wp-content/plugins/` folder on your site
4.  Single-site BuddyPres go to Plugins menu and activate there.
5.  For Multisite visit Network Admin -> Plugins and Network Activate it there.
6. Visit Settings->Activity plus to update settings.

== Frequently Asked Questions ==

= Where do I get the support? =
Please use [BuddyDev Support forums](https://buddydev.com/support/forums/).

= What is the difference between MediaPress and this plugin? =
MediaPress allows creating user gallery, sitewide gallery as well as group galleries too. This plugin only allows upload from activity.
If you are looking for a simple BuddyPress activity media solution, this may fit you. You may want to explore [MediaPress](https://wordpress.org/plugins/mediapress/) to see if that fits your need.

= Will you be maintaining this plugin? =
Yes. Until the media feature comes to BuddyPress core, we will maintain and develop this plugin.

== Screenshots ==

1. Photos and websites are easily embedded screenshot-1.png
2. Image galleries right in the activity stream screenshot-2.png
3. Video in your activity stream screenshot-3.png

== Changelog ==
= 1.1.1 =
- Handled ajax response in case of failure

= 1.1.0 =
- Enforce the image limit for remote images too

= 1.0.9 =
- Made the constant BPFB_IMAGE_LIMIT feasible from bp-custom.php

= 1.0.8 =
- Add flag for disabling the upload form.

= 1.0.7 =
- Made plugin string translatable.

= 1.0.6 =
- Added option to select which upload types(video,ausio,image) whould be available.

= 1.0.5 =
- Made string translatable.

= 1.0.4 =
- Added compatibility for themes using content editable.

= 1.0.3 =
- Add option for link opening preference(same window or external).

= 1.0.2 =
- Fix issue with BuddyPress Group documents. Disables integration in favour of future rewrite.
- Fixes hardcoded version number with dynamic version.

= 1.0.1 =
- Fixes bug with link posting for the link pages which has large sizes.
- Fixes issues with BP Nouveau posting on single group
- todo: Fix issue with BP Nouveu template on activity directory page for posting to groups. Currently, there is a bug in BP Nouveau preventing us from doing it.

= 1.0.0 =
- Initial release.
- Comes as refactored BuddyPress Activity Plus (1.6.5) for easier maintainence.
- Fixes issue with PHP 7.3/7.4
