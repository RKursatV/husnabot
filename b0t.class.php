<?php
require_once("settings.php");
class husna extends settings
{
    private $senderId;
    private $senderUsername;
    private $senderFirstName;
    private $senderLastName;
    private $message;
    private $firstWord;
    private $otherWords;
    private $queryTokens;
    private $messageId;
    private $chatId;
    private $userEnter;
    private $userExit;
    private $groupOrPrivate;
    private $reqUrl;
    private $commands = array();
    private $menu_elems = array();
    private $whoamI;
	private $groupName;
    public function __construct($data)
    {
        $this->senderUsername = @$data["message"]["from"]["username"];
        $this->senderFirstName = $data["message"]["from"]["first_name"];
        $this->senderLastName = @$data["message"]["from"]["last_name"];
	$this->senderId = @$data["message"]["from"]["id"];
        $this->message = ($data["message"]["text"]) ? mb_strtolower($data["message"]["text"]) : "";
        $this->firstWord = explode(" ",$this->message,2)[0];
        $this->otherWords = (isset(explode(" ",$this->message,2)[1])) ? explode(" ",$this->message,2)[1] : "";
        $this->queryTokens = explode(" ", $this->message);
        $this->messageId = @$data["message"]["message_id"];
        $this->chatId = @$data["message"]["chat"]["id"];
	if(isset($data["message"]["new_chat_member"])){
        $this->userEnter = (isset($data["message"]["new_chat_member"]["username"])) ? "@".$data["message"]["new_chat_member"]["username"] : $data["message"]["new_chat_member"]["first_name"]. " ".$data["message"]["new_chat_member"]["last_name"];}
	if(isset($data["message"]["left_chat_participant"])){
        $this->userExit = (isset($data["message"]["left_chat_participant"]["username"])) ? "@". $data["message"]["left_chat_participant"]["username"] : $data["message"]["left_chat_participant"]["first_name"]." ".$data["message"]["left_chat_participant"]["last_name"];}
        $this->groupOrPrivate = ($data["message"]["chat"]["type"] == "private") ? 0 : 1; // 0 = private message, 1 = group message
		$this->groupName = ($this->groupOrPrivate === 1) ? $data["message"]["chat"]["title"] : $this->senderUsername;
        $this->reqUrl = "https://api.telegram.org/bot".$this->getBotToken() . "/";
        $this->whoamI = "ben hüsna b0t\n
 · *bilgiad* yazarsan sana harika rastgele bilgiler getiririm\n
 · *bilgiad* {söz öbeği} formatında şeyler söylersen de arar tarar senin için o şeyi bulurum _örn: bilgiad odtü_\n
 · *bilgiadl{dil}* yazarsan sana attığın {dil} koduna sahip dilde harika rastgele bilgiler getiririm _örn: bilgiadlen_\n
 · *bilgiadl{dil}* {söz öbeği} formatında şeyler söylersen de arar tarar senin için o şeyi {dil} koduna sahip dilde bulurum _örn: bilgiadlen Deaths in 2019_\n
 · *getirhoca* {id} | {ad-soyad} yazarsan senin için okulda o öğrenciyi ararım _örn: getirhoca 2337046 ya da getirhoca ali yılmaz_ (numaranın veya ad soyad bilgisinin tamamını yazmasan da yardımcı olurum _örn: getirhoca ayşe ha_ )\n
 · *mizahyab* yazarsan senin için birbirinden eĞLenCeLi fıkralarımdan birisini anlatırım\n
 · *fotoad* yazarsan senin için internetin derinliklerinden elde ettiğim görsellerimden birisini paylaşırım\n
 · *yemekad* yazarsan senin için yemekhanede bugün ne olduğunu söylerim\n
 · *dolarad* yazarsan 1 amerikan doları kaç tl imiş onu söyler ve seninle üzülürüm\n
 · *avroad* veya *euroad* yazarsan 1 avro kaç tl imiş onu söyler ve seninle üzülürüm\n
 · *egonomiad* yazarsan senin için en güncel iktisadi verileri bir araya getirir güçlü egonomimizin ne kadar iyi olduğundan dem vururum\n
 · *havadurumuad* {şehir} {ilçe} yazarsan senin için en güncel hava durumu verilerini getiririm\n
 · *gunaydin* yazarsan seni güne sıcacık başlatmaya çalışırım, çok korkuyorum, selam\n
 · *komutad* yazarsan sana komutlara daha hızlı erişebilmen için menü hazırlarım\n
 · *oyunad* {oyunadı} yazarsan sana istedigin oyunun fiyatini soylerim\n
henüz yeni sayılırım mazur gör hoja!\n
for hugs and bugs @z4r4r
";

    }

    public function process(){
        if(trim($this->getUserEnter()) != "")
        {
            $this->sayHi();
            return;
        }
        else if(trim($this->getUserExit()) != "")
        {
            $this->sayBye();
            return;
        }

        foreach ($this->getCommands() as $key => $value) {
            if (strpos($this->message, $key) !== false) {
                $value();
            }
        }
        die();
    }

    /*
        sendMessage function takes
        $message : text of the message to be sent
        $reply_to_message (optional, default 0) : sends messages as reply if <> 0
    */


    public function sendMessage($message,$reply_to_message = 0) {
        $reply = ($reply_to_message != 0) ? "&reply_to_message_id=".$this->getMessageId(): "";
        $url = "sendMessage?chat_id=".$this->getChatId()."&text=".urlencode($message).$reply."&parse_mode=markdown";
        $this->requEst($url);
        return;
    }
	
