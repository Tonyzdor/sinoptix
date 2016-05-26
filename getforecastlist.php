<?php 

//https://tech.yandex.ru/direct/doc/dg-v4/live/GetForecast-docpage/
require_once "HTTP/Request.php";
require_once "medoo/db.php";

$req =& new HTTP_Request("https://api.direct.yandex.ru/live/v4/json/");
$req->setMethod(HTTP_REQUEST_METHOD_POST);

		

$dataForListReq = array(
	token => "AQXo-9sAAeUoUDZfeHz7Tbqc3zf-zXC5Vw",
	token_type => "bearer",
	expires_in => "31536000"
);
$dataForListReq['method'] = "GetForecastList";

var_dump($dataForListReq);

$json_data = json_encode($dataForListReq);
$req->addRawPostData($json_data);

$response = $req->sendRequest();
$errmsg = PEAR::isError($response);
if (! $errmsg) {
	$result = $req->getResponseBody();
	$decoded_result = json_decode($result);
	if (isset($decoded_result->data)) {
	// Обработка ответа метода
		var_dump($decoded_result);
		foreach ($decoded_result as $decodedKey) {
			foreach ($decodedKey as $key) {
			$database->update("forecast_list", [
					"forecast_status" => $key->StatusForecast,
				], [
					"forecast_id" => $key->ForecastID,
				]);
			}
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