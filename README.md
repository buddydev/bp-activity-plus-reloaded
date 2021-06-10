# BuddyPress Activity Plus Reloaded

This plugin is a fork of BuddyPress Activity Plus by WPMUDEV.

## Fast powerful image, video and link sharing for BuddyPress.
 

![share-video-735x470](https://premium.wpmudev.org/wp-content/uploads/2011/05/share-video-735x470.jpg)

 Integrate easy access to video, image and link sharing.

### Easy Sharing

Add a set of buttons that make it easy to share content from across the web. Simplify video embed, photo sharing and content linking. Preview embedded content before posting, add your own commentary and auto-pull titles, descriptions and thumbnails.  There's no configuration needed – just activate the plugin and allow your users to start sharing.  

![Styles-735x470](https://premium.wpmudev.org/wp-content/uploads/2011/05/Styles-735x470.jpg)

 Use a built-in style or create your own.

### Styles That Fit

Choose one of the included button style that best fits your theme. Button designs include Legacy, Modern and rounded. Or follow the simple customization guide and craft buttons that perfectly fit your design aesthetic.     

![Link-post-735x470](https://premium.wpmudev.org/wp-content/uploads/2011/05/Link-post-735x470.jpg)

 Autofill title and description and choose a thumbnail.

### Complete Control

Configuration tools are super easy to use from toggle theme and alignment selection to oEmbed and thumbnail size setup. All the BuddyPress Activity + settings can be accessed from one simple settings page. 

## Usage

### To Get Started:

Start by reading [Installing Plugins](https://premium.wpmudev.org/wpmu-manual/using-regular-plugins-on-wpmu/) section in our [comprehensive WordPress and WordPress Multisite Manual](https://premium.wpmudev.org/wpmu-manual/) if you are new to WordPress.

### To Install:

1.  Download the plugin file 

2.  Unzip the file into a folder on your hard drive 

3.  Upload the **_/_buddypress-activity-plus****_/_** folder and all its contents to the **/wp-content/plugins/** folder on your site 

4.  Login to your admin panel for WordPress or Multisite and activate the plugin:

*   On regular WordPress installs - visit **Plugins** and **Activate** the plugin.
*   For WordPress Multisite installs - visit **Network Admin -> Plugins** and **Network Activate** the plugin.

5\. Once installed and activated, you will see a new menu item in your admin at Settings > Activity Plus.

### Configuring Settings

Basic settings can be configured at Settings > Activity Plus. 

![alt](https://premium.wpmudev.org/wp-content/uploads/2011/05/buddypress-activity-plus-settings.png)

 1. Theme  
2\. Alignment  
3. oEmbed Width  
4. Image thumbnail dimensions

 1\. Select a _Theme_ to be used for the icons. 
 
 2\. Choose the _Alignment_, either Left or Right. This alignment setting will be used for the posted media. 
 
 3\. Specify the _oEmbed Width_, which will be the width of any media files such as videos. The height of the media will adjust to accommodate the width. 
 
 4\. Specify the _Image thumbnail dimensions_, both Width and Height, to be used for thumbnail images for the posted media. Now let's take a look at further customization options.

### Usage and Customization

You can also use custom icons in your theme, simply use add_theme_support("bpfb_toolbar_icons"); in your functions.php, copy over the rules from css/bpfb_toolbar.css and edit to suit your needs. Alternatively, if you're OK with 32x32 icon sizes, you can just override the icons in your stylesheet using background property and !important. These are the IDs: #bpfb_addPhotos, #bpfb_addVideos, #bpfb_addLinks, #bpfb_addDocuments You can also set your preferred thumbnail size separately from your default thumbnail size settings, if you wish to do so. You can do that by adding this line to your wp-config.php: `define('BPFB_THUMBNAIL_IMAGE_SIZE', '200x200');` Where "200x200" are width and height (in that order), in pixels. Finally, be sure to verify your default sizes for embedded media. It's in Settings -> Media -> Embeds -> Maximum embed size There are a few additional constants that you can override in your _wp-config.php_ file to further customize how the plugin functions. Add the following to wp-config.php to override oEmbed width: `define('BPFB_OEMBED_WIDTH', 450, true);` Add the following to wp-config.php to increase/decrease the number of allowed images per activity item: `define('BPFB_IMAGE_LIMIT', 5, true);`

### User Experience

Here are a few screenshots of what this plugin could look like on your site for your users (depending on your theme of course). 

![Adding an image to an activity update.](https://premium.wpmudev.org/wp-content/uploads/2011/05/buddypress-activity-plus-add-image.png)

 Adding an image to an activity update.

 

![Image added to update.](https://premium.wpmudev.org/wp-content/uploads/2011/05/buddypress-activity-plus-image-added.png)

 Image added to update.

 

![Adding a video to an activity update.](https://premium.wpmudev.org/wp-content/uploads/2011/05/buddypress-activity-plus-add-video.png)

 Adding a video to an activity update.

 

![Video added to update.](https://premium.wpmudev.org/wp-content/uploads/2011/05/buddypress-activity-plus-video-added.png)

 Video added to update.

 

![Adding a link to an activity update.](https://premium.wpmudev.org/wp-content/uploads/2011/05/buddypress-activity-plus-add-link.png)

 Adding a link to an activity update.

 

![Link added to update.](https://premium.wpmudev.org/wp-content/uploads/2011/05/buddypress-activity-plus-link-added.png)

 Link added to update.

### Known Issues

When using the plugin in combination with BuddyPress Media, a conflict can arise due to the activity stream upload, since both the plugins are trying to upload media through the activity stream. If you want to use the BuddyPress Activity Plus functionality, uncheck "Enable Activity Uploading" on the BuddyPress Media Settings Page.

// Dropped
bpfb_injection_additional_condition