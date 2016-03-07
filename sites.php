<?php



abstract class Site {
  function GetSiteName(){
    return get_class($this);
  }
  function GetShowObject($title,$url){
    $show["name"]=$title;
    $show["url"]=$url;
    $show["type"]="show";
    $show["site"]=$this->GetSiteName();
    return $show;
  }
  function GetEpisodeObject($title,$url){
    $show["name"]=$title;
    $show["url"]=$url;
    $show["type"]="episode";
    $show["site"]=$this->GetSiteName();
    return $show;
  }
  function GetSourceObject($title,$url){
    $show["name"]=$title;
    $show["url"]=$url;
    $show["type"]="source";
    $show["site"]=$this->GetSiteName();
    return $show;
  }
  abstract function GetShows();
  abstract function GetEpisodes($url);
  abstract function GetSources($url);
}

class GoGoAnime extends Site {
  function GetShows(){
    $html = file_get_contents('http://www.gogoanime.com/watch-anime-list');

    while(true){

      $html = strstr($html,"<li class=\"cat-item");

      if($html==FALSE)
        break;

      $html = strstr($html,"<a href=\"");
      $html = substr($html,9);
      $url = substr($html,0,strpos($html,"\""));
      $html = substr($html,strlen($url));
      $html = strstr($html,"\">");
      $html = substr($html,2);
      $title = substr($html,0,strpos($html,"<"));
      $html = substr($html,strlen($title));

      $items[]=$this->GetShowObject($title,$url);
    }

    return $items;
  }
  function GetEpisodes($url){
    $html = file_get_contents("http://www.gogoanime.com/category/".$url);

    while(true){

      $html = strstr($html,"class=\"postlist\"");

      if($html == FALSE)
        break;

      $html = strstr($html,"<a href=\"");
      $html = substr($html,9);
      $url = substr($html,0,strpos($html,"\""));
      $html = substr($html,strlen($url));
      $html = strstr($html,"title=\"");
      $html = substr($html,7);
      $title = substr($html,0,strpos($html,"\""));
      $html = substr($html,strlen($title));

      $items[]=$this->GetEpisodeObject($title,$url);
    }

    return $items;
  }
  function GetSources($url){
    $html = file_get_contents($url);

    $html = strstr($html,"class=\"postcontent\"");

    while(true){

      $html = strstr($html,"<iframe");

      if($html==FALSE)
        break;

      $html = strstr($html,"src=\"");
      $html = substr($html,5);
      $url = substr($html,0,strpos($html,"\""));
      $html = substr($html,strlen($url));

      if(substr($url,0,4)=="http")
        $items[]=$this->GetSourceObject("", $url);
    }

    return $items;
  }
}
class Cucirca extends Site {
  function GetShows(){
    $html = file_get_contents('http://cucirca.eu/');

    $html = strstr($html,"<ul><li>");

    while(true){

      $html = strstr($html,"<li><a href=\"");

      if($html==FALSE)
        break;

      $html = substr($html,13);
      $url = substr($html,0,strpos($html,"\""));
      $html = substr($html,strlen($url));
      $html = strstr($html,"\">");
      $html = substr($html,2);
      $title = substr($html,0,strpos($html,"<"));
      $html = substr($html,strlen($title));

      $items[]=$this->GetShowObject($title,$url);
    }

    return $items;
  }
  function GetEpisodes($url){
    $html = file_get_contents($url);

    while(true){

      if($html==FALSE)
        break;

      $html = strstr($html,"<a href=\"");
      $html = substr($html,9);
      $url = substr($html,0,strpos($html,"\""));
      $html = substr($html,strlen($url)+2);
      $title = substr($html,0,strpos($html,"</a>"));
      $html = substr($html,strlen($title));

      if(substr($title,0,7)=="Episode")
        $items[]=$this->GetEpisodeObject($title,$url);
    }

    return $items;
  }
  function GetSources($url){
    $html = file_get_contents($url);
    $html = strstr($html, "postid-");
    $html = substr($html, 7, strpos($html, " ") - 7);
    $html = file_get_contents("http://cucirca.eu/getvideo.php?id=" . $html . "&nr=1");
    $html = strstr($html, "<IFRAME");
    $html = strstr($html, "SRC=\"");
    $html = substr($html, 5);
    $url = substr($html, 0, strpos($html,"\""));

    $source["url"] = $url;

    $items[] = $this->GetSourceObject("", $url);

    return $items;
  }
}
$sites["GoGoAnime"]=new GoGoAnime();
$sites["Cucirca"]=new Cucirca();




?>
