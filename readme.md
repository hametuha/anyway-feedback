# Anyway Feedback
Contributors: Takahashi_Fumiki  
Tags: feedback, analytics  
Requires at least: 3.8  
Tested up to: 4.1-alpha  
Stable tag: 1.0  

This plugin enable users to send feedback with single click. This may support you to analyze your user's opinion. Works like Facebook's help center.

## Description

Anyway Feedback provides simple controller with 2 buttons(Useful and Useless). Users can send feedback to specific post or comment. 

What you get is amount of positive feed backs and negative ones per post types. Typical usage is for FAQ. You could know if your FAQs are usefull or not.

Sidebar also supported. You can display sidebar which includes the most popular posts per post type.

If you have some request, feel free to contact me. For experienced developper, I'm waiting  for pull requests on github.com.

**NOTICE** Requires PHP 5.3 and over.

## Installation

Installation is easy.

e.g.

1. Upload `anyway-feedback` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. In admin panel, Go to Options > Anyway Feedback and set up with instractions displayed.
1. You can customize display and use widget.
1. Template tag manual is on admin panel.

## Frequently Asked Questions

### How can I know the feedbacks result?

You can see it on your admin panel. Go to Options > Anyway Feedback.

### Can custom post type FAQ will be registered?

No. If you need some custom post type, you have to register it. There are lots of plugins which register custom post type with GUI. After registration, you can easily add feedback controller to custom post type with Anyway Feedback plugin.

### How can I display feedback results?

There are currently 2 ways. 1st is a feedback controller which displays number of people saying good or not. 2nd is a widget which displays most popular posts per post type.

## Screenshots

1. Controller looks like this. You can customize it with your own css and markup.
2. Super cool statistic summary in admin panel! Yeah!
3. Simple setting. Documentations are also on admin screen!

## Changelog

### 1.0

* Requires PHP 5.3 and over. Name space is so cool! Template tags have backward compatibility.
* Quit using session. Vote history will be stored in Cookie.
* Refine admin screen.
* Fix some style sheet.
* Bug fix. trashed posts will be no longer displayed on widget.
* **New Feature** Add [Google Analytics event tracking](https://developers.google.com/analytics/devguides/collection/analyticsjs/events). See detail at setting screen.

### 0.6

* Use $_SESSION to avoid user from repeated feedback.

### 0.5

* 1st release.

## Upgrade Notice 

### 0.5

Nothing.