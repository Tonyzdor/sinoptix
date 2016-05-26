<?php

	//подключаем всё что надо для запросов в базу и работы с апи
	require "allineedforcode.php";


	$data['method'] = "GetRegions";

	$json_data = json_encode($data);
	$decoded_result = json_decode($result);

	$req->addRawPostData($json_data);

	$response = $req->sendRequest();

	$errmsg = PEAR::isError($response);

	if (! $errmsg) {
	    $result = $req->getResponseBody();
	    $decoded_result = json_decode($result);
	    //var_dump($decoded_result);
	     if (isset($decoded_result->data)) {
	        //print_r($decoded_result->data);
	        //var_dump($decoded_result->data);

	        foreach ($decoded_result->data as $key) {

            	$database->insert('regions', [
		         	'region_id'       => $key->RegionID,
		         	'parent_id'       => $key->ParentID,
		         	'region_name'     => $key->RegionName,
		         	'region_type'     => $key->RegionType,
		        ]);
			};

	     } else if ($decoded_result->error_code) {
	         // Если ошибку вернул сервер API
	         echo "Error: code = ".$decoded_result->error_code
	                     .", str = ".$decoded_result->error_str
	                     .", detail = ".$decoded_result->error_detail;
	     } else {
	         echo "Unknown error";
	     }

	} else {
	    // Если ошибка произошла при попытке запроса
	    echo "Request error: ".$errmsg;
	}
?>