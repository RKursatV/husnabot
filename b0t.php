<?php
//ERROR HANDLING







error_reporting(E_ALL); 
ini_set('ignore_repeated_errors', TRUE); 
ini_set('display_errors', FALSE); 
ini_set('log_errors', TRUE); 
ini_set('error_log', __DIR__ . "/3rr0r5.log"); 
ini_set('log_errors_max_len', 1024); 

require_once("b0t.class.php");

$data = file_get_contents('php://input');
$data = json_decode($data, TRUE);

$husnab0t = new husna($data);
require_once("b0t.functions.php");
require_once("priv8.php");

$husnab0t->addCommand("husnacim","husnacimFunc");
$husnab0t->addCommand("hüsnacım","husnacimFunc");
$husnab0t->addCommand("bilgiad","bilgiadFunc",1);
$husnab0t->addCommand("mizahyab","mizahyabFunc",1);
$husnab0t->addCommand("fotoad","fotoadFunc",1);
$husnab0t->addCommand("yemekad","yemekteNeVar",1);
$husnab0t->addCommand("dolarad","dolaradFunc",1);
$husnab0t->addCommand("avroad","avroadFunc",0);
$husnab0t->addCommand("euroad","avroadFunc",1);
$husnab0t->addCommand("egonomiad","egonomiadFunc",1);
$husnab0t->addCommand("havadurumuad","havadurumuadFunc",1);
$husnab0t->addCommand("helb","helber",1);
$husnab0t->addCommand("help","helber");
$husnab0t->addCommand("/start","helber");
//$husnab0t->addCommand("neyesem","yemeksepeti"); deactivated as it does not word properly
$husnab0t->addCommand("gunaydin","gunadyinFunc");
$husnab0t->addCommand("günaydın","gunadyinFunc",1);
$husnab0t->addCommand("boşyapıyolar","bojyabmaFunc");
$husnab0t->addCommand("bojyapıyolar","bojyabmaFunc");
$husnab0t->addCommand("bosyapiyolar","bojyabmaFunc");
$husnab0t->addCommand("boşyapiyolar","bojyabmaFunc");
$husnab0t->addCommand("bojyapiyolar","bojyabmaFunc");
$husnab0t->addCommand("nasıl","beyle");
$husnab0t->addCommand("nasil","beyle");
$husnab0t->addCommand("java","jaava");
$husnab0t->addCommand("bilimsiz","bilimsiz");
$husnab0t->addCommand("muazzam","muazzam");
$husnab0t->addCommand("komutad","komutad");
$husnab0t->addCommand("oyunad","oyunadFunc",1);
$husnab0t->addCommand("so sad","despacito");
$husnab0t->addCommand("nani","nani");
$husnab0t->addCommand("shindeiru","omae");
$husnab0t->addCommand("dead","omae");
$husnab0t->addCommand("yaprak","yaprak");
$husnab0t->addCommand("secimad","secimAd");
/* PUT NEW COMMANDS BELOW */

$husnab0t->addCommand("geceler","iyiGeceler");
$husnab0t->addCommand("zlotiad","zlotyadFunc");

$husnab0t->addCommand("kursatad","kursatad");

/* PUT NEW COMMANDS ABOVE */

$husnab0t->process();
die("1");
?>
