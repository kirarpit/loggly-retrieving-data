# Loggly Event Retrieval API
> Loggly retrieving data API library for PHP

## Installation
`composer require kirarpit/loggly-retrieving-data`

## Usage
The library could be used to search for events or filter by field across all log events. The “Search” & “Event” endpoints will work together to return a set of your events and counts as described in the [Loggly Retrieving Data API](https://www.loggly.com/docs/api-retrieving-data/).

```php
<?php

use kirarpit\logglyRetrievingData\Loggly;

// array of search query
$search = array(
		'query' => 'nginx.status:200 AND nginx.requestURI:"/favicon.ico"',
		'from_timestamp' => strtotime(date('Y-m-d H:i:s', strtotime('-5 minutes'))),
		'to_timestamp' => time(),
		'size' => '2000', // optional (defaults '1000')
		'order'=>'asc' // optional (defaults 'desc')
	       );

$loggly = new Loggly(LOGGLY_SUBDOMAIN, LOGGLY_USER, LOGGLY_PASSWORD);

$result = $loggly->query($search);
```
## Contributing
See [CONTRIBUTING.md](https://github.com/kirarpit/loggly-retrieving-data/blob/master/CONTRIBUTING.md).

## License
[MIT](https://github.com/kirarpit/loggly-retrieving-data/blob/master/LICENSE)
