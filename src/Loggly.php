<?php 

/**
 * This file is part of the Loggly retrieving data api library.
 *
 * (c) Arpit Garg <iarpitgarg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */

namespace kirarpit\logglyRetrievingData;

class Loggly {

	const MAX_EVENTS_PER_RSID = 5000;// There is currently a maximum limit of 5000 individual events returned per search query

	/** @var string */
	private $domain;

	/** @var string */
	private $account;

	/** @var string */
	private $username;

	/** @var string */
	private $password;

	/** @var string */
	private $rsid;

	function __construct($account, $username, $password) {
		$this->domain = 'loggly.com';
		$this->account = $account;
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * @param array searchOptions
	 * @return array result
	 * @exception if query is not an instance of class Search
	 */
	public function query($search){

		$query = new Search($search);

		if($query instanceof Search){
			$final_result = array();

			$run = 0;
			do{
				$page = 0;
				$rsid = $this->get_rsid($query);
				do{
					$result = $this->get_events($rsid, $page);
					$total_events = $result['total_events'];
					if($page == 0 && $run == 0){
						$final_result['total_events'] = $total_events;
					}
					$final_result['events'][] = $result['events'];
					$page++;

				}while($total_events > $page*$query->get_size() && $page*$query->get_size() < self::MAX_EVENTS_PER_RSID);

				$last_event = end($result['events']);
				if($query->get_order() == 'asc'){
					$query->set_from_timestamp(floor($last_event['timestamp']/1000));
				}else if($query->get_order() == 'desc'){
					$query->set_to_timestamp(ceil($last_event['timestamp']/1000));
				}
				$run++;

			}while($final_result['total_events'] > $run*self::MAX_EVENTS_PER_RSID);

			return $final_result;
		}else{
			throw new \Exception("Not an object of Search class");
		}
	}

	/**
	 * @param string RSID
	 * @param string page number
	 * @return array result contaning total_events, page, events
	 */
	private function get_events($rsid, $page){
		$params = array(
				'rsid' => $rsid,
				'page' => $page
			       );

		$result = $this->makeRequest('apiv2/events', $params);
		$result = json_decode($result, true);

		return $result;
	}

	/**
	 * @param Search query
	 * @return string RSID
	 * @exception if RSID is null or not found
	 */
	private function get_rsid($query){
		date_default_timezone_set('UTC');

		$params = array(
				'q' => $query->get_query(),
				'from' => date('Y-m-d H:i:s', $query->get_from_timestamp()),
				'until' => date('Y-m-d H:i:s', $query->get_to_timestamp()),
				'order' => $query->get_order(),
				'size' => $query->get_size()
			       );

		$result = $this->makeRequest('apiv2/search', $params);
		$rsid = json_decode($result, true);
		$rsid = $rsid['rsid']['id'];

		if(empty($rsid)){
			throw new \Exception("RSID not found");
		}else{
			return $rsid;
		}
	}

	/**
	 * @param string path
	 * @param array key-value params
	 * @param string method of request
	 * @return json result
	 * @exception if failed curl or invalid json result
	 */
	public function makeRequest($path, $params = null, $method = 'GET') {

		if ($path[0] !== '/') {
			$path = '/' . $path;
		}

		$method = strtoupper($method);
		$url = sprintf('https://%s.%s%s', $this->account, $this->domain, $path);

		$curl = curl_init();
		if ($method === 'POST') {
			curl_setopt($curl, CURLOPT_POST, 1);
		}else if ($method === 'PUT' || $method === 'DELETE') {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		}

		curl_setopt($curl, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

		if ($params) {
			$segments = array();
			foreach ($params as $k => $v) {
				$segments[] .= $k . '=' . urlencode($v);
			}
			$qs = join($segments, '&');
			if ($method === 'POST' || $method === 'PUT') {
				curl_setopt($curl, CURLOPT_POSTFIELDS, $qs);
			} else {
				$url .= '?' . $qs;
			}
		}

		curl_setopt($curl, CURLOPT_URL, $url);

		if ($method === 'PUT') {
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($qs))); 
		}

		$result = curl_exec($curl);
		if (!$result) {
			throw new \Exception(curl_error($curl));
		}

		if (!json_decode($result, true)) {
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
			if ($status >= 200 && $status <= 299) {
				return null;
			}
			curl_close($curl);
			throw new \Exception($result);
		}

		curl_close($curl);
		return $result;
	}
}

?>
