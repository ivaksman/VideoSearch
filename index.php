<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>

  <title>Online Video Search</title>

  <style>

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

<div style="margin:60px auto 0px auto;width:800px">

<div>

<h3>Sites Loaded</h3>

<div id="sites">
</div>

</div>

<div style="width:600px">

<h3>Online Video Search</h3>

<iframe id="display" src="" style="width:600px;height:300px;margin:0px;padding:0px">
</iframe>

<div>
<input id="input" onkeyup="displayshows(findshows(this.value))" placeholder="Search Shows or Movies" style="width:500px" />
<input id="inputgo" type="button" value="Go" />
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

  for(var a=0;a<words.length;a++){
    if(words[a]=="")
      continue;

    if(typeof index[words[a]]!="object")
      index[words[a]]=new Array();

    index[words[a]].push(show)
  }
}

function addshowstoindex(shows){
  for(var a=0;a<shows.length;a++){
    addtoindex(shows[a]);
  }
}

function displaysources(sources){
  document.getElementById("output").innerHTML="";
  for(var c=0;c<sources.length;c++){
    var elem=document.createElement("div");
    elem.innerHTML=sources[c].url;
    elem.className="sourcesitem";
    (function(){
      var b=c;
      elem.onclick=function() {
        document.getElementById("display").src=sources[b].url;
      }
    })();
    document.getElementById("output").appendChild(elem);
  }
}

function displayepisodes(episodes){
  document.getElementById("output").innerHTML="";
  for(var c=0;c<episodes.length;c++){
    var elem=document.createElement("div");
    elem.innerHTML=episodes[c].name;
    elem.className="episodeitem";
    (function(){
    var b=c;
    elem.onclick=function() {
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
          var links=JSON.parse(xhttp.responseText);
          if(links.length==1)
            document.getElementById("display").src=links[0].url;
          else
            displaysources(links);
        }
      };
      xhttp.open("GET", "actions.php?action=getsources&site="+encodeURIComponent(episodes[b].site)+"&url="+episodes[b].url, true);
      xhttp.send();
    }
    })();
    document.getElementById("output").appendChild(elem);
  }
}

function displayshows(shows){
  document.getElementById("output").innerHTML="";
  for(var c=0;c<shows.length;c++){
    var elem=document.createElement("div");
    elem.innerHTML=shows[c].name;
    elem.className="showitem";
    (function(){
    var b=c;
    elem.onclick=function() {
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
          var links=JSON.parse(xhttp.responseText);
          displayepisodes(links);
        }
      };
      xhttp.open("GET", "actions.php?action=getepisodes&site="+encodeURIComponent(shows[b].site)+"&url="+encodeURIComponent(shows[b].url), true);
      xhttp.send();
    }
    })();
    document.getElementById("output").appendChild(elem);
  }
}

function loadsites(sites){
  document.getElementById("output").innerHTML="";
  for(var c=0;c<sites.length;c++){    
    sites[c].loaded=false;
    (function(){
    var b=c;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
        if(sites[b].loaded)
          return;
        var links=JSON.parse(xhttp.responseText);
        addshowstoindex(links);
        sites[b].loaded=true;
        var elem=document.createElement("span");
        elem.innerHTML=sites[b];
        elem.className="showitem";
        elem.style.margin="0px 40px 0px 0px";
        document.getElementById("sites").appendChild(elem);
      }
    };
    xhttp.open("GET", "actions.php?action=getshows&site="+encodeURIComponent(sites[c])+"&url="+encodeURIComponent(sites[c]), true);
    xhttp.send();
    })();
  }
}

    function findshows(input){
      var words=input.split(" ");
      var refs=[];
      var retshows=[];

      for(i=0;i<words.length;i++){
        var word=words[i];
        for(k=0;k<index[word].length;k++){
          var ref=index[word][k];

          refs.push(ref);
        }
      }

      return refs;
    }

document.getElementById("input").onkeyup=function(event){
  displayshows(findshows(document.getElementById("input").value));
}

document.getElementById("input").ontouchend=function(event){
  displayshows(findshows(this.value));
}

document.getElementById("inputgo").onclick=function(event){
  displayshows(findshows(document.getElementById("input").value));
}

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
