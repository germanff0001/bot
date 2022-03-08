<?php
$token = '5114395699:AAHkkf9z-RLpdUSnG07btSN0jjVo82tyVI4';
$website = 'https://api.telegram.org/bot'.$token;

$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];
$reply = $update['message']['reply_to_message']['text'];

    switch($message) {
        case '/start':
            $response = 'Me has iniciado';
            sendMessage($chatId, $response, FALSE);
            break;
        case '/info':
            $response = 'Hola! Soy @GermanBot';
            sendMessage($chatId, $response, FALSE);
            break;
        case '/ayuda':
            $response = "Tranquilo, estoy contigo.";
            sendMessage($chatId, $response, FALSE);
            break;
        case '/noticias':
            $response = "¿Sobre que quieres ver la noticia?";
            sendMessage($chatId, $response, TRUE);
            break;
        case 'politica':
                sendMessage($chatId, $response, FALSE);
            break;
        case 'deportes':
            sendMessage($chatId, $response, FALSE);
        break;
        case 'tecnologia':
            sendMessage($chatId, $response, FALSE);
        break;
        case 'cultura':
            sendMessage($chatId, $response, FALSE);
        break;
        default:
        $response = 'No te he entendido';
        sendMessage($chatId, $response, FALSE);
        break;   
    }


    if ($reply=="¿Sobre que quieres ver la noticia?"){
        $categoria = $message;
        consultar_categoria($chatId,$categoria);
    
    }



function sendMessage($chatId, $response,$reply_a){

    if ($reply_a == TRUE){
        
        $reply_mark=array('force_reply'=>True);
        $url= $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&reply_markup='.json_encode($reply_mark).'&text='.urlencode($response);
    }
    else $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
    file_get_contents($url);
    
}


function consultar_categoria($chatId,$categoria){

    if ($categoria == 'politica'){
        $urlcategoria = "https://www.europapress.es/rss/rss.aspx?ch=00066";

    }elseif ($categoria == "deportes"){
        $urlcategoria="https://www.europapress.es/rss/rss.aspx?ch=00067";
    }elseif ($categoria == "tecnologia"){
        $urlcategoria="https://www.europapress.es/rss/rss.aspx?ch=00564";
    }elseif ($categoria == "cultura"){
    $urlcategoria="https://www.europapress.es/rss/rss.aspx?ch=00126";
    }


    $context = stream_context_create(array('http' =>  array('header' => 'Accept: application/xml')));

    $xmlstring = file_get_contents($urlcategoria, false, $context);
 
    $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
    $json = json_encode($xml);
    $array = json_decode($json, TRUE);
 
    for ($i=0; $i < 9; $i++) { 
        $titulos = $titulos."\n\n".$array['channel']['item'][$i]['title']."<a href='".$array['channel']['item'][$i]['link']."'> +info</a>";
    }
 
    sendMessage($chatId, $titulos,FALSE);
}

?>