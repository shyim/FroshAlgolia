Algolia for Shopware
=====
This plugin integrates the high-performance search cluster [Algolia](https://www.algolia.com/) in your Shopware system.

Algolia Search allows full-text searches over tons of records within microseconds. This ensures a great UX for your customers and high click-through rates on your search results.

Algolia credentials for development
-----
* **Application ID**: 47WGJ9ERN3
* **Search-Only API Key**: ea33799ba0afd8de30e5c73163a72c70

Requirements
-----
* Shopware >= 5.2.5

Pushing article data to Algolia index
====
To push your article data to the algolia index, run the following command:

`php bin/console algoliaindex push`