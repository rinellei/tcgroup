<<<<<<< HEAD
<?php
/**
 * Telegram Bot access token è URL.
 */
$access_token = '249706675:AAG7yLpaOGHVhpwYJ9QC0UrUmZrD_GnWhyc';
$api = 'https://api.telegram.org/bot' . $access_token;

/**
 * Çàäà¸ì îñíîâíûå ïåðåìåííûå.
 */
$output = json_decode(file_get_contents('php://input'), TRUE);
$chat_id = $output['message']['chat']['id'];
$first_name = $output['message']['chat']['first_name'];
$message = $output['message']['text'];

/**
 * Emoji äëÿ ëó÷øåãî âèçóàëüíîãî îôîðìëåíèÿ.
 */
$emoji = array(
  'preload' => json_decode('"\uD83D\uDE03"'), // Óëûáî÷êà.
  'weather' => array(
    'clear' => json_decode('"\u2600"'), // Ñîëíöå.
    'clouds' => json_decode('"\u2601"'), // Îáëàêà.
    'rain' => json_decode('"\u2614"'), // Äîæäü.
    'snow' => json_decode('"\u2744"'), // Ñíåã.
  ),
);

/**
 * Ïîëó÷àåì êîìàíäû îò ïîëüçîâàòåëÿ.
 */
