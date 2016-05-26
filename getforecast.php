<?php 
//https://tech.yandex.ru/direct/doc/dg-v4/live/GetForecast-docpage/

require_once "HTTP/Request.php";
require_once "medoo/db.php";

$req =& new HTTP_Request("https://api.direct.yandex.ru/live/v4/json/");
$req->setMethod(HTTP_REQUEST_METHOD_POST);

$forecastList = $database->select("forecast_list", [
			"forecast_id",
		], [
			"forecast_status" => 'Done',
		]);
$data = array(
	token => "AQXo-9sAAeUoUDZfeHz7Tbqc3zf-zXC5Vw",
	token_type => "bearer",
	expires_in => "31536000"
);
$data['method'] = "GetForecast";
foreach ($forecastList as $key) {
	$data['param'] = $key['forecast_id'];
	$json_data = json_encode($data);

	$req->addRawPostData($json_data);
	$response = $req->sendRequest();
	$errmsg = PEAR::isError($response);
	if (! $errmsg) {
		$result = $req->getResponseBody();
		$decoded_result = json_decode($result);

		if (isset($decoded_result->data)) {
			// Обработка ответа метода
			//var_dump($decoded_result->data->Phrases['0']->Phrase);
			foreach ($decoded_result->data->Phrases as $key) {
				echo $key->Phrase.'<br/>';
				echo $key->Min.'<br/>';
				echo $key->Max.'<br/>';
				echo $key->Max.'<br/>';
				echo $key->PremiumMin.'<br/>';
				echo $key->PremiumMax.'<br/>';
				echo $key->Shows.'<br/>';
				echo $key->Clicks.'<br/>';
				echo $key->FirstPlaceClicks.'<br/>';
				echo $key->PremiumClicks.'<br/>';
				echo $key->Phrase.'<br/>';

				echo '<br>';
				var_dump($key);
				echo '<br>';
				
				$time = date("H:i:s");
				echo '<br>';
				echo $time;
				echo '<br>';

				$database->insert("forecast_results", [
					"phrases" 			=> $key->Phrase,
					"min"				=> $key->Min,
					"max"				=> $key->Max,
					"premium_min"		=> $key->PremiumMin,
					"premium_max"		=> $key->PremiumMax,
					"shows"				=> $key->Shows,
					"clicks"			=> $key->Clicks,
					"first_place_clicks"=> $key->FirstPlaceClicks,
					"premium_clicks"	=> $key->PremiumClicks,
					"ctr"				=> $key->CTR,
					"first_place_ctr"	=> $key->FirstPlaceCTR,
					"premium_ctr"		=> $key->PremiumCTR,
					"forecast_id"		=> $data['param'],
					"region_id"			=> $key->Phrase,
				]);
				
		 	}
		 	//var_dump($decoded_result->data->Phrases[1]);


			//отдаём рез-т функции
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
	unset($data['param']);
}

?>