<?php


function husnaCurl($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch,CURLOPT_TIMEOUT,1000);
  $response = curl_exec($ch);
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

/* bilgiad Function STARTS */
function bilgiadFunc(){
        global $husnab0t;
        $ch = curl_init();
        $caller=$husnab0t->getFirstWord();
        $thread=trim($husnab0t->getOtherWords());
	$lang = ($caller == "bilgiad") ? "tr" : str_replace("bilgiadl","",$caller);

        if(strlen($thread) > 0) {
          $url = "https://$lang.wikipedi0.org/w/api.php?action=opensearch&search=".urlencode($thread)."&limit=7&namespace=0&format=json";
        }
        else {
          $url = "https://$lang.wikipedi0.org/w/api.php?format=json&action=query&prop=extracts&explaintext=&generator=random&grnnamespace=0&exlimit=max&exintro";
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 900);

        $response = curl_exec($ch);
        $response = json_decode($response, TRUE);
        curl_close($ch);

	if(strlen($thread) > 0){
		$result_count= count($response[1]);
		if(!$result_count) {$husnab0t->sendMessage("hojam boj yabmayın",1); return;}

		$husnab0t->sendMessage($response[0]." sorgusu için $result_count sonuç bulundu:",1);
		$yanit = "";
		for($i = 0; $i < $result_count; $i++){


			$yanit .= "<b>".$response[1][$i]."</b>\n".
			$response[2][$i]."\n".
			"Daha fazla bilgi için:\n".
			$response[3][$i]."\n----------------\n";

		}

		if (strlen($yanit) > 3500) {
			$messageparts = str_split($yanit, 3500);
			foreach($messageparts as $parts){
			$husnab0t->sendMessage_html($parts);
			}
		}
		else {$husnab0t->sendMessage_html($yanit); }

	} else {
		if (!$response || !array_value_recursive('extract', $response)) {
		  $husnab0t->sendMessage("hojam boj yabmayın",1);
		} else {
		  $husnab0t->sendMessage("*".array_value_recursive('title', $response)."*"."\n".array_value_recursive('extract', $response));
		}
	}

}
/* bilgiad Function ENDS */

/* mizahyab Function STARTS */
function mizahyabFunc()
{
        global $husnab0t;
        $response = husnaCurl("http://fikra.gen.tr/index.php");
        $result = "";
        preg_match_all ("/<div class=fikra_body >([^`]*?)<\/div>/", $response, $result);
        $ver = @$result[1][0];
        $ver =  mb_convert_encoding($ver,'UTF-8','ISO-8859-9');
        $ver = str_replace("<br />", "", $ver);
        if(trim($ver) == ""){
                mizahyabFunc();
        }
        if (strlen($ver) > 4000) {
                $messageparts = str_split($ver, 4004);
                foreach($messageparts as $parts){
                $husnab0t->sendMessage($parts);
                }
        }
        else{
                $husnab0t->sendMessage($ver);
        }
}
/* mizahyab Function ENDS */


/* fotoad Function STARTS */
function fotoadFunc(){
        global $husnab0t;
        $response = husnaCurl("http://www.funcage.com/?");
        $result = "";
        preg_match_all('/src="([^"]+)"/',$response, $result);
        $sonhal = "http://www.funcage.com".$result[1][1];
        $husnab0t->sendPhoto($sonhal);
}
/* fotoad Function ENDS */

