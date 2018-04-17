<?php
/**
----------------------------------------------------------------------------------------------------------------
HTTP Simple Queue Service - httpsqs client class for PHP v1.7.1

Author: Zhang Yan (http://blog.s135.com), E-mail: net@s135.com
This is free software, and you are welcome to modify and redistribute it under the New BSD License
----------------------------------------------------------------------------------------------------------------
Useage:
<?php
include_once("httpsqs_client.php");
$httpsqs = new httpsqs($httpsqs_host, $httpsqs_port, $httpsqs_auth, $httpsqs_charset);
$result = $httpsqs->put($queue_name, $queue_data); //1. PUT text message into a queue. If PUT successful, return boolean: true. If an error occurs, return boolean: false. If queue full, return text: HTTPSQS_PUT_END
$result = $httpsqs->get($queue_name); //2. GET text message from a queue. Return the queue contents. If an error occurs, return boolean: false. If there is no unread queue message, return text: HTTPSQS_GET_END
$result = $httpsqs->gets($queue_name); //3. GET text message and pos from a queue. Return example: array("pos" => 7, "data" => "text message"). If an error occurs, return boolean: false. If there is no unread queue message, return: array("pos" => 0, "data" => "HTTPSQS_GET_END")
$result = $httpsqs->status($queue_name); //4. View queue status
$result = $httpsqs->status_json($queue_name); //5. View queue status in json. Return example: {"name":"queue_name","maxqueue":5000000,"putpos":130,"putlap":1,"getpos":120,"getlap":1,"unread":10}
$result = $httpsqs->view($queue_name, $queue_pos); //6. View the contents of the specified queue pos (id). Return the contents of the specified queue pos.
$result = $httpsqs->reset($queue_name); //7. Reset the queue. If reset successful, return boolean: true. If an error occurs, return boolean: false
$result = $httpsqs->maxqueue($queue_name, $num); //8. Change the maximum queue length of per-queue. If change the maximum queue length successful, return boolean: true. If  it be cancelled, return boolean: false
$result = $httpsqs->synctime($num); //9. Change the interval to sync updated contents to the disk. If change the interval successful, return boolean: true. If  it be cancelled, return boolean: false
?>
----------------------------------------------------------------------------------------------------------------
*/

/**
 *
 * HttpSQS连接库
 * @package sdk.src.framework.mq
 * @author ianzhang
 * @version $Id: HttpSQS.class.php 2542 2013-01-17 06:23:30Z ianzhang $
 */
class HttpSQS
{
	public $httpsqs_host;
	public $httpsqs_port;
	public $httpsqs_auth;
	public $httpsqs_charset;

	private $curl;
	private $url;
	private $optionArray;

	private $needle = "\r\n";
	private $needleLength = 2;

	public function __construct($host='127.0.0.1', $port=1218, $auth='', $charset='utf-8')
	{
		$this->httpsqs_host = $host;
		$this->httpsqs_port = $port;
		$this->httpsqs_auth = $auth;
		$this->httpsqs_charset = $charset;

		$this->curl = curl_init();
		if (FALSE === $this->curl)
		{
			throw new TMException('fail to create crul for httpsqs');
		}
		$this->url = 'http://'.$host;
		$option_array = array();
		$option_array[CURLOPT_PORT] = $this->httpsqs_port;
		$option_array[CURLOPT_HEADER] = 1;
		$option_array[CURLOPT_RETURNTRANSFER] = 1;
		$this->optionArray = $option_array;
	}

	public function __destruct()
	{
		curl_close($this->curl);
	}

    public function http_get($query)
    {
    	$option_array = $this->optionArray;
    	$option_array[CURLOPT_URL] = $this->url . $query;
    	$option_array[CURLOPT_HTTPHEADER] = array('Expect:');
    	curl_setopt_array($this->curl, $option_array);
    	$data = curl_exec($this->curl);

    	if(curl_errno($this->curl) != 0)
    	{
    		throw new TMRemoteException("Remote visiting is failed: ".curl_error($this->curl));
    	}

    	return $this->processResult($data);
    }

    public function http_post($query, $body)
    {
    	$option_array = $this->optionArray;
    	$option_array[CURLOPT_URL] = $this->url . $query;
    	$option_array[CURLOPT_POST] = 1;
    	$option_array[CURLOPT_POSTFIELDS] = $body;
    	$option_array[CURLOPT_HTTPHEADER] = array('Expect:');
    	curl_setopt_array($this->curl, $option_array);
    	$data = curl_exec($this->curl);

    	if(curl_errno($this->curl) != 0)
    	{
    		throw new TMRemoteException("Remote visiting is failed: ".curl_error($this->curl));
    	}

    	return $this->processResult($data);
    }

    private function processResult($data)
    {
    	$lastIndex = 0;
    	$index = strpos($data, $this->needle);
        $pos_value = 0;
    	list($proto, $rcode, $result) = explode(" ", trim(substr($data, 0, $index)));
    	$len = -1;

    	for (;;)
    	{
    		$lastIndex = $index + $this->needleLength;
    		$index = strpos($data, $this->needle, $lastIndex);
    		$line = trim(substr($data, $lastIndex, $index - $lastIndex));
    		if ($line == "")
    			break;
    		if (strstr($line, "Content-Length:"))
    		{
    			list($cl, $len) = explode(" ", $line);

    		}
    		if (strstr($line, "Pos:"))
    		{
    			list($pos_key, $pos_value) = explode(" ", $line);
    		}
    	}
    	if ($len <= 0)
    	{
    		return false;
    	}

    	$body = substr($data, $index + $this->needleLength);

    	$result_array["pos"] = (int)$pos_value;
    	$result_array["data"] = $body;
    	return $result_array;
    }

    public function put($queue_name, $queue_data)
    {
    	$result = $this->http_post("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=put", $queue_data);
		if ($result["data"] == "HTTPSQS_PUT_OK") {
			return true;
		} else if ($result["data"] == "HTTPSQS_PUT_END") {
			return $result["data"];
		}
		return false;
    }

    public function get($queue_name)
    {
    	$result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=get");
		if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false) {
			return false;
		}
        return $result["data"];
    }

    public function gets($queue_name)
    {
    	$result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=get");
		if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false) {
			return false;
		}
        return $result;
    }

    public function status($queue_name)
    {
    	$result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=status");
		if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false) {
			return false;
		}
        return $result["data"];
    }

    public function view($queue_name, $queue_pos)
    {
    	$result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=view&pos=".$queue_pos);
		if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false)
		{
			return false;
		}
        return $result["data"];
    }

    public function reset($queue_name)
    {
    	$result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=reset");
		if ($result["data"] == "HTTPSQS_RESET_OK") {
			return true;
		}
        return false;
    }

    public function maxqueue($queue_name, $num)
    {
    	$result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=maxqueue&num=".$num);
		if ($result["data"] == "HTTPSQS_MAXQUEUE_OK") {
			return true;
		}
        return false;
    }

    public function status_json($queue_name)
    {
    	$result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=status_json");
		if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false) {
			return false;
		}
        return $result["data"];
    }

    public function synctime($num)
    {
    	$result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=httpsqs_synctime&opt=synctime&num=".$num);
		if ($result["data"] == "HTTPSQS_SYNCTIME_OK") {
			return true;
		}
        return false;
    }
}
?>