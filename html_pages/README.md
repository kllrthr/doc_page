# Doc settings

This module is an alternative to using iframes for dispaying external documentation.

# Install

Place this module in drupal/modules/custom/ 

It should look like this: drupal/modules/custom/html_page

Activate the module on the "extend" page. (/admin/modules)

#Use

Go to the permission page and add the permission: "Administer documentation page" to the role that should have access. This is not needed if you are user 1.

Navigate to /admin/config/content/html and enter the url for the documentation page source. This for is also linked from the Configuration page.

These are the pages

- /documentation
- /releaseinfo

Create an url alias if you want a different url for any of these pages. 
