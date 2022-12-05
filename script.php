<?php

function curl_get_contents($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    // For https connections, we do not require SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    $content = curl_exec($ch);
    //$error = curl_error($ch);
    //$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $content;
}


$fileCotent = @curl_get_contents("https://highload.today/category/novosti/");
$doc = new DomDocument();
//$doc->validateOnParse = true;
@$doc->loadHtml($fileCotent);
//echo $doc->saveHTML();
$finder = new DomXPath($doc);
$articlesNodes = $finder->query("//*[contains(@class, 'lenta-item')]");

$articles = [] ;

foreach($articlesNodes as $key => $node) {
    if ($key == 0) continue;

    $articles[]= ['description'=>$articlesNodes[$key]->lastElementChild->nodeValue,
                  'title'=>$articlesNodes[$key]->childNodes[5]->nodeValue,
                  'image'=>$articlesNodes[2]->childNodes[10-1]->childNodes[1]->childNodes[1]->attributes[6]->textContent,
                 ];
}


//var_dump($articlesNodes[1]->lastElementChild->nodeValue );//desription
//var_dump($articlesNodes[2]->childNodes[5] );//title
//var_dump($articlesNodes[2]->childNodes[10-1]->childNodes[1]->childNodes[1]->attributes[6]->textContent );//image
var_dump($articles);//