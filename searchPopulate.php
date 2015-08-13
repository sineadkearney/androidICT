<!DOCTYPE html>
<html lang="en">

<head>
	<link rel="icon" type="image/png"  href="images/Android_robot.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Android Tablets in the Classroom</title>

    <!-- Bootstrap core CSS -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="css/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin-2.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

	    <!-- jQuery -->
    <script src="../js-libraries/jquery-2.1.1.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../js-libraries/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
	
	<script src="js/androidICT.js"></script>
	
	<script>
	

var xmlString = "<pages>";
/**************** for search only ************************/
function readTextFile(file)
{
	var allText = "";
	
    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", file, false);
    rawFile.onreadystatechange = function ()
    {
        if(rawFile.readyState === 4)
        {
            if(rawFile.status === 200 || rawFile.status == 0)
            {
				var el = document.createElement( 'test' );
				el.innerHTML = rawFile.responseText;
				
				var title = el.getElementsByClassName("page-header");
				title = title[0]; //the above returns an array. Assume that if there is more than one header, we only want the first
				title = title.innerHTML;

				var pageWrapper = "";
				for (var i = 0; i < el.children.length; i++)
				{
					if (el.children[i].id == "wrapper")
					{
						pageWrapper = el.children[i].children[1];
					}
				}
				
				//add all lists with IDs
				var allUltags = pageWrapper.getElementsByTagName( 'ul' );
				for (var i = 0; i < allUltags.length; i++)
				{
					var id = allUltags[i].id;
					if (id != "")
					{
						var content = allUltags[i].innerHTML;
						var arr = SplitStringIntoArray(content);
						// console.log(arr);
						
						var wordAmountEitherSide = 6;
						AddParagrphContentToXml(arr, wordAmountEitherSide, title, id);
					}
				}
				
				//add all h4 entries with IDs
				var allH4tags = pageWrapper.getElementsByTagName( 'h4' );
				for (var i = 0; i < allH4tags.length; i++)
				{
					var id = allH4tags[i].parentElement.id;
					
					if (id != "")
					{
						var content = allH4tags[i].innerHTML;
						var arr = SplitStringIntoArray(content);
						
						var wordAmountEitherSide = 6;
						AddParagrphContentToXml(arr, wordAmountEitherSide, title, id);
					}
				}
				
				//find text in all stand-alone paragraphs
				var allPtags = pageWrapper.getElementsByTagName( 'p' )
				for (var i = 0; i < allPtags.length; i++)
				{
					var parentElementClass = allPtags[i].parentElement.className;
					if (parentElementClass.indexOf('popover-icon') != -1) //if the parent is in the popover-icon class
					{
						var id = allPtags[i].parentElement.id;
						console.log("id: " + id);
						var linkTitle = id.replace(/_/g, " "); //replace all underscores with spaces
						linkTitle = RemoveSpecialChars(linkTitle);
						console.log("linkTitle: " + linkTitle);
						
						if (CheckIfWordIsValid(linkTitle))
						{
							resultString = GenerateXmlEntry(linkTitle, title, linkTitle, id); //(word, title, string, url)
							xmlString += resultString + "\n";
						}
					}
					else //it's a normal paragraph
					{
						var content = allPtags[i].innerHTML;
						if (content != "" && content != " ")
						{
							var arr = SplitStringIntoArray(content);
							
							var id = allPtags[i].id;
							
							if (id != "")
							{
								var wordAmountEitherSide = 6;
								AddParagrphContentToXml(arr, wordAmountEitherSide, title, id);
							}
						}
					}
				}
				
				//parse the data in the data-content section of a .popover-icon div
				var allPopPverIcons = pageWrapper.getElementsByClassName('popover-icon');
				
				for (var i = 0; i < allPopPverIcons.length; i++)
				{
					var id = allPopPverIcons[i].attributes['id'].value;
					console.log("id: " + id);
					var linkTitle = id.replace(/_/g, " "); //replace all underscores with spaces
					linkTitle = RemoveSpecialChars(linkTitle);
					// console.log("linkTitle: " + linkTitle);
					
					var dataContent = allPopPverIcons[i].attributes['data-content'];
					dataContent = dataContent.value;
					if (dataContent.indexOf("Description:") != -1) //if it contains "Description:"
					{
						
						//console.log(dataContent);
						dataContent = dataContent.substr(dataContent.indexOf('</strong>')+9);
						var desc = dataContent.substr(0, dataContent.indexOf('</p>'));
						desc = RemoveSpecialChars(desc);
						// console.log(desc);
						
						var descArr = desc.split(' '); //turn the string into an array, where each element is a word (seperated by ' ');
						var wordAmountEitherSide = 6;
						for (var n = 0; n < descArr.length; n++)
						{
							var word = descArr[n];
							
							if (CheckIfWordIsValid(word))
							{
								var resultString = "";
								
								var startStringAtWordIndex = n - wordAmountEitherSide;
								var endStringAtWordIndex = n + wordAmountEitherSide;
								var searchWordIndex = n;
								
								if (startStringAtWordIndex < 0) startStringAtWordIndex = 0;
								if (endStringAtWordIndex > descArr.length-1) endStringAtWordIndex = descArr.length-1;
								
								//create the results string
								for (var k = startStringAtWordIndex; k <= endStringAtWordIndex; k++)
								{
									if (k == searchWordIndex)
										resultString += "==b=" + descArr[k] + '=b== '; //these will be replaces with "<strong>" and "</strong>" later
									else
										resultString += descArr[k] + ' ';
								}
									
								//remove all puncutation from the word
								word = word.replace(/[.,-\/#!$%\^&\*;:{}=\-_`~()?]/g,"")
								resultString = GenerateXmlEntry(word, linkTitle, resultString, id);
								xmlString += resultString + "\n";
							}
						}
					}
				}
				
				// xmlString += "</pages>";
				// console.log(xmlString);
				
				// save xml string to links.xml
				// var data = new FormData();
				// data.append("data" , xmlString);
				// var xhr = (window.XMLHttpRequest) ? new XMLHttpRequest() : new activeXObject("Microsoft.XMLHTTP");
				// xhr.open( 'post', 'saveToFile.php', true );
				// xhr.send(data);
				// console.log("sent");

            }
        }
    }
    rawFile.send(null);
}

function SplitStringIntoArray(string)
{
	string = RemoveSpecialChars(string);
	console.log(string);
						
	var arr = string.split(' '); //turn the string into an array, where each element is a word (seperated by ' ');
	return arr;			
}

function RemoveSpecialChars(string)
{
	if (string.indexOf(" & ") != -1)
	{
		var test = ""; //for debugging
	}
	string = string.replace(/[\n\r\t]/g, ''); //remove all newLine and tab characters
	string = string.replace(/<(?:.|\n)*?>/gm, ''); //remove all html tags
	string = string.replace(/&/g, 'and'); //replace '&' with 'and' ('&' is invalid in xml)
	string = string.replace(/\//g, ' '); //replace '/' with ' '
	
	return string;
}

function AddParagrphContentToXml(arr, wordAmountEitherSide, linkTitle, id)
{
	for (var n = 0; n < arr.length; n++)
	{
		var word = arr[n];
		if (CheckIfWordIsValid(word))
		{
			var resultString = "";
				
			var startStringAtWordIndex = n - wordAmountEitherSide;
			var endStringAtWordIndex = n + wordAmountEitherSide;
			var searchWordIndex = n;
								
			if (startStringAtWordIndex < 0) startStringAtWordIndex = 0;
			if (endStringAtWordIndex > arr.length-1) endStringAtWordIndex = arr.length-1;
								
			//create the results string
			for (var k = startStringAtWordIndex; k <= endStringAtWordIndex; k++)
			{
				if (k == searchWordIndex)
					resultString += "==b=" + arr[k] + '=b== '; //these will be replaces with "<strong>" and "</strong>" later
				else
					resultString += arr[k] + ' ';
			}
						
			//remove all punctuation from the word
			word = word.replace(/[.,-\/#!$%\^&\*;:{}=\-_`~()?]/g,"")
								
			resultString = GenerateXmlEntry(word, linkTitle, resultString, id);
			xmlString += resultString + "\n";
		}
	}
}

//return false if the word is one of the words that we are ignoring
// var ignoreTheseWords = ['a', 'the', 'of', 'is', 'an', 'and', '', 'in', 'or', 'to', '...', '-', '--', '.....', 'q.', 'a.', ':-)', ':-(', '*', ')', '('];
var ignoreTheseWords = ['a', 'the', 'of', 'is', 'an', 'and', '', 'in', 'or', 'to', 'q', 'i', 'you', 'be', 'can', 'on', 'it', 'that', 'which', 'may', 'for'];
//TODO: all puncutation should be ignored, without having to check ignoreTheseWords
function CheckIfWordIsValid(word)
{
	word = word.replace(/[.,-\/#!$%\^&\*;:{}=\-_`~()?€£$]/g,"") //remove all punctuation from the word
	
	if (!isNaN(word) || word == '' || word == " ") //if the word IS a number.
		return false; //only add words that are not numbers (eg, '100' will return false, but 'hundred' will be true)

		for (var i = 0; i < ignoreTheseWords.length; i++)
		{
			if (word.toLowerCase() == ignoreTheseWords[i])
				return false;
		}

	return true;
}

// <pages>
// 	<link>
//		<term> the word the user searches for </term>
// 		<title> the title of the result which will be a hyperlink </title>
//		<string> shows some of the string in which "term" is found </string>
// 		<url> the hyperlink that "title" uses</url>
// 	</link>
//...
//</pages>
function GenerateXmlEntry(word, title, string, url)
{
	var str = "\n\t<link>\n\t\t<term>"+word+"</term>";
	str += "\n\t\t<title>"+title+"</title>";
	str += "\n\t\t<string>"+string+"</string>";
	url = fileName + "#" + url;
	str += "\n\t\t<url>"+url+"</url>\n\t</link>";
	return str;
}

//Done
// var fileName = "apps-personal.php";
// var fileName = "index.php";
// var fileName = "contact.php";
// var fileName = "android-classroom.php";
// var fileName = "android-os.php";
// var fileName = "android-research.php";
// var fileName = "android-support.php"; 
// var fileName = "android-testimonials.php";
// var fileName = "android-weblinks.php";
// var fileName = "apps-postprimary.php";
// var fileName = "apps-primary.php";
// var fileName = "apps-sen.php";
// var fileName = "apps-teacher.php";
// var fileName = "apps-technical.php";

//to check
// var fileName = "android-hardware.php";
var fileName = "";
$( document ).ready(function() {

	var files = ["index.php", "contact.php", "android-classroom.php", "android-os.php", "android-research.php", "android-support.php", 
	"android-testimonials.php", "android-weblinks.php", "apps-personal.php", "apps-postprimary.php", "apps-primary.php", "apps-sen.php", 
	"apps-teacher.php", "apps-technical.php"];
	
	for (var i = 0; i < files.length; i++)
	{
		xmlString = "<pages>";
	
	
		fileName = files[i];
		console.log(fileName);
		readTextFile(fileName);
		
		xmlString += "</pages>";
	
		// save xml string to links<i>.xml
		var data = new FormData();
		data.append("data" , xmlString);
		var xhr = (window.XMLHttpRequest) ? new XMLHttpRequest() : new activeXObject("Microsoft.XMLHTTP");
		xhr.open( 'post', 'saveToFile.php?q='+i, true );
		xhr.send(data);
	
	}
});
	</script>
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        		  <?php
		include('menu.php');
	?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-xs-12">
                    <h3 class="page-header">Search Results:</h3>
					<hr>
					
					<div id="search-results">
					</div>
				
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->

			

	<!-- /#page-wrapper -->
        </div>
        
    </div>
    <!-- /#wrapper -->

				  
				  
				  <?php
		include('footer.php');
	?>


</body>

</html>