switch($message) {
  // API ïîãîäû ïðåäîñòàâëåíî OpenWeatherMap.
  // @see http://openweathermap.org
  case '/pogoda':
    // Îòïðàâëÿåì ïðèâåòñòâåííûé òåêñò.
    $preload_text = 'Îäíó ñåêóíäó, ' . $first_name . ' ' . $emoji['preload'] . ' ß óòî÷íÿþ äëÿ âàñ ïîãîäó..';
    sendMessage($chat_id, $preload_text);
    // API key äëÿ OpenWeatherMap.
    $apikey= '8524c0476f79564e19712812f4df7b28';
    // ID äëÿ ãîðîäà/ðàéîíà/ìåñòíîñòè (åñòü âñå ãîðîäà ÐÔ).
    $id = '1484839'; // Äëÿ ïðèìåðà: Ïåòåðáóðã, ñåâåð ãîðîäà.
    // Ïîëó÷àåì JSON-îòâåò îò OpenWeatherMap.
    $pogoda = json_decode(file_get_contents('http://api.openweathermap.org/data/2.5/weather?appid=' . $apikey . '&id=' . $id . '&units=metric&lang=ru'), TRUE);
    // Îïðåäåëÿåì òèï ïîãîäû èç îòâåòà è âûâîäèì ñîîòâåòñòâóþùèé Emoji.
    if ($pogoda['weather'][0]['main'] === 'Clear') { $weather_type = $emoji['weather']['clear'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Clouds') { $weather_type = $emoji['weather']['clouds'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Rain') { $weather_type = $emoji['weather']['rain'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Snow') { $weather_type = $emoji['weather']['snow'] . ' ' . $pogoda['weather'][0]['description']; }
    else $weather_type = $pogoda['weather'][0]['description'];
    // Òåìïåðàòóðà âîçäóõà.
    if ($pogoda['main']['temp'] > 0) { $temperature = '+' . sprintf("%d", $pogoda['main']['temp']); }
    else { $temperature = sprintf("%d", $pogoda['main']['temp']); }
    // Íàïðàâëåíèå âåòðà.
    if ($pogoda['wind']['deg'] >= 0 && $pogoda['wind']['deg'] <= 11.25) { $wind_direction = 'ñåâåðíûé'; }
    elseif ($pogoda['wind']['deg'] > 11.25 && $pogoda['wind']['deg'] <= 78.75) { $wind_direction = 'ñåâåðî-âîñòî÷íûé, '; }
    elseif ($pogoda['wind']['deg'] > 78.75 && $pogoda['wind']['deg'] <= 101.25) { $wind_direction = 'âîñòî÷íûé, '; }
    elseif ($pogoda['wind']['deg'] > 101.25 && $pogoda['wind']['deg'] <= 168.75) { $wind_direction = 'þãî-âîñòî÷íûé, '; }
    elseif ($pogoda['wind']['deg'] > 168.75 && $pogoda['wind']['deg'] <= 191.25) { $wind_direction = 'þæíûé, '; }
    elseif ($pogoda['wind']['deg'] > 191.25 && $pogoda['wind']['deg'] <= 258.75) { $wind_direction = 'þãî-çàïàäíûé, '; }
    elseif ($pogoda['wind']['deg'] > 258.75 && $pogoda['wind']['deg'] <= 281.25) { $wind_direction = 'çàïàäíûé, '; }
    elseif ($pogoda['wind']['deg'] > 281.25 && $pogoda['wind']['deg'] <= 348.75) { $wind_direction = 'ñåâåðî-çàïàäíûé, '; }
    else { $wind_direction = ' '; }
    // Ôîðìèðîâàíèå îòâåòà.
    $weather_text = 'Ñåé÷àñ ' . $weather_type . '. Òåìïåðàòóðà âîçäóõà: ' . $temperature . '°C. Âåòåð ' . $wind_direction . sprintf("%u", $pogoda['wind']['speed']) . ' ì/ñåê.';
    // Îòïðàâêà îòâåòà ïîëüçîâàòåëþ Telegram.
    sendMessage($chat_id, $weather_text);
    break;
  default:
    break;
}

/**
 * Ôóíêöèÿ îòïðàâêè ñîîáùåíèÿ sendMessage().
 */
function sendMessage($chat_id, $message, $access_token) {
    echo "sending message to " . $chat_id . "\n";


    $url = "https://api.telegram.org/bot" . $access_token . "/sendMessage?chat_id=" . $chat_id;
    $url = $url . "&text=" . urlencode($message);
    $ch = curl_init();
    $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
    );
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    curl_close($ch);
}
=======
<?php
/**
 * Telegram Bot access token Ð¸ URL.
 */
$access_token = '249706675:AAG7yLpaOGHVhpwYJ9QC0UrUmZrD_GnWhyc';
$api = 'https://api.telegram.org/bot' . $access_token;

/**
 * Ð—Ð°Ð´Ð°Ñ‘Ð¼ Ð¾ÑÐ½Ð¾Ð²Ð½Ñ‹Ðµ Ð¿ÐµÑ€ÐµÐ¼ÐµÐ½Ð½Ñ‹Ðµ.
 */
$output = json_decode(file_get_contents('php://input'), TRUE);
$chat_id = $output['message']['chat']['id'];
$first_name = $output['message']['chat']['first_name'];
$message = $output['message']['text'];

/**
 * Emoji Ð´Ð»Ñ Ð»ÑƒÑ‡ÑˆÐµÐ³Ð¾ Ð²Ð¸Ð·ÑƒÐ°Ð»ÑŒÐ½Ð¾Ð³Ð¾ Ð¾Ñ„Ð¾Ñ€Ð¼Ð»ÐµÐ½Ð¸Ñ.
 */
$emoji = array(
  'preload' => json_decode('"\uD83D\uDE03"'), // Ð£Ð»Ñ‹Ð±Ð¾Ñ‡ÐºÐ°.
  'weather' => array(
    'clear' => json_decode('"\u2600"'), // Ð¡Ð¾Ð»Ð½Ñ†Ðµ.
    'clouds' => json_decode('"\u2601"'), // ÐžÐ±Ð»Ð°ÐºÐ°.
    'rain' => json_decode('"\u2614"'), // Ð”Ð¾Ð¶Ð´ÑŒ.
    'snow' => json_decode('"\u2744"'), // Ð¡Ð½ÐµÐ³.
  ),
);

/**
 * ÐŸÐ¾Ð»ÑƒÑ‡Ð°ÐµÐ¼ ÐºÐ¾Ð¼Ð°Ð½Ð´Ñ‹ Ð¾Ñ‚ Ð¿Ð¾Ð»ÑŒÐ·Ð¾Ð²Ð°Ñ‚ÐµÐ»Ñ.
 */
switch($message) {
  // API Ð¿Ð¾Ð³Ð¾Ð´Ñ‹ Ð¿Ñ€ÐµÐ´Ð¾ÑÑ‚Ð°Ð²Ð»ÐµÐ½Ð¾ OpenWeatherMap.
  // @see http://openweathermap.org
  case '/pogoda':
    // ÐžÑ‚Ð¿Ñ€Ð°Ð²Ð»ÑÐµÐ¼ Ð¿Ñ€Ð¸Ð²ÐµÑ‚ÑÑ‚Ð²ÐµÐ½Ð½Ñ‹Ð¹ Ñ‚ÐµÐºÑÑ‚.
    $preload_text = 'ÐžÐ´Ð½Ñƒ ÑÐµÐºÑƒÐ½Ð´Ñƒ, ' . $first_name . ' ' . $emoji['preload'] . ' Ð¯ ÑƒÑ‚Ð¾Ñ‡Ð½ÑÑŽ Ð´Ð»Ñ Ð²Ð°Ñ Ð¿Ð¾Ð³Ð¾Ð´Ñƒ..';
    sendMessage($chat_id, $preload_text);
    // API key Ð´Ð»Ñ OpenWeatherMap.
    $apikey= '8524c0476f79564e19712812f4df7b28';
    // ID Ð´Ð»Ñ Ð³Ð¾Ñ€Ð¾Ð´Ð°/Ñ€Ð°Ð¹Ð¾Ð½Ð°/Ð¼ÐµÑÑ‚Ð½Ð¾ÑÑ‚Ð¸ (ÐµÑÑ‚ÑŒ Ð²ÑÐµ Ð³Ð¾Ñ€Ð¾Ð´Ð° Ð Ð¤).
    $id = '1484839'; // Ð”Ð»Ñ Ð¿Ñ€Ð¸Ð¼ÐµÑ€Ð°: ÐŸÐµÑ‚ÐµÑ€Ð±ÑƒÑ€Ð³, ÑÐµÐ²ÐµÑ€ Ð³Ð¾Ñ€Ð¾Ð´Ð°.
    // ÐŸÐ¾Ð»ÑƒÑ‡Ð°ÐµÐ¼ JSON-Ð¾Ñ‚Ð²ÐµÑ‚ Ð¾Ñ‚ OpenWeatherMap.
    $pogoda = json_decode(file_get_contents('http://api.openweathermap.org/data/2.5/weather?appid=' . $apikey . '&id=' . $id . '&units=metric&lang=ru'), TRUE);
    // ÐžÐ¿Ñ€ÐµÐ´ÐµÐ»ÑÐµÐ¼ Ñ‚Ð¸Ð¿ Ð¿Ð¾Ð³Ð¾Ð´Ñ‹ Ð¸Ð· Ð¾Ñ‚Ð²ÐµÑ‚Ð° Ð¸ Ð²Ñ‹Ð²Ð¾Ð´Ð¸Ð¼ ÑÐ¾Ð¾Ñ‚Ð²ÐµÑ‚ÑÑ‚Ð²ÑƒÑŽÑ‰Ð¸Ð¹ Emoji.
    if ($pogoda['weather'][0]['main'] === 'Clear') { $weather_type = $emoji['weather']['clear'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Clouds') { $weather_type = $emoji['weather']['clouds'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Rain') { $weather_type = $emoji['weather']['rain'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Snow') { $weather_type = $emoji['weather']['snow'] . ' ' . $pogoda['weather'][0]['description']; }
    else $weather_type = $pogoda['weather'][0]['description'];
    // Ð¢ÐµÐ¼Ð¿ÐµÑ€Ð°Ñ‚ÑƒÑ€Ð° Ð²Ð¾Ð·Ð´ÑƒÑ…Ð°.
    if ($pogoda['main']['temp'] > 0) { $temperature = '+' . sprintf("%d", $pogoda['main']['temp']); }
    else { $temperature = sprintf("%d", $pogoda['main']['temp']); }
    // ÐÐ°Ð¿Ñ€Ð°Ð²Ð»ÐµÐ½Ð¸Ðµ Ð²ÐµÑ‚Ñ€Ð°.
    if ($pogoda['wind']['deg'] >= 0 && $pogoda['wind']['deg'] <= 11.25) { $wind_direction = 'ÑÐµÐ²ÐµÑ€Ð½Ñ‹Ð¹'; }
    elseif ($pogoda['wind']['deg'] > 11.25 && $pogoda['wind']['deg'] <= 78.75) { $wind_direction = 'ÑÐµÐ²ÐµÑ€Ð¾-Ð²Ð¾ÑÑ‚Ð¾Ñ‡Ð½Ñ‹Ð¹, '; }
    elseif ($pogoda['wind']['deg'] > 78.75 && $pogoda['wind']['deg'] <= 101.25) { $wind_direction = 'Ð²Ð¾ÑÑ‚Ð¾Ñ‡Ð½Ñ‹Ð¹, '; }
    elseif ($pogoda['wind']['deg'] > 101.25 && $pogoda['wind']['deg'] <= 168.75) { $wind_direction = 'ÑŽÐ³Ð¾-Ð²Ð¾ÑÑ‚Ð¾Ñ‡Ð½Ñ‹Ð¹, '; }
    elseif ($pogoda['wind']['deg'] > 168.75 && $pogoda['wind']['deg'] <= 191.25) { $wind_direction = 'ÑŽÐ¶Ð½Ñ‹Ð¹, '; }
    elseif ($pogoda['wind']['deg'] > 191.25 && $pogoda['wind']['deg'] <= 258.75) { $wind_direction = 'ÑŽÐ³Ð¾-Ð·Ð°Ð¿Ð°Ð´Ð½Ñ‹Ð¹, '; }
    elseif ($pogoda['wind']['deg'] > 258.75 && $pogoda['wind']['deg'] <= 281.25) { $wind_direction = 'Ð·Ð°Ð¿Ð°Ð´Ð½Ñ‹Ð¹, '; }
    elseif ($pogoda['wind']['deg'] > 281.25 && $pogoda['wind']['deg'] <= 348.75) { $wind_direction = 'ÑÐµÐ²ÐµÑ€Ð¾-Ð·Ð°Ð¿Ð°Ð´Ð½Ñ‹Ð¹, '; }
    else { $wind_direction = ' '; }
    // Ð¤Ð¾Ñ€Ð¼Ð¸Ñ€Ð¾Ð²Ð°Ð½Ð¸Ðµ Ð¾Ñ‚Ð²ÐµÑ‚Ð°.
    $weather_text = 'Ð¡ÐµÐ¹Ñ‡Ð°Ñ ' . $weather_type . '. Ð¢ÐµÐ¼Ð¿ÐµÑ€Ð°Ñ‚ÑƒÑ€Ð° Ð²Ð¾Ð·Ð´ÑƒÑ…Ð°: ' . $temperature . 'Â°C. Ð’ÐµÑ‚ÐµÑ€ ' . $wind_direction . sprintf("%u", $pogoda['wind']['speed']) . ' Ð¼/ÑÐµÐº.';
    // ÐžÑ‚Ð¿Ñ€Ð°Ð²ÐºÐ° Ð¾Ñ‚Ð²ÐµÑ‚Ð° Ð¿Ð¾Ð»ÑŒÐ·Ð¾Ð²Ð°Ñ‚ÐµÐ»ÑŽ Telegram.
    sendMessage($chat_id, $weather_text);
    break;
  default:
    break;
}

/**
 * Ð¤ÑƒÐ½ÐºÑ†Ð¸Ñ Ð¾Ñ‚Ð¿Ñ€Ð°Ð²ÐºÐ¸ ÑÐ¾Ð¾Ð±Ñ‰ÐµÐ½Ð¸Ñ sendMessage().
 */
function sendMessage($chat_id, $message, $access_token) {
    echo "sending message to " . $chat_id . "\n";


    $url = "https://api.telegram.org/bot" . $access_token . "/sendMessage?chat_id=" . $chat_id;
    $url = $url . "&text=" . urlencode($message);
    $ch = curl_init();
    $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
    );
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    curl_close($ch);
}
>>>>>>> 4712874d335609b3d9b83a5a2537fa19d9c687a3
