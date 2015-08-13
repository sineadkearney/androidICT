<?php

	
  //for($n=0; $n<14; $n++) {
  
	$xmlDoc=new DOMDocument();
	// $xmlDoc->load("links".$i.".xml");
	
	$n=$_GET["n"];
	$xmlDoc->load("links".$n.".xml");

	$x=$xmlDoc->getElementsByTagName('link');

	//get the q parameter from URL
	$q=$_GET["q"];

	//lookup all links from the xml file if length of q>0
	if (strlen($q)>0) {
	  $hint="";
	  for($i=0; $i<($x->length); $i++) {
		$a=$x->item($i)->getElementsByTagName('term');
		$b=$x->item($i)->getElementsByTagName('string');
		$c=$x->item($i)->getElementsByTagName('url');
		$d=$x->item($i)->getElementsByTagName('title');
		
		if ($a->item(0)->nodeType && $a->item(0)->nodeType==1) {
		  //find a link matching the search text
		  if (stristr($a->item(0)->childNodes->item(0)->nodeValue,$q)) { //if the value in <term> is equal to the q parameter
			if ($hint=="") {
			  $hint="<a class='searchResult' href='" . 
			  $c->item(0)->childNodes->item(0)->nodeValue . 
			  "'>+ " . 
			  $d->item(0)->childNodes->item(0)->nodeValue . "</a>".
			  "<p>". $b->item(0)->childNodes->item(0)->nodeValue. "</p>";
			} else {
			  // $hint=$hint . "<br /><a class='searchResult' href='" . 
			  $hint=$hint . "<a class='searchResult' href='" . 
			  $c->item(0)->childNodes->item(0)->nodeValue . 
			  "'>+ " . 
			  $d->item(0)->childNodes->item(0)->nodeValue . "</a>".
			  "<p>". $b->item(0)->childNodes->item(0)->nodeValue. "</p>";
			}
		  }
		}
	  }
	}


if ($hint!="") {
  echo $hint;
}

?>