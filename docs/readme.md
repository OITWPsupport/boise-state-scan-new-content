#Purpose#
This document describes how to use the Boise State Scan New Content WordPress plugin, and how to understand the information it provides.

#Overview#
When installed and activated on a WordPress site, the Boise State Scan New Content plugin triggers an automated scan of all new or updated content you publish. This scan looks at the newly published page for accessibility errors, and automatically emails a summary of any errors to the WP Support team (wp-support-group@boisestate.edu). The Site Admin can choose to receive this email, too.

#Installation#
The Boise State Scan New Content is available at
https://github.com/OITWPsupport/boise-state-scan-new-content/releases/latest
To install it for the first time, download the zip file and upload it to your WordPress site. To update the plugin, select Network Admin -> Dashboard -> Updates and look for "Boise State Scan New Content” on the list of plugins with available updates.

#Configuration#
The plugin adds a new menu to your WordPress admin interface. In your site dashboard, click "Settings," then "Boise State a11y options." If you want to receive scan results from the plugin, check the box that reads "Automatically scan pages and posts for accessibility errors" and provide your email in the  "Send notice of errors to" text field.

WP Support will receive a copy of the scan results whether you check the box or not.

#Accessibility Errors#
If the content you’ve published contains no errors, the plugin will take no action.

If it detects accessibility errors, it will email the WP Support team (and your Site Admin if that option was configured). This alerts the relevant parties that accessibility errors have been introduced into your site.

The email contans the following information:

- The URL of the page that was scanned
- A description of the WCAG guideline that failed testing.
- The identifier of that WCAG guideline, for further information.
- A URL where you can find more information about the error and how to fix it.

Example:

>**Error:** Img element missing an alt attribute. Use the alt attribute to specify a short text alternative.  
>WCAG2AA.Principle1.Guideline1\_1.1\_1\_1.H37  
>[https://www.w3.org/WAI/GL/2015/WD-WCAG20-TECHS-20150714/H37](https://www.w3.org/WAI/GL/2015/WD-WCAG20-TECHS-20150714/H37)
>
>**Error:** Iframe element requires a non-empty title attribute that identifies the frame.
>WCAG2AA.Principle2.Guideline2\_4.2\_4\_1.H64.1  
>[https://www.w3.org/WAI/GL/2015/WD-WCAG20-TECHS-20150714/H64](https://www.w3.org/WAI/GL/2015/WD-WCAG20-TECHS-20150714/H64)

