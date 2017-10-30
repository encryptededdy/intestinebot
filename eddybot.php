<?php
include 'keys.php';

define('BOT_TOKEN', $bot_token);
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

abstract class Commands
{
    const    aesthetic = "/aesthetic ";
    const    dear = "/dear ";
    const    intestine = "/intestine ";
    const    yige = "/yige ";
    const    sciroom = "/sciroom ";
    const    mitochondria = "/mitochondria ";
    const    shruggie = "/shruggie ";
    const    define = "/define ";
    const    bitcoin = "/bitcoin ";
    const    austin = "/austin ";
    const    asb = "/asb ";
    const    bs = "/bs ";
    const    globalwarming = "/globalwarming ";
    const    about = "/about ";
    const    westpac = "/westpac ";
    const    drone = "/drone ";
    const    asus = "/asus ";
    const    quake = "/quake ";
    const    samecount = "/samecount";
    const    comic = "/comic";
    const    comic1 = "/comic1";
    const    comic2 = "/comic2";
    const    wow = "/wow";
    const    doggo = "/doggo";
    const    lauren = "/lauren ";
    const    shitpost = "/shitpost ";
    const    help = "/help";
}

// for quakey

function time_since($since)
{
    $chunks = array(
        array(60 * 60 * 24 * 365, 'year'),
        array(60 * 60 * 24 * 30, 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24, 'day'),
        array(60 * 60, 'hour'),
        array(60, 'minute'),
        array(1, 'second')
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 ' . $name : "$count {$name}s";
    return $print;
}

// same

// mysql

date_default_timezone_set('Pacific/Auckland');

function apiRequestWebhook($method, $parameters)
{
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }

    if (!$parameters) {
        $parameters = array();
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }

    $parameters["method"] = $method;

    header("Content-Type: application/json");
    echo json_encode($parameters);
    return true;
}

function exec_curl_request($handle)
{
    $response = curl_exec($handle);

    if ($response === false) {
        $errno = curl_errno($handle);
        $error = curl_error($handle);
        error_log("Curl returned error $errno: $error\n");
        curl_close($handle);
        return false;
    }

    $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
    curl_close($handle);

    if ($http_code >= 500) {
        // do not wat to DDOS server if something goes wrong
        sleep(10);
        return false;
    } else if ($http_code != 200) {
        $response = json_decode($response, true);
        error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
        if ($http_code == 401) {
            throw new Exception('Invalid access token provided');
        }
        return false;
    } else {
        $response = json_decode($response, true);
        if (isset($response['description'])) {
            error_log("Request was successfull: {$response['description']}\n");
        }
        $response = $response['result'];
    }

    return $response;
}

function apiRequest($method, $parameters)
{
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }

    if (!$parameters) {
        $parameters = array();
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }

    foreach ($parameters as $key => &$val) {
        // encoding to JSON array parameters, for example reply_markup
        if (!is_numeric($val) && !is_string($val)) {
            $val = json_encode($val);
        }
    }
    $url = API_URL . $method . '?' . http_build_query($parameters);

    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);

    return exec_curl_request($handle);
}

function apiRequestJson($method, $parameters)
{
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }

    if (!$parameters) {
        $parameters = array();
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }

    $parameters["method"] = $method;

    $handle = curl_init(API_URL);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);
    curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
    curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

    return exec_curl_request($handle);
}

