<?PHP
header('Content-Type: text/html; charset=UTF-8');
  function sendMessage($chatID, $messaggio, $token) {
    #echo "sending message to " . $chatID . "\n";
    $url = "https://api.telegram.org/" . $token . "/sendMessage?chat_id=" . $chatID;
    $url = $url . "&text=" . urlencode($messaggio);
    $ch = curl_init();
    $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
    );
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    curl_close($ch);
	}
	
	#sendMessage($chatid, $message, $token);
	$token = "bot260052841:A9840aTSq9JdcRU555555zX55uiWqYywgA0";
	$chatid = "@channel_name";

function refresh_news(){
	$url = "http://yoursite.com/feed"; // url to parse   или в виде www.site.com/file.rss
	$rss = simplexml_load_file($url); // XML parser
	$mas_title = array();
	$mas_link = array();
	$mas_date = array();
	$db = new SQLite3('database.db') or die('Unable to open database');
	//Вывод.
	foreach($rss->channel->item as $item):
		//echo $item->link; ?></br> <?
		//print $item->title; ?></br> <?
		//print date("r", strtotime($item->pubDate)); ?></br> <?   #D, M d Y H:i:s
		?></br> <? 
		array_push($mas_title, $item->title);   ///заполнили временный массив title'ов чтоб потом проверять по ним бд
		array_push($mas_link, $item->link);   
		array_push($mas_date, date("r", strtotime($item->pubDate)));   

	endforeach;
	
	///создаем таблицу бд если еще нету. если есть не создастся.
$query1 = <<<EOD
	CREATE TABLE IF NOT EXISTS rss (
	id INTEGER PRIMARY KEY,
	title VARCHAR(300),
	link VARCHAR(300),
	date VARCHAR(35) )
EOD;
$db->exec($query1) or die('Create db failed');

// проверка базы на новость. если нету -- добавляем в бд и отправляем в бот\канал сообщение и инфой
	for ($i = 0; $i < count($mas_title); $i++){
		$title_local = $mas_title[$i];
		
		$result = $db->query("SELECT * FROM rss WHERE title LIKE '%".$title_local."%'") or die('Query failed');

		$row = $result->fetchArray();
		print("\n");
		if($row['title'] == null){
			$query = "INSERT INTO rss (id, title, link, date) VALUES(null, '".$title_local."', '".$mas_link[$i]."', '".$mas_date[$i]."')";
			$db->exec($query) or die("Unable to add user $user");

			global $chatid, $token;
			sendMessage($chatid, $title_local."\n".$mas_link[$i], $token);
			sleep(2);
		}
		else{

		}

	}

$db->close();
}

refresh_news();
?>