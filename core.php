<?

class bot_vk {

 const v = '5.0';
 const METHOD_URL = 'https://api.vk.com/method/';
 const v_bot = '1.0';

 function __construct($settings) {
  $this->settings = (object) $settings;
 }

 public function start() {
  global $settings, $body, $user_id;
  switch ($settings->data->type) {

   case 'confirmation': 
    echo $settings->confirmation_token; 
   break;

   case 'message_new':

    $get = $this->api('messages.get', [
     'count' => 1,
     'access_token' => $settings->token 
    ]);

    if($get->response[1]->read_state == 0) {

     $oot = $settings->data->object->attachments;
     $user_id = $settings->data->object->user_id;

     $body_mess = mb_strtolower($settings->data->object->body);
     $body = explode(' ', $body_mess);

     if($body[0] == '/commands' || $body[0] == '/cmd' || $body[0] == '/команды') {
      $this->send_message("Список доступных команд ".$this->random(['⁣💜⁣🔥', '⁣⁣💙⁣🔥'])."\n/commands \n/music \n/fact \n/saved \n/utter \n/update \n/advice \n/wiki \n/help \n/news");
     }

     elseif($body[0] == 'музыка') {
      $this->send_message('', 'audio'.$this->random($settings->audio));
      $this->send_message('', '', 50);
     }

     elseif(($body[0] == '/regdate' || $body[0] == '/регистрация') and (!empty($body[1]))) {
      $this->send_message($this->foaf($body[1]));
     }

     elseif($body[0] == '/update' || $body[0] == '/обновления' || $body[0] == '/upd') {
      $this->send_message("Обновление от 21 октября ".$this->random(['⁣💜⁣🔥', '⁣⁣💙⁣🔥'])."\n\n 1. Команды стали поддерживаться на русском языке, например: <</music>> будет означать тоже, что и <</музыка>> \n\n 2. Добавлена команда <</advice>> - генератор мотивационных советов, наверное\n\n 3. Восстановлена команда <</wiki + запрос>> - краткое определение задаваемого слова");
      $this->send_message("Обновление от 22 октября ".$this->random(['⁣💜⁣🔥', '⁣⁣💙⁣🔥'])."\n\n 1. Добавлена команда <</news>> - список последних событий из Яндекс.Новости");
     }

     elseif($body[0] == '/saved' || $body[0] == '/сохраненка') {

      $scan = $this->api('photos.get', [
        'owner_id' => '-152318721',
        'album_id' => 'wall',
        'count' => 1,
        'offset' => rand(rand(1,50),1000),
        'access_token' => $settings->utoken
      ]);

      $id = $scan->response[0]->pid;
       
      $this->send_message($this->random(['⁣Окей, держи ⁣💜⁣🔥', '⁣⁣Надеюсь понравится ⁣⁣💙⁣🔥']), 'photo-152318721_'.$id);
     }

     elseif($body[0] == '/utter' || $body[0] == '/высказывание') {
      $uttr = $this->curl('http://randstuff.ru/saying/');
      preg_match('/<td>(.*?)<span class="author">(.*?)<\/span><\/td>/is', $uttr, $match);
      $this->send_message($this->random(['⁣Окей, держи ⁣💜⁣🔥', '⁣⁣Надеюсь понравится ⁣⁣💙⁣🔥'])."\n\n".$match[1]."\n".$match[2]);
     }

     elseif($body[0] == '/advice' || $body[0] == '/совет' ) {
      $advice = json_decode($this->curl('http://fucking-great-advice.ru/api/random'));
      $text = html_entity_decode($advice->text);
      if($body[1] == '1') $fr = preg_replace('блять', '', $text);
      else $fr = $text;
      $this->send_message($this->random(['⁣Окей, держи ⁣💜⁣🔥', '⁣⁣Надеюсь понравится ⁣⁣💙⁣🔥'])."\n\n".$fr);
     }

     elseif(($body[0] == '/wiki' || $body[0] == '/вики') and (!empty($body[1]))) {
      $this->send_message($this->wiki($body[1]));
     }

     elseif($body[0] == '/news' || $body[0] == '/новости') {
      $this->send_message($this->random(['⁣Окей, держи ⁣💜⁣🔥', '⁣⁣Надеюсь понравится ⁣⁣💙⁣🔥'])."\n\n".$this->news('1'));
     }

     elseif(($body[0] == '/voice' || $body[0] == '/голос') and (!empty($body[1]))) {

      $get = $this->api('docs.getUploadServer', [
       'type' => 'audio_message',
       'group_id' => '44214',
       'access_token' => $settings->utoken
      ]);

      $name = rand(1000,9999).".mp3";
      $fr = str_replace("/voice", "", $body_mess);

      $url = "https://translate.google.com/translate_tts?ie=UTF-8&client=tw-ob&q=".str_replace(array(" "), array("+"), $fr)."&tl=ru";

      $this->send_message('Одну минуточку, зай '.$this->random(['⁣💜⁣🔥', '⁣⁣💙⁣🔥']));
      sleep(2);

      $handle = curl_init($url);
      $downloadFile = fopen($name, "w");
      curl_setopt($handle, CURLOPT_FILE, $downloadFile);
      curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
      curl_exec($handle);
      curl_close($handle);
      fclose($downloadFile);

      $json = json_decode($this->curl($get->response->upload_url, ['file' => new CURLFile($name)]));

      $save = $this->api('docs.save', [
       'access_token' => $settings->utoken, 
       'file' => $json->file
      ]);

      unlink($name);

      $this->send_message('', 'doc'.$save->response[0]->owner_id.'_'.$save->response[0]->did);
     
     }

     elseif(($body[0] == '/vinci' or $body[0] == '/винчи') and $oot[0]->type == 'photo') {
      if(($oot[0]->photo->width < 200 || $oot[0]->photo->height < 200) and $body[1] != 0) {
       $this->send_message('Так, давай сразу договоримся мне нужна фотография не меньше 200 на 200 пикселей ♥');
      } else {

       $this->send_message('Так, пошла обработка, погоди минутку ♥');
       sleep(4);
       $name = $oot[0]->photo->access_key.'.jpg';

       $this->download($oot[0]->photo->photo_604, $name);

       $test = json_decode($this->curl('http://vinci.camera/preload', ['file' => new CURLFile($name)]));

       if($test->preload) {

        $r = 'http://vinci.camera/process/'.$test->preload.'/'.$body[1].'';
        $this->download($r, $name);

        $get = $this->api('photos.getMessagesUploadServer', ['access_token' => $settings->token]);

        if($get->response) {
         $upload = json_decode($this->curl($get->response->upload_url, ['file' => new CURLFile($name)]));
         if($upload->hash) {
          $save = $this->api('photos.saveMessagesPhoto', [
           'hash' => $upload->hash,
           'photo' => $upload->photo, 
           'server' => $upload->server, 
           'access_token' => $settings->token
          ]);  

          $this->send_message($this->random(['⁣Окей, держи ⁣💜⁣🔥', '⁣⁣Надеюсь понравится ⁣⁣💙⁣🔥']), 'photo'.$save->response[0]->owner_id.'_'.$save->response[0]->pid);
          unlink($name);
         }
        }

       }
      }
     }

     elseif($body[0] == '/help' || $body[0] == '/помощь') {
      $this->send_message("Описание каждой команды и правилосьность запроса ".$this->random(['⁣💜⁣🔥', '⁣⁣💙⁣🔥'])."\n\nКоманда <</commands>> или <</команды>> - выводят пользователю все доступные команды\n\nКоманда <</music>> или <</музыка>> - выводит пользователю случайный трек из раздела <<Новинки>> музыки ВКонтакте\n\nКоманда <</fact>> или <</факт>> - выводит случайную цитату из интернета\n\nоманда <</saved>> или <</сохраненка>> - выводит пользователю топ-сохраненку из популярных групп ВКонтакте\n\nКоманда <</utter>> или <</высказывание>> - выводит случайное высказывание из интернета\n\nКоманда <</update>> или <</обновления>> - выводит пользователю последние изменения в боте\n\nКоманда <</advice>> или <</совет>> - выводит пользователю случайный совет\n\nКоманда <</wiki + запрос>> или <</вики + запрос>> - краткое определение задаваемого слова\n\n");
      $this->send_message("Команда <</news>> или <</новости>> - выводит список последних новостей разных категорий");
     }

     elseif($body[0] == '/fact' || $body[0] == '/факт') {
      $fact = $this->curl('http://randstuff.ru/fact/');
      preg_match('/<td>(.*?)<\/td>/is', $fact, $match);
      $this->send_message($this->random(['⁣Окей, держи ⁣💜⁣🔥', '⁣⁣Надеюсь понравится ⁣⁣💙⁣🔥'])."\n\n".$match[1]);
     } else {
      $this->send_message('', '', 69);
     }

    }

    echo('ok'); 
   break;

   case 'group_join':

    $info = $this->api('users.get', ['user_ids' => $settings->data->object->user_id]);

    $this->send_message($info->response[0]->first_name.', спасибо за подписку '.$this->random(['⁣💜⁣🔥', '⁣⁣💙⁣🔥']));
   break;

   default: echo('ok');
  }
 }

