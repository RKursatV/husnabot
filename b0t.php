<?php 
require_once("connection.php");
$website = "https://api.telegram.org/bot".$botToken;
 
$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

/*
$output = print_r($update, true);
 
$ac = fopen("logx.txt","a+");
fwrite($ac,$output."\n");

fclose($ac);
*/

$chatId = $update["message"]["chat"]["id"];
$sender = $update["message"]["from"]["username"];
$firstname = $update["message"]["from"]["first_name"];
$lastname = $update["message"]["from"]["last_name"];
$message = $update["message"]["text"];






if ($update["message"]["new_chat_member"]["username"] == "HusnaBot") {
	sendVoiceMessage($chatId, "https://kursat.blog/b0t/audio/selam.ogg","hey bitchezzz");
	$veri = "ben hüsna b0t\nbilgiad yazarsan sana harika bilgiler getiririm\n####\nallambilgiad {söz öbeği} formatında şeyler söylersen de arar tarar senin için o şeyi bulurum\n####\ngetirhoca {id}|{ad-soyad} yazarsan senin için okulda o öğrenciyi ararım\n####\nmizahyab yazarsan senin için birbirinden eĞLenCeLi fıkralarımdan birisini anlatırım.\n####\nfotoad yazarsan senin için internetin derinliklerinden elde ettiğim görsellerimden birisini paylaşırım";
	$veri .="\n####\nhenüz yeni sayılırım mazur gör hoja";
	sendMessage($chatId,$veri);
	die();
}
elseif ($update["message"]["new_chat_member"]["username"]) {
	sendVoiceMessage($chatId, "https://kursat.blog/b0t/audio/selam.ogg","@".$update["message"]["new_chat_member"]["username"]);
	die();
}
elseif ($update["message"]["left_chat_participant"]["username"]) {
	
	sendVoiceMessage($chatId, "https://kursat.blog/b0t/audio/seriuzgunad.ogg",$update["message"]["left_chat_participant"]["first_name"]. " gitti. artık bir eksiğiz...");
	die();
}
elseif ($lastname == "Çaykuş"){

	sendVoiceMessage($chatId, "https://kursat.blog/b0t/audio/iyiyiy.ogg","",$update["message"]["message_id"]);
	die();
}
if($update["message"]["chat"]["type"] =="private") {
	if ($sender != "z4r4r") {
		sendMessage($chatId, "üzgünüm, sadece botlarla görüşüyorum");
		sendVoiceMessage($chatId, "https://kursat.blog/b0t/audio/hugz.ogg","f0r hugz and bugz @z4r4r");
		notify_owner("@".$sender." dedi ki: ". $message);
	}
	else{
		
			$parcala = explode(" ",$message,2);
			switch($parcala[0]) {
			        case "ekle":
			                $ac = fopen("todo.txt","a+");
							fwrite($ac,$parcala[1]."\n");
							fclose($ac);
							sendMessage($chatId, "todo eklendi");
					  break;
					case "napcaz":
							sendMessage($chatId, "takılmasak mı");
						break;		
			}




	}
}
else{


				$parcala = explode(" ",$message,2);
				$girmedi = 0;
				$parcala[0] = strtolower($parcala[0]);
				switch($parcala[0]) {
				        case "getirhoca":
				        $parcala[1] = trim($parcala[1]);
				        sendMessage($chatId, "getiriyorum hoca bekle");
				        if(strlen($parcala[1]) < 3){
				        	sendMessage($chatId, "en az üç hane gir hocaaa");
				        	break;
				        }

				        sendMessage($chatId, $parcala[1]);
				        


						$category_elems = array();

						if(is_numeric($parcala[1])){
							$param = "$parcala[1]%";
							$stmt = $con->prepare("SELECT department as category FROM students WHERE id  like ? GROUP BY category ORDER BY `students`.`department` DESC");
							$stmt ->bind_param("s",$param);
							$stmt->execute();
							$result = $stmt->get_result();
							$stmt->close();
							$categories = array();
							while($row = $result->fetch_assoc())
							{
								$stmt = $con->prepare("SELECT id as description,CONCAT(firstname,' ',lastname, ' | ', department) as title FROM students WHERE department = ? and id  like ? ORDER BY `students`.`department` DESC");
								$stmt ->bind_param("ss",$row["category"],$param);
								$stmt->execute();
								$resultx = $stmt->get_result();
								while ($rowx = $resultx->fetch_assoc()) {
									array_push($category_elems, $rowx);
								}
								$categories += array($row["category"]=>array("name"=>$row["category"],"results"=>$category_elems));
							}
						}
						else
						{
							$parcala[1] = urldecode($parcala[1]);
							$param = "%$parcala[1]%";
							$stmt = $con->prepare("SELECT department as category FROM students WHERE (CONCAT(firstname,' ',lastname) like ?) or department like ? GROUP BY category ORDER BY `students`.`department` DESC");
							$stmt ->bind_param("ss", $param,$param);
							$stmt->execute();
							$result = $stmt->get_result();
							$stmt->close();
							$categories = array();
							while($row = $result->fetch_assoc())
							{

								$stmt = $con->prepare("SELECT id as description,CONCAT(firstname,' ',lastname, ' | ', department) as title FROM students WHERE department = ? and (CONCAT(firstname,' ',lastname) like ?) ORDER BY `students`.`department` DESC");
								$stmt ->bind_param("ss",$row["category"],$param);
								$stmt->execute();
								$resultx = $stmt->get_result();
								
								while ($rowx = $resultx->fetch_assoc()) {
									//$rowx += array('url' => '/student/'.$rowx["description"] );
									array_push($category_elems, $rowx);
								}
								$categories += array($row["category"]=>array("name"=>$row["category"],"results"=>$category_elems));
							}
						}
						$ver = "#--->@$sender\n";
						foreach ($category_elems as $key => $value) {
							$ver = $ver .implode(" ", $value)."\n";
						}
						$ver .= "### @$firstname isteğinin sonu ###";
						if (strlen($ver) > 4000) {
							$messageparts = str_split($ver, 4004);
						    foreach($messageparts as $parts){
						    sendMessage($chatId,$parts);
						    }
						}
						else{
						sendMessage($chatId, $ver);
						}
						  break;



				case "bilgiad":
					$veri = wiki_getir();
					sendMessage($chatId, array_value_recursive('title', $veri));
					sendMessage($chatId, array_value_recursive('extract', $veri));
					break;
				case "allambilgiad":
					$veri = wiki_getir_ozel($parcala[1]);
					if (!array_value_recursive('extract', $veri)) {
						sendMessage($chatId,"hojam boj yabmayın");
					}else{
					sendMessage($chatId, array_value_recursive('extract', $veri));
					}
					break;
				case "fotoad":
					sendPhoto($chatId);

					break;
				case "mizahyab":
					mizahShow($chatId);

					break;
				case "help":

						$veri = "ben hüsna b0t\nbilgiad yazarsan sana harika bilgiler getiririm\n####\nallambilgiat {söz öbeği} formatında şeyler söylersen de arar tarar senin için o şeyi bulurum\n####\ngetirhoca {id}|{ad-soyad} yazarsan senin için okulda o öğrenciyi aratırım.\n####\nmizahyab yazarsan senin için birbirinden eĞLenCeLi fıkralarımdan birisini anlatırım.\n####\nfotoad yazarsan senin için internetin derinliklerinden elde ettiğim görsellerimden birisini paylaşırım";
						$veri .="\n####\nhenüz yeni sayılırım mazur gör hoja";
						sendMessage($chatId,$veri);


					break;
				case "dolarad":
					dolarShow($chatId);
						
					break;
				default:
					$girmedi = 1;
					break;
				}

				if ($girmedi == 1) {
					if (is_numeric(strpos($message, '?')) || is_numeric(strpos($message, '¿'))) {
						//
						sendMessage($chatId,"bu ne saçma soru amk",$update["message"]["message_id"]);
					} elseif (is_numeric(strpos($message, '!')) || is_numeric(strpos($message, '¡')))  {
						//
						sendMessage($chatId,"hoja sakin ol hoja",$update["message"]["message_id"]);
					}
					elseif (is_numeric(strpos($message, 'xd')) || is_numeric(strpos($message, 'xD'))) {
						sendVoiceMessage($chatId, "https://kursat.blog/b0t/audio/iyiyiy.ogg","",$update["message"]["message_id"]);
						
					}
						
						
				}
}






 
function sendMessage ($chatId, $message,$reply_to_message_id = 0) {
       	$reply = ($reply_to_message_id != 0) ? "&reply_to_message_id=".$reply_to_message_id: "";
        $url = $GLOBALS[website]."/sendMessage?chat_id=".urlencode($chatId)."&text=".urlencode($message).$reply;
        requEst($url);
       
}
function mizahShow ($chatId) {



		$ch = curl_init();
		$url = "http://fikra.gen.tr/index.php";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =curl_exec($ch);
		curl_close($ch);
		$result = "";

		preg_match_all ("/<div class=fikra_body >([^`]*?)<\/div>/", $response, $result);
		$ver = $result[1][0];

		$ver =  mb_convert_encoding($ver,'UTF-8','ISO-8859-9');

		$ver = str_replace("<br />", "", $ver);
		if (strlen($ver) > 4000) {
			$messageparts = str_split($ver, 4004);
		    foreach($messageparts as $parts){
		    sendMessage($chatId,$parts);
		    }
		}
		else{
			sendMessage($chatId, $ver);
		}
       
}

