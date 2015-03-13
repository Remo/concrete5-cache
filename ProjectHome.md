## Description ##

This is an addon for Concrete5, http://www.concrete5.org
It does improve the performance for pages that only contain static blocks like content, image by caching the whole page output.

## Installation ##

Checkout the trunk and copy "remo\_cache" to the package directory of your Concrete5 site.
Add this line to config/site.php

```
<?php define('ENABLE_APPLICATION_EVENTS', true); ?>
```