 public function foaf($tid) {

  $t = $this->api('users.get', [
   'user_ids' => $tid, 
   'fields' => 'deactivated'
  ]);

  if($t->response[0]->deactivated != 'deleted' or $t->response[0]->deactivated != 'banned') {
   $id = $t->response[0]->uid;
   for($i = $id; $i <= (2+$id); $i++) {
    $p = file_get_contents('https://vk.com/foaf.php?id='.$i);
    $match = preg_match('/<ya:created dc:date="([\d]{4}-[\d]{2}-[\d]{2}T[\d]{2}:[\d]{2}:[\d]{2}\+[\d]{2}:[\d]{2})/i', $p, $matches);
    if($matches[1]) {
     $exp = explode("T", $matches[1]);
     $month = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
     $time = str_replace('+03:00', ' ', $exp[1]);
     $date = date("d", strtotime($exp[0])).' '.$month[date("n", strtotime($exp[0]))-1].' '.date("Y", strtotime($exp[0]));
     return 'Дата регистрации: '.$date.' в '.substr($time, 0, -4).'<br>';
    } else {
     ++$i;
     continue;
    }
   }
  } else {
    return 'Не удалось, блять, ну как так-то(99!111';
  }
 }

 public function wiki($word) {
  $data = json_decode($this->curl('https://ru.wikipedia.org/w/api.php', [
   'action' => 'opensearch',
   'search' => $word,
   'prop' => 'info',
   'format' => 'json',
   'inprop' => 'url'
  ]));

  if($data[2][0] != null) {
    
   $short = $this->vkcc($data[3][0]);
   $short1 = $this->vkcc($data[3][1]);
   $short2 = $this->vkcc($data[3][2]);

   if (!preg_match("/Это статья о слове/i", $data[2][0]) and (substr($data[2][0], -1) != ':')) {
    $text = $this->random(['⁣Окей, держи ⁣💜⁣🔥', '⁣⁣Надеюсь понравится ⁣⁣💙⁣🔥'])."\n\n ".$data[2][0]."\n\nПолная статья - ".$this->expo($short->response->short_url);
   } else {
    $text = $this->random(['⁣Окей, держи ⁣💜⁣🔥', '⁣⁣Надеюсь понравится ⁣⁣💙⁣🔥'])."\n\nВ Википедии заданная страница является страницей разрешения неоднозначностей. Несколько вариантов, которые она предлагает: \n\n".$this->expo($short->response->short_url)." \n".$this->expo($short1->response->short_url)." \n".$this->expo($short2->response->short_url);
   }
   return $text;
  } else {
   return "По твоему запросу ничего не нашлось, повтори запрос ".$this->random(['⁣💜⁣🔥', '⁣⁣💙⁣🔥']);
  }
 }

