<?php


include "sites.php";

$gogoanime=new GoGoAnime();

header("text/json");

if($_GET["action"]=="getshows")
  echo json_encode($sites[$_GET["site"]]->GetShows($_GET["url"]));
else if($_GET["action"]=="getsources")
  echo json_encode($sites[$_GET["site"]]->GetSources($_GET["url"]));
else if($_GET["action"]=="getepisodes")
  echo json_encode($sites[$_GET["site"]]->GetEpisodes($_GET["url"]));
  ?>