/* yemekad Function STARTS */
function yemekteNeVar() {
        global $husnab0t;
        $others = trim($husnab0t->getOtherWords());
        date_default_timezone_set('Europe/Istanbul');
        $bak = date("N");
        $tomo = 0;
        $others = explode(" ",$others);
        if (in_array("yarın", $others) || in_array("yarin", $others) || in_array("geceler", $others)) {
          $bak = (date("N")+1) % 7;
          $tomo = 1;
        }
        $yemekURL = "https://kafeterya.metu.edu.tr/service.php";
        if($tomo) {
          $yemekURL .= "?tarih=".date("d-m-Y", strtotime('tomorrow'));
        }
        $response = husnaCurl($yemekURL);
        if ($bak > 5) {
          $yemekler = "Haftasonu yemek yok hojam \xF0\x9F\x98\x94";
        } else {
          $yemekler = "\xF0\x9F\x8D\xB4 Y";
          if ($tomo) {
            $yemekler .= "arın y";
          }
          $responseDecode = json_decode($response, true);
          $ogle = $responseDecode["ogle"];
          $aksam = $responseDecode["aksam"];
          $yemekler .= "emekte şunlar varmış hojam: \n\n *Öğle yemeği*\n ";
          for($i=0; $i<4; $i++) {
              $yemekler .= " · ".$ogle[$i]["name"]."\n ";
          }
          $yemekler .= "*Akşam yemeği*\n ";
          for($i=0; $i<4; $i++) {
              $yemekler .= " · ".$aksam[$i]["name"]."\n ";
          }
          $yemekler .= "\n\xF0\x9F\xA5\xAC *Vejetaryen* alternatifler de şunlarmış hojam: \n\n";
          $yemekler .= "*Öğle yemeği*\n · ".$ogle[4]["name"]."\n";
          $yemekler .= "*Akşam yemeği*\n · ".$aksam[4]["name"]."\n";
          $yemekler .="\n";
          $yemekler .= "Afiyet olsun hojam!";
        }
        $husnab0t->sendMessage($yemekler);
        $yemekler_array = array();
        for ($i=0; $i<5; $i++) {
            $yemekler_array[$i] = $ogle[$i]["name"];
            $yemekler_array[$i + 5] = $aksam[$i]["name"];
        }
        if (contains("BORONA", $yemekler_array)) {
          $husnab0t->sendMessage("Aaa bi dk hojam borona varmış menüde? \xf0\x9f\xa5\x95 \n BORONA BORONA \n AL BENI BORONA \n YAKISIRIZ AMA \n COK COK");
        }
}
/* yemekad Function ENDS */

/* dolarad Function STARTS */
function dolaradFunc() {
          global $husnab0t;
          $response = husnaCurl("https://www.bloomberght.com/doviz");
          preg_match_all("/<span data-type=\"son_fiyat\" class=\"LastPrice\" data-secid=\"USDTRY Curncy\">(.*?)<\/span>/msi", $response, $resultRegex);
          $message = "\xF0\x9F\x92\xB5 dolar şu an *".$resultRegex[1][0]."* ₺ hojam. \xF0\x9F\x92\xB8";
          $husnab0t->sendMessage($message);
}
/* dolarad Function ENDS */

/* avroad Function STARTS */
function avroadFunc() {
          global $husnab0t;
          $response = husnaCurl("https://www.bloomberght.com/doviz");
          preg_match_all("/<span data-type=\"son_fiyat\" class=\"LastPrice\" data-secid=\"EURTRY Curncy\">(.*?)<\/span>/msi", $response, $resultRegex);
          $message = "\xF0\x9F\x92\xB6 avro şu an *".$resultRegex[1][0]."* ₺ hojam. \xF0\x9F\x92\xB6";
          $husnab0t->sendMessage($message);
}
/* avroad Function ENDS */

