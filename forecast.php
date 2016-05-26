<?php 
require_once "HTTP/Request.php";
// Важно: данные отправляем POST-методом
$req =& new HTTP_Request("https://api.direct.yandex.ru/live/v4/json/");
$req->setMethod(HTTP_REQUEST_METHOD_POST);
// Инициализация параметров для авторизации
$data = array(
	token => "AQXo-9sAAeUoUDZfeHz7Tbqc3zf-zXC5Vw",
	token_type => "bearer",
	expires_in => "31536000"
);


$data['method'] = "CreateNewForecast";
$data['param'] = array('Phrases' => array('buy iphone','buy apple'), 'GeoID' => array(213));

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
		 // Обработка ответа метода
		 print_r($decoded_result);
		 //отдаём рез-т функции
		 return $decoded_result;
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