 public function random($array) {
  return $array[mt_rand(0, count($array)-1)];
 }

 public function vkcc($url) {
  global $settings;
  return $this->api('utils.getShortLink', [
  'access_token' => $settings->utoken,
  'url' => $url
  ]);
 }

 public function expo($url) {
  return preg_replace('!^https?://!i', '', $url);
 }

 public function download($url, $save) {
  $ch = curl_init($url);
  $fp = fopen($save, 'wb');
  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_exec($ch);
  curl_close($ch);
  fclose($fp);
 }

 public function news($true) {
  if($true == 1) {
   $url = array('http://news.yandex.ru/index.rss','http://news.yandex.ru/world.rss','http://news.yandex.ru/sport.rss','http://news.yandex.ru/auto.rss','http://news.yandex.ru/science.rss','http://news.yandex.ru/internet.rss','http://news.yandex.ru/computers.rss'); 
   for($x = 0; $x <=6; $x++) {
    $rss = simplexml_load_file($url[$x]);
    $t .= ($x+1).". ".substr($rss->channel->title, 29).": ".$rss->channel->item->title."\n\n";
   }
   return $t;
  }
 }

 public function api($method, $param) {
  return json_decode($this->curl('https://api.vk.com/method/'.$method, $param));
 }

 public function send_message($message, $attach = null, $stick = null) {
  global $settings, $user_id;
  return $this->api('messages.send', [
   'message' => $message,
   'attachment' => $attach,
   'sticker_id' => $stick,
   'user_id' => $user_id,
   'access_token' => $settings->token,
   'v' => self::v
  ]);
 }

 public function curl($url, $post = null) {
  if(is_array($post)) $url .= '?'.http_build_query($post);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  if($post) {
   curl_setopt($ch, CURLOPT_POST, 1); 
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
  }
  $response = curl_exec($ch);
  curl_close($ch);
  return $response;
 }

}

?>
