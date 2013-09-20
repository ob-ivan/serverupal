<?php
function writelog ($url, $success) {

    $fn = 'log.txt';
    $msg = date('Y-m-d H:i:s') . "\t" . $url . "\t" . ($success ? 'up' : 'down');

    $f = fopen ($fn, 'ab');
    fwrite ($f, $msg . "\r\n");
    fclose ($f);
}


function main () {
    $q = trim($_SERVER['QUERY_STRING']);
    if (! empty ($q)) {
        $q = preg_replace ('~^http://~i', '', $q);
        if (! preg_match ('~^(([a-z\d\.\-]+)\.[a-z]{2,4}).*$~i', $q, $matches)) {
            $return = 'Что-то <a href="' . $q . '">' . $q . '</a> не слишком похоже на адрес сайта.';
        }
        else {
            $q = 'http://' . substr($matches[1], 0, 255) . '/';
            $f = @file_get_contents($q);
            $success = ! empty($f);

            /* // Это как-нибудь потом
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $q);
            curl_setopt($c, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($c, CURLOPT_NOBODY, true);
            curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 5);
            curl_exec($c);
            curl_close($c);
            */

            if ($success) {
                $return = 'Да нет, сервер <a href="' . $q . '">' . $q . '</a> вполне себе работает.';
            }
            else {
                $return = 'Увы, да, сервер <a href="' . $q . '">' . $q . '</a> лежит в лёжку.';
            }
            writelog ($q, $success);
        }
        return $return . '<br/><a href="?">Проверь другой сайт!</a>';
    }
    return
        '<form onsubmit="return serverupal()">' .
            'Что с сайтом <input id="url" type="text" placeholder="http://example.com/"/>? ' .
            'Неужели <a href="#" onclick="serverupal()" onkeyup="serverupal()">сервер упал</a>?' .
            '<input type="submit" style="display:none"/>' .
        '</form>';
}

header ('Content-Type: text/html; charset=utf-8');
?>
<html>
    <head>
        <title>Сервер упал? Проверь!</title>
        <script type="text/javascript" src="script.js"></script>
        <link rel="stylesheet" href="style.css"/>
        <meta name="Description" content="Сервер упал? Сервис для проверки работы сайта."/>
    </head>
    <body>
        <div>
            <?php print main() ?>
        </div>
    </body>
</html>
