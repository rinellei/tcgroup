<?php
/**
 * Telegram Bot access token � URL.
 */
$access_token = '249706675:AAG7yLpaOGHVhpwYJ9QC0UrUmZrD_GnWhyc';
$api = 'https://api.telegram.org/bot' . $access_token;

/**
 * ����� �������� ����������.
 */
$output = json_decode(file_get_contents('php://input'), TRUE);
$chat_id = $output['message']['chat']['id'];
$first_name = $output['message']['chat']['first_name'];
$message = $output['message']['text'];

/**
 * Emoji ��� ������� ����������� ����������.
 */
$emoji = array(
  'preload' => json_decode('"\uD83D\uDE03"'), // ��������.
  'weather' => array(
    'clear' => json_decode('"\u2600"'), // ������.
    'clouds' => json_decode('"\u2601"'), // ������.
    'rain' => json_decode('"\u2614"'), // �����.
    'snow' => json_decode('"\u2744"'), // ����.
  ),
);

/**
 * �������� ������� �� ������������.
 */
switch($message) {
  // API ������ ������������� OpenWeatherMap.
  // @see http://openweathermap.org
  case '/pogoda':
    // ���������� �������������� �����.
    $preload_text = '���� �������, ' . $first_name . ' ' . $emoji['preload'] . ' � ������� ��� ��� ������..';
    sendMessage($chat_id, $preload_text);
    // API key ��� OpenWeatherMap.
    $apikey= '8524c0476f79564e19712812f4df7b28';
    // ID ��� ������/������/��������� (���� ��� ������ ��).
    $id = '1484839'; // ��� �������: ���������, ����� ������.
    // �������� JSON-����� �� OpenWeatherMap.
    $pogoda = json_decode(file_get_contents('http://api.openweathermap.org/data/2.5/weather?appid=' . $apikey . '&id=' . $id . '&units=metric&lang=ru'), TRUE);
    // ���������� ��� ������ �� ������ � ������� ��������������� Emoji.
    if ($pogoda['weather'][0]['main'] === 'Clear') { $weather_type = $emoji['weather']['clear'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Clouds') { $weather_type = $emoji['weather']['clouds'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Rain') { $weather_type = $emoji['weather']['rain'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Snow') { $weather_type = $emoji['weather']['snow'] . ' ' . $pogoda['weather'][0]['description']; }
    else $weather_type = $pogoda['weather'][0]['description'];
    // ����������� �������.
    if ($pogoda['main']['temp'] > 0) { $temperature = '+' . sprintf("%d", $pogoda['main']['temp']); }
    else { $temperature = sprintf("%d", $pogoda['main']['temp']); }
    // ����������� �����.
    if ($pogoda['wind']['deg'] >= 0 && $pogoda['wind']['deg'] <= 11.25) { $wind_direction = '��������'; }
    elseif ($pogoda['wind']['deg'] > 11.25 && $pogoda['wind']['deg'] <= 78.75) { $wind_direction = '������-���������, '; }
    elseif ($pogoda['wind']['deg'] > 78.75 && $pogoda['wind']['deg'] <= 101.25) { $wind_direction = '���������, '; }
    elseif ($pogoda['wind']['deg'] > 101.25 && $pogoda['wind']['deg'] <= 168.75) { $wind_direction = '���-���������, '; }
    elseif ($pogoda['wind']['deg'] > 168.75 && $pogoda['wind']['deg'] <= 191.25) { $wind_direction = '�����, '; }
    elseif ($pogoda['wind']['deg'] > 191.25 && $pogoda['wind']['deg'] <= 258.75) { $wind_direction = '���-��������, '; }
    elseif ($pogoda['wind']['deg'] > 258.75 && $pogoda['wind']['deg'] <= 281.25) { $wind_direction = '��������, '; }
    elseif ($pogoda['wind']['deg'] > 281.25 && $pogoda['wind']['deg'] <= 348.75) { $wind_direction = '������-��������, '; }
    else { $wind_direction = ' '; }
    // ������������ ������.
    $weather_text = '������ ' . $weather_type . '. ����������� �������: ' . $temperature . '�C. ����� ' . $wind_direction . sprintf("%u", $pogoda['wind']['speed']) . ' �/���.';
    // �������� ������ ������������ Telegram.
    sendMessage($chat_id, $weather_text);
    break;
  default:
    break;
}

/**
 * ������� �������� ��������� sendMessage().
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