/* egonomiad Function STARTS */
function egonomiadFunc() {
          global $husnab0t;
          $response = husnaCurl("https://www.bloomberght.com/doviz");
          preg_match_all("/<span data-type=\"son_fiyat\" class=\"LastPrice\" data-secid=\"USDTRY Curncy\">(.*?)<\/span>/msi", $response, $resultRegex);
          $message = "\xF0\x9F\x92\xB5 dolar şu an *".$resultRegex[1][0]."* ₺. \xF0\x9F\x92\xB8";
          preg_match_all("/<span data-type=\"son_fiyat\" class=\"LastPrice\" data-secid=\"EURTRY Curncy\">(.*?)<\/span>/msi", $response, $resultRegex);
          $message = $message."\n"."\xF0\x9F\x92\xB6 avro şu an *".$resultRegex[1][0]."* ₺. \xF0\x9F\x92\xB6";
          preg_match_all("/<span data-type=\"son_fiyat\" class=\"LastPrice\" data-secid=\"XU100 Index\">(.*?)<\/span>/msi", $response, $resultRegex);
          $message = $message."\n"."\xF0\x9F\x93\x88 borsa endeksi şu an *".$resultRegex[1][0]."*. \xF0\x9F\x93\x88";
          preg_match_all("/<span data-type=\"son_fiyat\" class=\"LastPrice\" data-secid=\"EURUSD Curncy\">(.*?)<\/span>/msi", $response, $resultRegex);
          $message = $message."\n"."\xF0\x9F\x8F\xA7 avro/dolar paritesi şu an *".$resultRegex[1][0]."*. \xF0\x9F\x8F\xA7";
          preg_match_all("/<span data-type=\"son_fiyat\" class=\"LastPrice\" data-secid=\"TAHVIL2Y\">(.*?)<\/span>/msi", $response, $resultRegex);
          $message = $message."\n"."faiz şu an *%".$resultRegex[1][0]."*.";
          preg_match_all("/<span data-type=\"son_fiyat\" class=\"LastPrice\" data-secid=\"XAU Curncy\">(.*?)<\/span>/msi", $response, $resultRegex);
          $message = $message."\n"."altın/ons şu an *".$resultRegex[1][0]."* ₺.";
          preg_match_all("/<span data-type=\"son_fiyat\" class=\"LastPrice\" data-secid=\"CO1 Comdty\">(.*?)<\/span>/msi", $response, $resultRegex);
          $message = $message."\n"."brent şu an *".$resultRegex[1][0]."* \$.";
          $message = $message."\n\n"."egonomi çoh iyi hojam.";
          $husnab0t->sendMessage($message);
}
/* egonomiad Function ENDS */

/* havadurumuad Function BEGINS*/
function havadurumuadFunc() {

          global $husnab0t;

          $obj1 = trim($husnab0t->getOtherWords());
          $obj = explode(" ", $obj1);

          if(count($obj) == 1 && $obj[0] == "") {
            $city = "cankaya";
            $response = husnaCurl("http://api.openweathermap.org/data/2.5/weather?q=cankaya&units=metric&appid=ba5f09dae8faa0fc1545ce998d86a0ed&lang=tr");
          }
          else {
            $city = $obj[0];
            $response = husnaCurl("http://api.openweathermap.org/data/2.5/weather?q=".$city."&units=metric&appid=ba5f09dae8faa0fc1545ce998d86a0ed&lang=tr");
          }
          $response = json_decode($response,TRUE);
          if(@$response["cod"] == "404"){
              $message = "$city diye bi yer yok hojam.";
          } else {
              $havadurumu = $response["weather"][0]["description"];
              $sicaklik = $response["main"]["temp"];

              if(strlen($city) > 1) {
                $message = $city." konumunda hava durumu *".$havadurumu."* ve sıcaklık *".$sicaklik."* derece gözüküyor.";
                $message = $message."\n"."hava çoh iyi hojam.";
              }
          }
          $husnab0t->sendMessage($message);
      }
/* havadurumuad Function ENDS*/

/* yemeksepeti function BEGINS*/

