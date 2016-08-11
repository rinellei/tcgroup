<?php
/**
 * Telegram Bot access token и URL.
 */
$access_token = '249706675:AAGoqr_sNdWkED3m9p5nvV_OvQXNGJfxL70';
$api = 'https://api.telegram.org/bot' . $access_token;

/**
 * Задаём основные переменные.
 */
$output = json_decode(file_get_contents('php://input'), TRUE);
$chat_id = $output['message']['chat']['id'];
$first_name = $output['message']['chat']['first_name'];
$message = $output['message']['text'];

/**
 * Emoji для лучшего визуального оформления.
 */
$emoji = array(
  'preload' => json_decode('"\uD83D\uDE03"'), // Улыбочка.
  'weather' => array(
    'clear' => json_decode('"\u2600"'), // Солнце.
    'clouds' => json_decode('"\u2601"'), // Облака.
    'rain' => json_decode('"\u2614"'), // Дождь.
    'snow' => json_decode('"\u2744"'), // Снег.
  ),
);

/**
 * Получаем команды от пользователя.
 */
switch($message) {
  // API погоды предоставлено OpenWeatherMap.
  // @see http://openweathermap.org
  case '/pogoda':
    // Отправляем приветственный текст.
    $preload_text = 'Одну секунду, ' . $first_name . ' ' . $emoji['preload'] . ' Я уточняю для вас погоду..';
    sendMessage($chat_id, $preload_text);
    // API key для OpenWeatherMap.
    $apikey= '8524c0476f79564e19712812f4df7b28';
    // ID для города/района/местности (есть все города РФ).
    $id = '1484839'; // Для примера: Петербург, север города.
    // Получаем JSON-ответ от OpenWeatherMap.
    $pogoda = json_decode(file_get_contents('http://api.openweathermap.org/data/2.5/weather?appid=' . $apikey . '&id=' . $id . '&units=metric&lang=ru'), TRUE);
    // Определяем тип погоды из ответа и выводим соответствующий Emoji.
    if ($pogoda['weather'][0]['main'] === 'Clear') { $weather_type = $emoji['weather']['clear'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Clouds') { $weather_type = $emoji['weather']['clouds'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Rain') { $weather_type = $emoji['weather']['rain'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Snow') { $weather_type = $emoji['weather']['snow'] . ' ' . $pogoda['weather'][0]['description']; }
    else $weather_type = $pogoda['weather'][0]['description'];
    // Температура воздуха.
    if ($pogoda['main']['temp'] > 0) { $temperature = '+' . sprintf("%d", $pogoda['main']['temp']); }
    else { $temperature = sprintf("%d", $pogoda['main']['temp']); }
    // Направление ветра.
    if ($pogoda['wind']['deg'] >= 0 && $pogoda['wind']['deg'] <= 11.25) { $wind_direction = 'северный'; }
    elseif ($pogoda['wind']['deg'] > 11.25 && $pogoda['wind']['deg'] <= 78.75) { $wind_direction = 'северо-восточный, '; }
    elseif ($pogoda['wind']['deg'] > 78.75 && $pogoda['wind']['deg'] <= 101.25) { $wind_direction = 'восточный, '; }
    elseif ($pogoda['wind']['deg'] > 101.25 && $pogoda['wind']['deg'] <= 168.75) { $wind_direction = 'юго-восточный, '; }
    elseif ($pogoda['wind']['deg'] > 168.75 && $pogoda['wind']['deg'] <= 191.25) { $wind_direction = 'южный, '; }
    elseif ($pogoda['wind']['deg'] > 191.25 && $pogoda['wind']['deg'] <= 258.75) { $wind_direction = 'юго-западный, '; }
    elseif ($pogoda['wind']['deg'] > 258.75 && $pogoda['wind']['deg'] <= 281.25) { $wind_direction = 'западный, '; }
    elseif ($pogoda['wind']['deg'] > 281.25 && $pogoda['wind']['deg'] <= 348.75) { $wind_direction = 'северо-западный, '; }
    else { $wind_direction = ' '; }
    // Формирование ответа.
    $weather_text = 'Сейчас ' . $weather_type . '. Температура воздуха: ' . $temperature . '°C. Ветер ' . $wind_direction . sprintf("%u", $pogoda['wind']['speed']) . ' м/сек.';
    // Отправка ответа пользователю Telegram.
    sendMessage($chat_id, $weather_text);
    break;
  default:
    break;
}

/**
 * Функция отправки сообщения sendMessage().
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
