<?php 
	// process client request. via url

	header("Content-Type:application/json");
	include("functions.php");
	if (!empty($_GET['name'])){
		//
		$name = $_GET['name'];
		$price = get_price($name);
		
		if(empty($price))
		{
			// book not found
			deliver_response(200, "book not found", NULL);
		}else{
			// respond book price.
			deliver_response('200', "book found", $price);
		}
	}
	else
	{
		// throw invalid request.
		deliver_response(400,"Invalid Request", NULL);
		
	}
	
	function deliver_response($status, $status_message, $data)
	{
		header("HTTP/1.1 $status, $status_message ");
		
		$response['status'] = $status;
		$response['status_message'] = $status_message;
		$response['data'] = $data;
		
		$json_responce = json_encode($response);
		echo $json_responce;
		
		
	}
?>