function yemeksepeti() {

          global $husnab0t;

          $others=trim($husnab0t->getOtherWords());
          $others=explode(" ",$others);
          $othersC=count($others);
          $minTutar = 0;
          $coksatan = 0;
          if(in_array("popi", $others) || in_array("popı", $others)) {
            $coksatan = 1;
          }
          if(($othersC > 1 && $coksatan == 1) || ($othersC == 1 && $coksatan == 0)) {
            $minTutar=$others[$coksatan];
          }
          $kampusteki="https://www.yemeksepeti.com/ankara/orta-dogu-teknik-universitesi-odtu-kampusu#sof:2|sob:true";
          if($minTutar) {
            $kampusteki.="|mbt:".$minTutar;
          }
          $simdiAcik=husnaCurl($kampusteki);
          #preg_match_all('/<a class="restaurantName withTooltip" href="(.*?)" target="_parent">/msi', $simdiAcik, $restorantlar);
          preg_match_all('/<a class="restaurantName withTooltip" href="(.*?)" target="_parent">(.*?)<span data-tooltip="(.*?)MinimumDeliveryPrice&quot;:(.*?),&quot(.*?)">/msi', $simdiAcik, $restorantlar);
          #1- links, 4- min delivery limits
          if($minTutar) {
            $restorantlarYeni=array();
            $Yenisay=count($restorantlar[1]);
            for($i=0;$i<$Yenisay;$i++) {
              if($restorantlar[4][$i] < $minTutar) {
                array_push($restorantlarYeni,array($restorantlar[0][$i],$restorantlar[1][$i]));
              }
            }
            $restorantlar=$restorantlarYeni;
          }

          $restorantSay=count($restorantlar[1]);
          $restorantSec=rand(1,$restorantSay)-1;
          $restorant="https://www.yemeksepeti.com".$restorantlar[1][$restorantSec];
          $menuGetir=husnaCurl($restorant);
          preg_match_all('/<meta name="twitter:title" content="(.*?), Ankara Online/msi', $menuGetir, $restorantAdi);

          if($coksatan) {
            preg_match_all('/<div class="productName">(.*?)<a href="javascript\:void\(0\)\;" data-catalog-name="TR_ANKARA" class="getProductDetail" data-product-id="(.*?)" data-category-name="(.*?)" data-top-sold-product="true">(.*?)<\/a>(.*?)<\/div>(.*?)<span class="productInfo">(.*?)<p>(.*?)<\/p>(.*?)<\/span>(.*?)<span class="pull-right newPrice">(.*?)<\/span>/msi', $menuGetir, $yemekler);
            $menuSay=count($yemekler[4]);
          }
          else {
            preg_match_all('/<div class="productName">(.*?)<a href="javascript\:void\(0\)\;" data-catalog-name="TR_ANKARA" class="getProductDetail" data-product-id="(.*?)" data-category-name="(.*?)" data-top-sold-product="(.*?)">(.*?)<\/a>(.*?)<\/div>(.*?)<span class="productInfo">(.*?)<p>(.*?)<\/p>(.*?)<\/span>(.*?)<span class="pull-right newPrice">(.*?)<\/span>/msi', $menuGetir, $yemekler);
            $menuSay=count($yemekler[5]);
          }

          $yemekSec=rand(1,$menuSay)-1;

          if($coksatan) {
            $yemek=$yemekler[4][$yemekSec];
            $icerik=$yemekler[8][$yemekSec];
            $fiyat=$yemekler[11][$yemekSec];
          }
          else {
            $yemek=$yemekler[5][$yemekSec];
            $icerik=$yemekler[9][$yemekSec];
            $fiyat=$yemekler[12][$yemekSec];
          }
          @preg_match_all('/<i class="ys-icons ys-icons-foto" data-imagepath="(.*?)" data-productname="(.*?)"><\/i>/msi', $yemek, $yemekozel);
          if(strlen($yemekozel[2][0]) > 2) {
            $yemek=$yemekozel[2][0];
          }
          $yemek=html_entity_decode($yemek);
          $sonuc="hojam bence *".$restorantAdi[1][0]."* mekanından *".$yemek."* yiyin. ";
          if(strlen($icerik) > 0) {
            $icerik=html_entity_decode($icerik);
            $sonuc.= "içinde *".$icerik."* var, ";
          }
          $sonuc.= "fiyatı da *".$fiyat."*, güzel bence. şuradan direkt sipariş verebilirsiniz: $restorant";


          $husnab0t->sendMessage($sonuc);

}


