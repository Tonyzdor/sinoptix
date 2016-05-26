<?php 
require_once "HTTP/Request.php";
//require_once "getforecastlist.php";
require_once "medoo/db.php";


// Важно: данные отправляем POST-методом
$req =& new HTTP_Request("https://api.direct.yandex.ru/live/v4/json/");
$req->setMethod(HTTP_REQUEST_METHOD_POST);

// Инициализация параметров для авторизации
$data = array(
	token => "AQXo-9sAAeUoUDZfeHz7Tbqc3zf-zXC5Vw",
	token_type => "bearer",
	expires_in => "31536000"
);

$geoForForecast = $database->select("regions", [
			"region_id",
		]);

$phrases = array('Купить айфон');
$data['method'] = "CreateNewForecast";
$data['param']['Phrases'] = $phrases;

while (!empty($geoForForecast)) {
	 	
	foreach ($geoForForecast as $key => $value) {
		$geoArray = array($value['region_id']);
		$data['param']['GeoID'] = $geoArray;
		$json_data = json_encode($data);
		$req->addRawPostData($json_data);
		$response = $req->sendRequest();
		$errmsg = PEAR::isError($response);
		if (! $errmsg) {
			$result = $req->getResponseBody();
			$decoded_result = json_decode($result);
				if (isset($decoded_result->data)) {
				//var_dump($decoded_result);
				$database->insert('forecast_list', [
					'forecast_id'		=> $decoded_result->data,
					'phrases'			=> $phrases,
					'region_id'			=> $value['region_id'],
				]);
				unset($geoForForecast[$key]);
				//sleep(3);
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
	}
	//sleep(10);
	$time = date("H:i:s");
	echo '<br>';
	echo $time;
	echo '<br>';
	sleep(5);
}
?>