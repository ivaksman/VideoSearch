<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>

  <title>Online Video Search</title>

  <style>

  #container {
    margin:60px auto 0px auto;width:600px
  }

  #display {
    width:600px;height:300px;margin:0px;padding:0px
  }

  .block {
    display: block;
    float: left;
    width: 20%;
    overflow: hidden;
  }

  .showitem {
    cursor:pointer;
  }

  .showitem:hover {
    color:white;
    background:#4cc;
  }

  .episodeitem {
    cursor:pointer;
  }

  .episodeitem:hover {
    color:white;
    background:#4cc;
  }

  .sourcesitem {
    cursor:pointer;
  }

  .sourcesitem:hover {
    color:white;
    background:#4cc;
  }

  </style>
</head>
<body>

<div id="container">

<div>

<h3>Sites Loaded</h3>

<div id="sites">
</div>

</div>

<div>

<h3>Online Video Search</h3>

<iframe id="display" src="" >
</iframe>

<div>
<input id="input" onkeyup="displayshows(findshowsinindex(this.value))" placeholder="Search Shows or Movies" style="width:600px" />
<!--<input id="inputgo" type="button" value="Go" />-->
</div>

<h3>Results</h3>

<div id="output" style="overflow:hidden">
</div>

</div>
<!--
<div style="float:left;margin-left:40px">

<h3>Sites Loaded</h3>

<div id="sites">
</div>

</div>
-->
<br style="clear:both" />

</div>


  <script>

var index={};

function addtoindex(show){
  var words=show["name"].toLowerCase().split(" ");

  words.map(function(word) {
    if(word=="")
      return;

    if(typeof index[word]!="object")
      index[word]=new Array();

    index[word].push(show)
  })
}

function findshowsinindex(input){
  var words=input.toLowerCase().split(" ");
  var refs=[];

  words.map(function(word) {
    index[word].map(function(ref) {
      refs.push(ref);
    })
  })

  return refs;
}

function displayelem(c, html, click) {
  var elem=document.createElement("div");
  elem.innerHTML=html;
  elem.className=c;
  elem.onclick=click
  return elem
}

function setajax(url, callback) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
      var links=JSON.parse(xhttp.responseText);
      callback(links)
    }
  };
  xhttp.open("GET", url, true);
  xhttp.send();
}

function displaysources(sources){
  document.getElementById("output").innerHTML="";
  sources.map(function(source) {
    document.getElementById("output").appendChild(displayelem("sourcesitem", source.url, function() {
      document.getElementById("display").src=source.url;
    }));
  })
}

function displayepisodes(episodes){
  document.getElementById("output").innerHTML="";
  episodes.map(function(episode) {
    document.getElementById("output").appendChild(displayelem("episodeitem", episode.name, function() {
      setajax("actions.php?action=getsources&site="+encodeURIComponent(episode.site)+"&url="+episode.url, function(links) {

        if(links.length==1)
          document.getElementById("display").src=links[0].url;
        else
          displaysources(links);

      })
    }));
  })
}

function displayshows(shows){
  document.getElementById("output").innerHTML="";
  shows.map(function(show) {
    document.getElementById("output").appendChild(displayelem("showitem", show.name, function() {
      setajax("actions.php?action=getepisodes&site="+encodeURIComponent(show.site)+"&url="+encodeURIComponent(show.url), function(links) {
        displayepisodes(links);
      })
    }));
  })
}

function loadsites(sites){
  document.getElementById("output").innerHTML="";
  sites.map(function(site) {
    site.loaded=false;
    setajax("actions.php?action=getshows&site="+encodeURIComponent(site)+"&url="+encodeURIComponent(site), function(links) {
      if(site.loaded)
          return;

        site.loaded=true;

        links.map((c) => addtoindex(c))

        var elem=document.createElement("span");
        elem.innerHTML=site;
        elem.className="siteitem";
        elem.style.margin="0px 40px 0px 0px";
        document.getElementById("sites").appendChild(elem);
    })
  })
}


document.getElementById("input").onkeyup=function(event){
  displayshows(findshowsinindex(document.getElementById("input").value));
}

document.getElementById("input").ontouchend=function(event){
  displayshows(findshowsinindex(this.value));
}
/*
document.getElementById("inputgo").onclick=function(event){
  displayshows(findshowsinindex(document.getElementById("input").value));
}
*/
    </script>


<?php

include "sites.php";

foreach($sites as $key => $val){
  $sitenames[] = $key;
}
?>


<script>

loadsites(<?=json_encode($sitenames)?>)

</script>

</body>
</html>