function dolarShow ($chatId) {



		$ch = curl_init();
		$url = "http://dolar.tlkur.com/";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =curl_exec($ch);
		curl_close($ch);
		$result = "";
		$mesajDolar = "dolar şimdi: ";
		preg_match_all ("/<td style="text-align:left;font-size:18px;. id=.USDTL_rate.>[0-9]\.[0-9][0-9][0-9][0-9]<\/td>/", $response, $result);
		$ver = $result[1][0];

		$ver =  mb_convert_encoding($ver,'UTF-8','ISO-8859-9');

		$ver = str_replace("<td style="text-align:left;font-size:18px;", "", $ver);
		$ver = str_replace('" id="USDTL_rate">', "", $ver);
		$ver = str_replace("</td>", "", $ver);
		$mesajDolar .= $ver;
		$mesajDolar .= "\nbu devirde bilgisayar alınmaz hocaaaa";
		
		sendMessage($chatId, $mesajDolar);
		
       
}



function sendPhoto ($chatId) {



		$ch = curl_init();
		$url = "http://www.funcage.com/?";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		$result = "";
		preg_match_all('/src="([^"]+)"/',$response, $result);

		$sonhal = "http://www.funcage.com".$result[1][1];
        $url = $GLOBALS[website]."/sendPhoto?chat_id=".urlencode($chatId)."&photo=".urlencode($sonhal);
        requEst($url);
       
}
function sendVoiceMessage ($chatId, $voice, $caption="",$reply_to_message_id = 0) {
       	$reply = ($reply_to_message_id != 0) ? "&reply_to_message_id=".$reply_to_message_id: "";
        $url = $GLOBALS[website]."/sendVoice?chat_id=".$chatId."&voice=".urlencode($voice)."&caption=".urlencode($caption).$reply;
        requEst($url);
}
function notify_owner($message){
	$url = $GLOBALS[website]."/sendMessage?chat_id=271982939&text=".urlencode($message);
    requEst($url);
}
function requEst($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		curl_exec($ch);
		curl_close($ch);
}
//https://tr.wikipedia.org/w/api.php?format=json&action=query&generator=random&grnnamespace=0&prop=revisions&rvprop=content&grnlimit=10
//https://tr.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles=Stack%20Overflow


//https://tr.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&explaintext=&generator=random&grnnamespace=0&exlimit=10



function wiki_getir($random = 0){
		$ch = curl_init();
		$url = "https://tr.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&explaintext=&generator=random&grnnamespace=0&exlimit=max&exintro";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$response = json_decode($response, TRUE);
		curl_close($ch);
		return $response;
}
//https://tr.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles=

function wiki_getir_ozel($veri){
		$ch = curl_init();
		$url = "https://tr.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles=".urlencode($veri);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$response = json_decode($response, TRUE);
		curl_close($ch);
		return $response;
}


function array_value_recursive($key, array $arr){
    $val = array();
    array_walk_recursive($arr, function($v, $k) use($key, &$val){
        if($k == $key) array_push($val, $v);
    });
    return count($val) > 1 ? $val : array_pop($val);
}
