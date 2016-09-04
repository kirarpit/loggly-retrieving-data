# Loggly Event Retrieval API Library
> loggly-retrieving-data is the PHP library of [Loggly Retrieving Data API](https://www.loggly.com/docs/api-retrieving-data/).

## Installation
`composer require kirarpit/loggly-retrieving-data`

## Usage
Anything which can be searched on Loggly Search Panel could be queried here and the resulted events would be returned along with the total number of such events.

The must defined parameters for a search are 'query', 'from_timestamp' and 'to_timestamp', rest are optional.

Optional size parameter in the query defines the number of events fetched in a single page. The maximum value it can take is 5000, after which another curl request is to be made with shorter duration as described in the [Loggly Retrieving Data API](https://www.loggly.com/docs/api-retrieving-data/), which is automatically handled by the library under the hood.

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