/* yemeksepeti function ENDS*/

function helber(){
	global $husnab0t;
  $husnab0t->sendMessage($husnab0t->getWhoamI());

}


function komutad($auto=0){
	global $husnab0t;
	if (!$husnab0t->getGroupOrPrivate()){
	$lista=array_chunk(array_keys($husnab0t->getMenu()), (ceil(count(array_keys($husnab0t->getMenu()))/3)));
	$replyMarkup = array(
    'keyboard' => $lista,
	);
	$encodedMarkup = json_encode($replyMarkup);
	$husnab0t->sendMessage_w_markup("komut seç bro",$encodedMarkup);}
	elseif (!$auto){
		$husnab0t->sendMessage("bu fonksiyon sadece özel mesajda çalışıyor",1);
	}
}

/* gunaydin Function STARTS	*/
function gunadyinFunc(){
	global $husnab0t;
	$husnab0t->sendMessage("hepinize günaydınlar :)");
	havadurumuadFunc();
	yemekteNeVar();
	egonomiadFunc();
}

/* gunaydin Function ENDS */

/* bojyabmaFunc Function STARTS*/
function bojyabmaFunc(){
	global $husnab0t;
	$husnab0t->sendPhoto("https://s2.eksiup.com/f4efffa4d670.jpeg","",1);
}
/* bojyabmaFunc Function ENDS */

/* muazzam Function STARTS*/
function muazzam(){
	global $husnab0t;
	$husnab0t->sendPhoto("https://s3.eksiup.com/66e49a926940.jpg","",1);
}
/* muazzam Function ENDS */

/* bilimsiz Function STARTS*/
function bilimsiz(){
	global $husnab0t;
	$husnab0t->sendPhoto("https://s3.eksiup.com/eb5ffea94370.jpg","",1);
}
/* bilimsiz Function ENDS */

/* java Function STARTS*/
function jaava(){
	global $husnab0t;
	$husnab0t->sendPhoto("https://s3.eksiup.com/4aed51458925.jpg","",1);
}
/* java Function ENDS */

/* beyle Function STARTS*/
function beyle(){
	global $husnab0t;
	$husnab0t->sendPhoto("https://s3.eksiup.com/fc55f421b312.jpg","",1);
}
/* beyle Function ENDS */

/* spam Function STARTS*/
function spamlamayin(){
	global $husnab0t;
	$husnab0t->sendMessage("hojam botu spamlamayin",1);
}
/* spam Function ENDS */

/* husnacim Function STARTS*/
function husnacimFunc() {
  global $husnab0t;
  $husnab0t->sendMessage("yeter ki sen iste",1);
}

function contains($str, array $arr) {
  foreach($arr as $a) {
    if (stripos($a,$str) !== false) return true;
  }
  return false;
}

/*oyunad function STARTS*/
function oyunfiyatiAd($url2){
    global $husnab0t;
    $tablex="";
    $rows="";
    $price="";
    $yok="bu oyun artik yok hodjam";
    $response=husnaCurl($url2);
    preg_match_all("/<table.*?>(.*?)<\/table>/si", $response, $tablex);
    if(preg_match_all("/<tr.*?>(.*?)<\/tr>/si", $tablex[1][1], $rows)){
        if(preg_match_all("/<td.*?>(.*?)<\/td>/si", $rows[1][14], $price)){
            return @$price[0][1];


        }
        else return $yok;
    }

    else{

        return false;
    }

}

