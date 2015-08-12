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
	var xmlString = "<pages>";
	
    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", file, false);
    rawFile.onreadystatechange = function ()
    {
        if(rawFile.readyState === 4)
        {
            if(rawFile.status === 200 || rawFile.status == 0)
            {
                var allText = rawFile.responseText;
				//document.getElementById("search-results").innerHTML = allText;
               // console.log(allText);
				
				
				var el = document.createElement( 'test' );
				el.innerHTML = allText;

				//var content = el.getElementById( 'page-wrapper' );
				//console.log(el.children[17]);
				//console.log(el.firstElementChild);
				var pageWrapper = el.children[17].children[1];
				console.log(pageWrapper);
				
				//working! find text in all stand-alone paragraphs
				var allPtags = pageWrapper.getElementsByTagName( 'p' )
				console.log(allPtags.length);
				console.log(typeof allPtags[7]);
				console.log("");
				for (var i = 0; i < allPtags.length; i++)
				// for (var i = 5; i < 6; i++)
				{
					// console.log(allPtags[i].innerHTML);
					var parentElementClass = allPtags[i].parentElement.className;
					if (parentElementClass.indexOf('popover-icon') != -1) //if the parent is in the popover-icon class
					{
						console.log("is class");
						var id = allPtags[i].parentElement.id;
						console.log("id: " + id);
						var linkTitle = id.replace(/_/g, " "); //replace all underscores with spaces
						console.log(linkTitle);
						
						resultString = GenerateXmlEntry(linkTitle, linkTitle, linkTitle, id);
						xmlString += resultString + "\n";
						// console.log(resultString);
					}
					// console.log(allPtags[i]);
				}
				
				
				//parse the data in the data-content section of a .popover-icon div
				var allPopPverIcons = pageWrapper.getElementsByClassName('popover-icon');
				
				// for (var i = 6; i < 7; i++)
				for (var i = 0; i < allPopPverIcons.length; i++)
				{
					var id = allPopPverIcons[i].attributes['id'].value;
					console.log("id: " + id);
					var linkTitle = id.replace(/_/g, " "); //replace all underscores with spaces
					console.log(linkTitle);
					
					var dataContent = allPopPverIcons[i].attributes['data-content'];
					console.log(dataContent);
					dataContent = dataContent.value;
					if (dataContent.indexOf("Description:") != -1) //if it contains "Description:"
					{
						
						console.log(dataContent);
						dataContent = dataContent.substr(dataContent.indexOf('</strong>')+9);
						var desc = dataContent.substr(0, dataContent.indexOf('</p>'));
						desc = desc.replace(/[\n\r\t]/g, ''); //remove all newLine and tab characters
						desc = desc.replace(/<(?:.|\n)*?>/gm, ''); //remove all html tags
						console.log(desc);
						
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
				
				xmlString += "</pages>";
				// console.log(xmlString);
				
				// save xml string to links.xml
				var data = new FormData();
				data.append("data" , xmlString);
				var xhr = (window.XMLHttpRequest) ? new XMLHttpRequest() : new activeXObject("Microsoft.XMLHTTP");
				xhr.open( 'post', 'saveToFile.php', true );
				xhr.send(data);
				console.log("sent");

            }
        }
    }
    rawFile.send(null);
	//return allText;
}


//return false if the word is one of the words that we are ignoring
var ignoreTheseWords = ['a', 'the', 'of', 'is', 'an', 'and', '', 'in', 'or', 'to', '...', '-', '--'];
function CheckIfWordIsValid(word)
{
	for (var i = 0; i < ignoreTheseWords.length; i++)
	{
		if (word == ignoreTheseWords[i])
			return false;
	}
	return true;
}

// <pages>
// <link>
// <title>What is Android?</title>
// <url>android-os.php#what_is_android</url>
// </link>
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

var fileName = "apps-personal.php";
$( document ).ready(function() {

	
	var text = readTextFile(fileName);
	

	
	//document.getElementById("search-results").innerHTML = text;
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
