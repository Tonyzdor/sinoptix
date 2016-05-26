<?php 

//https://tech.yandex.ru/direct/doc/dg-v4/live/CreateNewForecast-docpage/

//подключаем всё что надо для запросов в базу и работы с апи
require "allineedforcode.php";

$phrases = array('buy iphone');
$GeoID = array(221);

$data['method'] = "CreateNewForecast";
$data['param']['Phrases'] = $phrases;
$data['param']['GeoID'] = $GeoID;
$json_data = json_encode($data);
echo '<br>';
var_dump($data);
echo '<br>';
$req->addRawPostData($json_data);
$response = $req->sendRequest();
$errmsg = PEAR::isError($response);
if (! $errmsg) {
	$result = $req->getResponseBody();
	$decoded_result = json_decode($result);
		if (isset($decoded_result->data)) {
				//записываем в базу
		$database->insert('forecast_list', [
			'forecast_id'		=> $decoded_result->data,
			'phrases'			=> $phrases,
			'region_id'			=> $GeoID,
		]);

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

echo '<br>';
var_dump($data);
echo '<br>';

?>