function oyunadFunc(){
    global $husnab0t;

    $caller=$husnab0t->getFirstWord();
    $thread='';
    if($caller == "oyunad") {
        $thread=trim($husnab0t->getOtherWords());
    }
    if(strlen($thread) <= 0) {
        return false;
    }
    $url="https://steamdb.info/search/?a=app&q=";
    $url.=$thread;
    $table="";
    $rows="";
    $id="";
    $response=husnaCurl($url);
    $message="";
    $price="";
	if(strpos($response,"Nothing was found")){
		$husnab0t->sendMessage("Oyunu bulamadık hocam :/ \n");
		return;
	}
    preg_match_all("/<table.*?>(.*?)<\/table>/si", $response, $table);

    preg_match_all("/<tr.*?>(.*?)<\/tr>/si", $table[1][0], $rows);
    $i=2;

    $extension="";
    preg_match_all("/<td.*?>(.*?)<\/td>/si", $rows[1][$i], $id);
    preg_match_all("/<a href.*?>(.*?)<\/a>/si", $id[0][0], $extension);
    if (strlen($id[0][1])==13) $id[0][1]=substr($id[0][1],4,4);
    else if(strlen($id[0][1])==12) $id[0][1]=substr($id[0][1],4,3);

    if($id[0][1]=="DLC" || $id[0][1]=="Game"){
          $id[0][1]=strip_tags($id[0][1]);//type
          $id[0][2]=strip_tags($id[0][2]);//name
          $url2="https://steamdb.info/app/";
          $url2.=$extension[1][0]."/";
          $price.=oyunfiyatiAd($url2);
          $price=strip_tags($price);
          $message.="Type : ".$id[0][1]." Name : ".$id[0][2]." Price : ".$price;
          $message=html_entity_decode($message);

          $husnab0t->sendMessage($message."\n");


    }
}
/*oyunad function ENDS*/

/*despacito */
function despacito() {
  global $husnab0t;
  $husnab0t->sendVoiceMessage("http://husnabot.mozan.xyz/despacito.ogg","",1);
}
/* despacito ends */

/*nani */
function nani() {
  global $husnab0t;
  $husnab0t->sendVoiceMessage("http://husnabot.mozan.xyz/nanii.ogg","",1);
}
/* nani ends */

/*omae */
function omae() {
  global $husnab0t;
  $husnab0t->sendVoiceMessage("http://husnabot.mozan.xyz/shi.ogg","",1);
}
/* omae ends */
/*yaprak */
function yaprak() {
  global $husnab0t;
  $husnab0t->sendVoiceMessage("https://kursat.space/b0t/audio/yaprak.ogg","",1);
}
/* yaprak ends */
/* PUT NEW FEATURES BELOW */

/*iyi geceler starts*/

function iyiGeceler() {
  global $husnab0t;
  date_default_timezone_set('Europe/Istanbul');
  $bak=date("H");
  if($bak < 21 && $bak > 6) {
    $husnab0t->sendMessage("hangi saat diliminde yaşıyorsunuz hojam siz :/",1);
  }
  else {
    $husnab0t->sendMessage("iyi geceler hocccam (:");
    yemekteNeVar();
    egonomiadFunc();
  }
}

/*iyi geceler ends*/

/*zlotyad starts*/

function zlotyadFunc() {
  global $husnab0t;
  $response = husnaCurl("https://m.tr.investing.com/currencies/pln-try");
  preg_match_all('/<span class=\"lastInst pid.*?\">\s*?(\S*?)\s*?<\/span>/msi', $response, $resultRegex);
  $message = "\xF0\x9F\x87\xB5\xF0\x9F\x87\xB1 zloty şu an **".$resultRegex[1][0]."** ₺ hojam. \xF0\x9F\x87\xB9\xF0\x9F\x87\xB7";
  $husnab0t->sendMessage($message);
}

/*zlotyad ends*/


/*secimAd starts*/

function secimAdCurl($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch,CURLOPT_TIMEOUT,1000);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $response = curl_exec($ch);
  curl_close($ch);
  return $response;
}