	public function sendMessage_html($message,$reply_to_message = 0) {
        $reply = ($reply_to_message != 0) ? "&reply_to_message_id=".$this->getMessageId(): "";
        $url = "sendMessage?chat_id=".$this->getChatId()."&text=".urlencode($message).$reply."&parse_mode=html";		
        $this->requEst($url);
        return;
    }

	public function sendMessage_w_markup($message, $reply_markup) {

        $content = array(
			'chat_id' => $this->getChatId(),
			'reply_markup' => $reply_markup,
			'text' => "$message",
			'reply_to_message_id' => $this->getMessageId()
		);

        $this->requEst_post("sendMessage",$content);
        return;
    }



    /*
        sendPhoto function takes
        $photoUrl : image file link, any common extension(gif,jpeg,png etc.)
        $caption (optional, default 0) : image message caption, 0-200 characters
        $reply_to_message (optional, default 0) : sends messages as reply if <> 0

    */


    public function sendPhoto ($photoUrl, $caption = "",$reply_to_message = 0) {
        $reply = ($reply_to_message != 0) ? "&reply_to_message_id=".$this->getMessageId(): "";
        $caption = ($caption !== "") ? "&caption=" . urlencode(substr($caption,0,200)) : "";
        $url = "sendPhoto?chat_id=".$this->getChatId()."&photo=".urlencode($photoUrl). $caption . $reply;
        $this->requEst($url);
    }
    /*
        sendVoiceMessage function takes
        $voice : sound file link, only ogg extension is allowed
        $caption (optional) : voice message caption, 0-200 characters
        $reply_to_message (optional, default 0): sends messages as reply if <> 0

    */
    public function sendVoiceMessage ($voice, $caption="",$reply_to_message = 0) {

        $reply = ($reply_to_message != 0) ? "&reply_to_message_id=". $this->getMessageId()  : "";
        $url = "sendVoice?chat_id=".$this->getChatId()."&voice=".urlencode($voice)."&caption=".urlencode(substr($caption,0,200)).$reply;
        $this->requEst($url);
    }


    private function sayHi(){
            if($this->getUserEnter() == "HusnaBot" ){
                $this->sendVoiceMessage("https://kursat.space/b0t/audio/selam.ogg","hey bitchezzz");
                $this->sendMessage($this->getWhoamI());
            }else{
		
                $this->sendVoiceMessage("https://kursat.space/b0t/audio/selam.ogg",$this->getUserEnter(). " gelmiş hoj gelmiş");
            }
    }
    private function sayBye(){
        $this->sendVoiceMessage("https://kursat.space/b0t/audio/seriuzgunad.ogg",$this->getUserExit(). " gitti. artık bir eksiğiz...");
    }
    public function requEst($url){

        $output =  $this->getReqUrl() . $url;
	/*
        $ac = fopen("reqs.txt","a+");
        fwrite($ac,$output."\n");

        fclose($ac);
	*/


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getReqUrl() . $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_TIMEOUT,1000);
		curl_exec($ch);
		curl_close($ch);
    }
	public function requEst_post($url,$content){

		$ch = curl_init();
		$url= $this->getReqUrl() . $url;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_TIMEOUT,1000);

		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$server_output = curl_exec ($ch);
		curl_close ($ch);
		var_dump($server_output);



		/*
		$ac = fopen("logxxxxx.txt","a+");
       		fwrite($ac,$server_output."\n");

        	fclose($ac);
		*/
    }

    /**
     * Get the value of senderUsername
     */
    public function getSenderUsername()
    {
        return $this->senderUsername;
    }

    /**
     * Get the value of senderFirstName
     */
    public function getSenderFirstName()
    {
        return $this->senderFirstName;
    }

    /**
     * Get the value of senderLastName
     */
    public function getSenderLastName()
    {
        return $this->senderLastName;
    }

    /**
     * Get the value of message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the value of chatId
     */
    public function getChatId()
    {
        return $this->chatId;
    }

    /**
     * Get the value of userEnter
     */
    public function getUserEnter()
    {
        return $this->userEnter;
    }

    /**
     * Get the value of userExit
     */
    public function getUserExit()
    {
        return $this->userExit;
    }

    /**
     * Get the value of groupOrPrivate
     */
    public function getGroupOrPrivate()
    {
        return $this->groupOrPrivate;
    }

    /**
     * Get the value of messageId
     */
    public function getMessageId()
    {
        return $this->messageId;
    }
    /**
     * Get the value of senderId
     */
    public function getSenderId()
    {
        return $this->senderId;
    }
    /**
     * Set the value of commands
     *
     * @return  self
     */
    public function addCommand($command,$function_name,$add_menu=0)
    {
	if($add_menu) { $this->menu_elems += array($command=>$function_name); }
        $this->commands += array($command=>$function_name);
        return $this;
    }

    /**
     * Get the value of reqUrl
     */
    public function getReqUrl()
    {
        return $this->reqUrl;
    }

    /**
     * Get the value of whoamI
     */
    public function getWhoamI()
    {
        return $this->whoamI;
    }

    /**
     * Get the value of commands
     */
    public function getCommands()
    {
        return $this->commands;
    }
	/**
     * Get the value of menu_elems
     */
    public function getMenu()
    {
        return $this->menu_elems;
    }

    /**
     * Get the value of firstWord
     */
    public function getFirstWord()
    {
        return $this->firstWord;
    }

    /**
     * Get the value of otherWords
     */
    public function getOtherWords()
    {
        return $this->otherWords;
    }

    /**
     * Get the value of queryTokens
     */
    public function getQueryTokens()
    {
        return $this->queryTokens;
    }

	/**
     * Get the value of groupName
     */
    public function getGroupName()
    {
        return $this->groupName;
    }
}

?>
