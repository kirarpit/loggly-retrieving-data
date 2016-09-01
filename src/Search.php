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

class Search {

	const BATCH_SIZE = 5000;// There is currently a maximum limit of 5000 individual events returned per search query

	/** @var string */
	private $query;

	/** @var string */
	private $from_timestamp;

	/** @var string */
	private $to_timestamp;

	/** @var string */
	private $size;

	/** @var string */
	private $order;

	function __construct($search_options) {
		$this->size = '1000';
		$this->order = 'desc';

		if(!isset($search_options['query']) || !isset($search_options['from_timestamp']) || !isset($search_options['to_timestamp'])){
			throw new \Exception("Min parameters requirement not fulfilled");
		}else{
			$this->set_query($search_options['query']);
			$this->set_from_timestamp($search_options['from_timestamp']);
			$this->set_to_timestamp($search_options['to_timestamp']);
		}

		if(array_key_exists('size', $search_options) && !empty($search_options['size'])){
			$this->set_size($search_options['size']);
		}

		if(array_key_exists('order', $search_options) && !empty($search_options['order'])){
			$this->set_order($search_options['order']);
		}
	}

	/**
	 * @param string query
	 * @return bool
	 */
	public function set_query($query){
		return $this->query = $query;
	}

	/**
	 * @return null|string
	 */
	public function get_query(){
		return $this->query;
	}

	/**
	 * @param string timestamp
	 * @return bool
	 * @exception if not a valid unix timestamp
	 */
	public function set_from_timestamp($timestamp){
		if($this->isValidTimeStamp($timestamp)){
			return $this->from_timestamp = $timestamp;
		}else{
			throw new \Exception("Not a valid unix timestamp");
		}
	}

	/**
	 * @return null|string
	 */
	public function get_from_timestamp(){
		return $this->from_timestamp;
	}

	/**
	 * @param string timestamp
	 * @return bool
	 * @exception if not a valid unix timestamp
	 */
	public function set_to_timestamp($timestamp){
		if($this->isValidTimeStamp($timestamp)){
			return $this->to_timestamp = $timestamp;
		}else{
			throw new \Exception("Not a valid unix timestamp");
		}
	}

	/**
	 * @return null|string
	 */
	public function get_to_timestamp(){
		return $this->to_timestamp;
	}

	/**
	 * @param string number of rows
	 * @return bool
	 * @exception if not a valid number or if number exceeds the max limit.
	 */
	public function set_size($no_of_rows){
		if(is_numeric($no_of_rows)){
			if($no_of_rows <= self::BATCH_SIZE){
				return $this->size = $no_of_rows;
			}else{
				throw new \Exception("Can't be greater than 5000");
			}
		}else{
			throw new \Exception("Not a valid number");
		}
	}

	/**
	 * @return string
	 */
	public function get_size(){
		return $this->size;
	}

	/**
	 * @param string order of result
	 * @return bool
	 * @exception if not either of 'asc' or 'desc' order
	 */
	public function set_order($order){
		if($order == 'desc' || $order == 'asc'){
			return $this->order = $order;
		}else{
			throw new \Exception("Could only be either of 'asc' or 'desc'");
		}
	}

	/**
	 * @return string
	 */
	public function get_order(){
		return $this->order;
	}

	/**
	 * @param string timestamp
	 * @return bool
	 */
	private function isValidTimeStamp($timestamp) {
		return ((string) (int) $timestamp === (string) $timestamp) 
			&& ($timestamp <= PHP_INT_MAX)
			&& ($timestamp >= ~PHP_INT_MAX);
	}

}

?>