function secimAd() {
  global $husnab0t;
  if($husnab0t->getOtherWords() == ""){
    $husnab0t->sendPhoto("https://s1.eksiup.com/4e8333a0a939.jpeg","",1);
  }
  else{
    $sehirSec=trim($husnab0t->getOtherWords());
    $sehirSec=explode(" ",$sehirSec);
    if(in_array("istanbul", $sehirSec) && !in_array("ankara", $sehirSec)) {
      $istanbulFile = secimAdCurl("https://www.haberturk.com/secim/secim2019/yerel-secim/sehir/istanbul-34");
      preg_match_all('/Pie = \$\("#sehirPartiPieChart"\);\s*var dataPie = {\s*labels: \[".*?(\S{6})",".*?(\S{6})",/', $istanbulFile, $resultRegex);
      $binaliPercentage = $resultRegex[2][0];
      $imamogluPercentage = $resultRegex[1][0];

      preg_match_all('/genel_durum_acilan_sandik_oran">(\S{4})/', $istanbulFile, $resultRegex);
      $sandikPercentage = $resultRegex[1][0];

      preg_match_all('/.data\("qtipData","İstanbul - (Zey|Beş).*?<br \/>.*? (\S{5})%"/', $istanbulFile, $resultRegex);
      $rizaPercentage = $resultRegex[2][0];
      $omerPercentage = $resultRegex[2][1];


      $message = "İSTANBUL BB BAŞKANLIĞI\n"
        ."Açılan Sandık: %".$sandikPercentage."\n"
        ."1.Aday: Ekrem İmamoğlu - CHP - ".$imamogluPercentage."\n"
        ."2.Aday: Binali Yıldırım - AKP - ".$binaliPercentage."\n"
        ."\n"
        ."Beşiktaş Belediye Başkanlığı: Rıza Akpolat - CHP - %".$rizaPercentage."\n"
        ."Zeytinburnu Belediye Başkanlığı: Ömer Arısoy - AKP - %".$omerPercentage."\n"
        ."alamanyadan sevgiler hojajım.";

        $husnab0t->sendMessage($message);
        $husnab0t->sendPhoto("https://s1.eksiup.com/4e8333a0a939.jpeg","",1);
    }
    elseif(!in_array("istanbul", $sehirSec) && in_array("ankara", $sehirSec)) {
      $ankaraFile = secimAdCurl("https://www.haberturk.com/secim/secim2019/yerel-secim/sehir/ankara-6");
      preg_match_all('/Pie = \$\("#sehirPartiPieChart"\);\s*var dataPie = {\s*labels: \[".*?(\S{6})",".*?(\S{6})",/', $ankaraFile, $resultRegex);
      $mansurPercentage = $resultRegex[1][0];
      $ozhasekiPercentage = $resultRegex[2][0];

      preg_match_all('/genel_durum_acilan_sandik_oran">(\S{4})/', $ankaraFile, $resultRegex);
      $sandikPercentage = $resultRegex[1][0];

      preg_match_all('/.data\("qtipData","Ankara - (Çank|Yeni).*?<br \/>.*? (\S{5})%"/', $ankaraFile, $resultRegex);
      $alperPercentage = $resultRegex[2][0];
      $fethiPercentage = $resultRegex[2][1];

      $message = "ANKARA BB BAŞKANLIĞI\n"
        ."Açılan Sandık: %".$sandikPercentage."\n"
        ."1.Aday: Mansur Yavaş - CHP - ".$mansurPercentage."\n"
        ."2.Aday: Mehmet Özhaseki - AKP - ".$ozhasekiPercentage."\n"
        ."\n"
        ."Çankaya Belediye Başkanlığı: Alper Taşdelen - CHP - %".$alperPercentage."\n"
        ."Yenimahalle Belediye Başkanlığı: Fethi Yaşar - CHP - %".$fethiPercentage."\n"
        ."alamanyadan sevgiler hojajım.";

        $husnab0t->sendMessage($message);
        $husnab0t->sendPhoto("https://s1.eksiup.com/4e8333a0a939.jpeg","",1);
    }
    else {
      $message = "sadece angara veya izdanbul hoja.";
      $husnab0t->sendMessage($message);
    }
  }
}

/*secimAd ends*/


/* PUT NEW FEATURES ABOVE */
