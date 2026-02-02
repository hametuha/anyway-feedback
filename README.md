# Anyway Feedback

Contributors: Takahashi_Fumiki,hametuha  
Tags: feedback, analytics  
Tested up to: 6.9  
Stable Tag: nightly  

This plugin enable users to send feedback with single click.

## Description

Anyway Feedback provides simple controller with 2 buttons(Useful and Useless). Users can send feedback to specific post or comment. 

What you get is amount of positive feed backs and negative ones per post types. Typical usage is for FAQ. You could know if your FAQs are usefull or not.

Sidebar also supported. You can display sidebar which includes the most popular posts per post type.

If you have some request, feel free to contact me. For experienced developper, I'm waiting  for pull requests on github.com.

**NOTICE** Requires PHP 7.4 and WordPress 6.6 or later.

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

### How can I track feedback events with Google Analytics?

Since version 1.2.0, the plugin dispatches a custom JavaScript event `feedback.afb` when a user submits feedback. You can listen to this event and send data to Google Analytics (or any other analytics service) yourself.

<pre>
document.addEventListener( 'feedback.afb', function( e ) {
    // e.detail contains: { type: 'post'|'comment', objectId: number, affirmative: 0|1 }
    gtag( 'event', 'feedback', {
        feedback_type: e.detail.type,
        object_id: e.detail.objectId,
        is_positive: e.detail.affirmative === 1,
    } );
} );
</pre>

**Note:** The built-in GA4 integration was removed in version 1.2.0. Use the custom event approach above for more flexibility.

## Screenshots

1. Controller looks like this. You can customize it with your own css and markup.
2. Super cool statistic summary in admin panel! Yeah!
3. Simple setting. Documentations are also on admin screen!

## Changelog

### 1.2.0

* Remove jQuery dependency from frontend script.
* Remove js-cookie dependency in favor of native `document.cookie` API.
* Use wp-i18n for JavaScript translations instead of `wp_localize_script`.
* Change default markup from `<a>` tags to `<button>` tags. Custom markup with `<a>` tags still works.
* **New Feature:** Add negative feedback reason collection feature. When users click "Useless", a dialog prompts them to provide a reason. The answers will be stored as comments.
* Add REST API endpoint `POST /afb/v1/negative-reason/{post_type}/{object_id}` for storing feedback reasons.
* **Breaking Change:** Remove built-in GA4 integration. Use the `feedback.afb` custom event to send data to your analytics service.
* Add percentage rate display for feedback results.
* **Breaking Change:** The custom event format changed from jQuery `.trigger()` to vanilla `CustomEvent`. Listen with `document.addEventListener('feedback.afb', (e) => { /* e.detail */ })`.
* **Important** Requires PHP >= 7.4 and WordPress >= 6.6.

### 1.1.0

* Change feedback endpoint from admin-ajax.php to REST API.
* Drop jquery-cookie and use js-cookie.
* Remove session_start() from admin screen.
* Support GA4.
* Requires PHP>=7.2 and WordPress >=5.9

### 1.0.1

* Fixed filename bug. Sorry for that.
* Stop warning error.

### 1.0

* Requires PHP 5.4 and over. Name space is so cool! Template tags have backward compatibility.
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