function processMessage($message)
{
    // process incoming message
    $message_id = $message['message_id'];
    $sender = $message['from'];
    $chat_id = $message['chat']['id'];
    if (isset($message['text'])) {
        // incoming text message
        $text = $message['text'];

        if (strpos($text, "/dear") === 0) {
            $dear = file_get_contents("https://zhang.nz/dearstudent.php?api=1");
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $dear));
        } else if (strpos($text, "/intestine") === 0) {
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => '/intestine'));
        } else if (strpos($text, "/yige") === 0) {
            $args = preg_split("/[\s,]+/", $text);
            $consonants = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "x", "z");
            $vowels = array("a", "e", "i", "o", "u");
            if (empty($args[1])) {
                $args[1] = 1;
            } else if ($args[1] > 10) {
                apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => 'Too many yiges.'));
                return;
            }
            $yige = ('Y' . $vowels[array_rand($vowels, 1)] . $consonants[array_rand($consonants, 1)] . $vowels[array_rand($vowels, 1)]);
            for ($i = 1; $i < $args[1]; $i++) {
                $yige = ($yige . ', Y' . $vowels[array_rand($vowels, 1)] . $consonants[array_rand($consonants, 1)] . $vowels[array_rand($vowels, 1)]);
            }
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $yige));
        } else if (strpos($text, "/sciroom") === 0) {
            $sciout = 'Science Department Computer Labs:
      ';
            $labs = '32,33,34,35,64,5,9,6,7,10,8';
            $scijson = file_get_contents('http://www.fos.auckland.ac.nz/api/lab/current_usage/' . $labs . '.json');
            $scidata = json_decode($scijson, true);
            foreach ($scidata as $num) {
                if ($num["current_tutorial"]) {
                    $current_tutorial = $num["current_tutorial"]["description"];
                } else {
                    $current_tutorial = "N/A";
                }
                $sciout = $sciout . $num["room"] . ': ' . $num["machines_in_use"] . '/' . $num["total_machines"] . ' In use. Current Tut: ' . $current_tutorial . '
      ';
            };
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $sciout));
        } else if (strpos($text, "/mitochondria") === 0) {
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => 'mitochondria is the powerhouse of the cell'));
        } else if (strpos($text, "/shruggie") === 0) {
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => '¯\_(ツ)_/¯'));
        } else if (strpos($text, "/define ") === 0) {
            $term = substr($text, 8);
            $definiton = file_get_contents('http://api.urbandictionary.com/v0/define?term=\'' . urlencode($term) . '\'');
            $definiton = json_decode($definiton, true);
            if ($definiton["result_type"] == "no_results") {
                $def = "No results. try harder next time";
            } else {
                if ($definiton["list"]["0"]["definition"]) {
                    $def = $definiton["list"]["0"]["definition"];
                } else {
                    $def = $definiton["list"]["definition"];
                }
            }
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $def));
        } else if (strpos($text, "/bitcoin") === 0) {
            $btcjson = file_get_contents("https://api.coindesk.com/v1/bpi/currentprice.json");
            $btcdata = json_decode($btcjson, true);
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => 'BTC -> USD: $' . $btcdata["bpi"]["USD"]["rate"]));
        } else if (strpos($text, "/austin") === 0) {
            $austin = file_get_contents("https://zhang.nz/austin.php?api=1");
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $austin));
        } else if (strpos($text, "/audiotest") === 0) {
            global $rick;
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "voice" => $rick));
        } else if (strpos($text, "/asb") === 0) {
            $asb = file_get_contents("https://zhang.nz/botgen/asb.php");
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $asb));
        } else if (strpos($text, "/bs") === 0) {
            $asb = file_get_contents("https://zhang.nz/botgen/corporatebs.php");
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $asb));
        } else if (strpos($text, "/globalwarming") === 0) {
            $asb = file_get_contents("https://zhang.nz/botgen/donald.php");
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $asb));
        } else if (strpos($text, "/about") === 0) {
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => 'Intestine Bot by Edward Zhang https://zhang.nz/'));
        } else if (strpos($text, "/westpac") === 0) {
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => '紅色是一個很好的顏色'));
        } else if (strpos($text, "/drone") === 0) {
            //$yigetime = floor((time() - 1459331689)/86400);
            apiRequestWebhook("sendPhoto", array('chat_id' => $chat_id, "photo" => "https://zhang.nz/botgen/yigedrone.php?meme=" . time()));
        } else if (strpos($text, "/asus") === 0) {
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => 'sns∀'));
        } else if (strpos($text, "/quake") === 0) {
            $args = preg_split("/[\s,]+/", $text);
            if (empty($args[1])) {
                $args[1] = 0;
            }
            if (empty($args[2])) {
                $args[2] = 5;
            }
            $geonetjson = file_get_contents('https://api.geonet.org.nz/quake?MMI=' . $args[2]);
            $geonetdata = json_decode($geonetjson, true);
            $unixtime_quake = strtotime($geonetdata["features"][(int)$args[1]]["properties"]["time"]);
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true, "text" => '*Last (' . $args[1] . 'th before) earthquake with MMI > ' . $args[2] . ':*
        *Time:* ' . date('g:i:s A O', $unixtime_quake) . ' (' . time_since(time() - $unixtime_quake) . ' ago)
        *Depth:* ' . round($geonetdata["features"][(int)$args[1]]["properties"]["depth"], 1) . ' km
        *Magnitude:* ' . round($geonetdata["features"][(int)$args[1]]["properties"]["magnitude"], 1) . '
        *Location:* ' . $geonetdata["features"][(int)$args[1]]["properties"]["locality"] . '
        *Quality:* ' . $geonetdata["features"][(int)$args[1]]["properties"]["quality"] . '
        Map using /mapquake\_' . $geonetdata["features"][(int)$args[1]]["properties"]["publicID"] . '
        [More info on GeoNet](https://www.geonet.org.nz/quakes/' . $geonetdata["features"][(int)$args[1]]["properties"]["publicID"] . ')',));
        } else if (strpos($text, "/mapquake_") === 0) {
            global $gm_key;
            $args = preg_split("/_|@/", $text);
            $pubID = $args[1];
            $geonetjson = file_get_contents('https://api.geonet.org.nz/quake/' . $pubID);
            $geonetdata = json_decode($geonetjson, true);
            $coord = $geonetdata["features"][0]["geometry"]["coordinates"];
            $mapsURL = 'https://maps.googleapis.com/maps/api/staticmap?zoom=8&size=480x480&markers=' . $coord[1] . ',' . $coord[0] . '&key=' . $gm_key . '&maptype=hybrid';
            apiRequestWebhook("sendPhoto", array('chat_id' => $chat_id, "photo" => $mapsURL));
            //apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $pubID));
        } else if (stripos($text, Commands::samecount) === 0) {
            $args = preg_split("/[\s,]+/", $text);
            if (empty($args[1])) {
                $args[1] = '@' . $sender[username];
                //apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => 'Please supply a user'));
            }
            $file = fopen('same/same_' . substr($args[1], 1) . '.txt', "r");
            $strike = fgets($file);
            if (empty($strike)) {
                apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => 'No info found'));
            }
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $args[1] . ' has said same ' . $strike . ' times'));
        } else if (stripos($text, Commands::comic) === 0) {
            $args = preg_split("/[\s,]+/", $text);
            if (empty($args[1])) {
                $args[1] = 6;
            } else if ($args[1] > 20) {
                apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => 'No.'));
            }
            $thyme = time();
            global $servername, $username, $password, $dbname;
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            $sql = "SELECT * FROM `chatlog_ece` WHERE `chatid` = " . $chat_id . " ORDER BY `datetime` DESC LIMIT " . $args[1];
            $result = mysqli_query($conn, $sql);
            for ($i = ($args[1] - 1); $i >= 0; $i--) {
                $array = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $string = $array['message'];
                $string = (strlen($string) > 63) ? substr($string, 0, 60) . '...' : $string;
                $msg[$i] = $array['userid'] . " \"" . $string . "\"";
            }
            mysqli_close($conn);
            // we've got the array now
            if (stripos($text, Commands::comic1) === 0) {
                $command = "mono ./sassygen/ComicGenerator.exe \"comic_" . $thyme . ".png\" \"juice\" ";
            } else if (stripos($text, Commands::comic2) === 0) {
                $command = "mono ./sassygen/ComicGenerator.exe \"comic_" . $thyme . ".png\" \"eddy\" ";
            } else {
                $command = "mono ./sassygen/ComicGenerator.exe \"comic_" . $thyme . ".png\" \"nik\" ";
            }
            for ($i = 0; $i < $args[1]; $i++) {
                $command = $command . $msg[$i] . " ";
            }
            exec("rm comic_*.png");
            $result = exec($command);
            //apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => "executed; result = ".$result));
            sleep(1);
            apiRequestWebhook("sendPhoto", array('chat_id' => $chat_id, "photo" => "https://zhang.nz/231285106/intestinebot/comic_" . $thyme . ".png"));
        } else if (stripos($text, Commands::wow) === 0) {
            global $servername, $username, $password, $dbname;
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            $sql = "SELECT * FROM `chatlog_ece` WHERE `chatid` = " . $chat_id . " ORDER BY `datetime` DESC LIMIT 1";
            $result = mysqli_query($conn, $sql);
            $array = mysqli_fetch_array($result, MYSQLI_ASSOC);
            mysqli_close($conn);
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => "Wow " . $array['firstname']));
        } else if (stripos($text, Commands::doggo) === 0) {
            global $client_id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/gallery/r/doggos/time/');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
            $reply = curl_exec($ch);
            curl_close($ch);
            $reply = json_decode($reply, true);
            $randomID = rand(0, 99);
            $caption = $reply["data"][$randomID]["title"];
            $link = $reply["data"][$randomID]["link"];
            //apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $caption));
            apiRequestWebhook("sendPhoto", array('chat_id' => $chat_id, "photo" => $link, "caption" => $caption));
        } else if (stripos($text, Commands::lauren) === 0) {
            $text = substr($text, 8);
            function scramble_word($word)
            {
                if (strlen($word) < 2)
                    return $word;
                else
                    if (rand(0, 2) == 0) {
                        return strtoupper($word{0} . str_shuffle(substr($word, 1, -1)) . $word{strlen($word) - 1});
                    } else {
                        return $word{0} . str_shuffle(substr($word, 1, -1)) . $word{strlen($word) - 1};
                    }
            }

            $scrambled = preg_replace('/(\w+)/e', 'scramble_word("\1")', $text);
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $scrambled));
        } else if (stripos($text, Commands::aesthetic) === 0) {
            $text = substr($text, 11);
            $fullwidth = mb_convert_kana($text, "A");
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $fullwidth));
        } else if (stripos($text, Commands::shitpost) === 0) {
            $text = substr($text, 10);
            $exploded = explode(" ", $text);
            $out = "";
            // 👏
            foreach ($exploded as $word) {
                $out = $out . " " . strtoupper($word) . " 👏";
            }
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $out));
        } else if (stripos($text, Commands::help) === 0) {
            $reflect = new ReflectionClass(get_class(Commands::class));
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => implode("\n", $reflect->getConstants())));
        } else if ((stripos($text, "same") !== false) && (stripos($text, Commands::samecount) !== 0)) {
            // this really really needs to be updated to just use SQL
            $file = fopen('same/same_' . $sender[username] . '.txt', "r");
            $strike = fgets($file);
            if (empty($strike)) {
                $strike = 1;
            } else {
                $strike++;
            }
            $file = fopen('same/same_' . $sender[username] . '.txt', "w");
            fwrite($file, $strike);
            fclose($file);
            //
            $probf = fopen('sameprob.txt', "r");
            $probr = fgets($probf);
            if ($probr <= 2) {
                $prob = $probr;
            } else {
                $prob = ceil($probr / 2);
            }
            $probf = fopen('sameprob.txt', "w");
            if (rand(1, $probr) == 1) {
                $prob = 100;
                apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => $sender[first_name] . ', you can\'t just keep saying same. (probability was 1/' . $probr . ', setting to 1/' . $prob . ')'));
            } else {
//            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => 'same probability set to 1/'.$prob));
            }
            fwrite($probf, $prob);
            fclose($probf);
        } //else if (strtolower($text) == "oh no") {
        //  apiRequestWebhook("sendPhoto", array('chat_id' => $chat_id, "photo" => "http://68.media.tumblr.com/avatar_78d0e9a0b226_128.png"));
        // }


        // Stores messages into db
        global $servername, $username, $password, $dbname;
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        $text = mysqli_real_escape_string($conn, $text);
        $sql = "INSERT INTO `chatlog_ece` (`chatid`, `message_id`, `userid`, `datetime`, `username`, `firstname`, `message`) VALUES (" . $chat_id . ", '" . $message['message_id'] . "', '" . $sender['id'] . "', FROM_UNIXTIME(" . $message['date'] . "), '" . $sender['username'] . "', '" . $sender[first_name] . "', '" . $text . "')";
        mysqli_query($conn, $sql);
        mysqli_close($conn);
    } else if (isset($message['photo'])) {
        //if (isset($message['photo'][3])){
        //    apiRequestWebhook("getFile", array('file_id' => $message['photo'][3]['file_id']));
        //    apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => 'File ID: '.$message['photo'][3]['file_id']));
        //}
    }
}


define('WEBHOOK_URL', 'https://zhang.nz/231285106/intestinebot/eddybot.php');

if (php_sapi_name() == 'cli') {
    // if run from console, set or delete webhook
    apiRequest('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));
    exit;
}


$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    // receive wrong update, must not happen
    exit;
}

if (isset($update["message"])) {
    processMessage($update["message"]);
}
?>
