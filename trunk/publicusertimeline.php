<html>
    <head>
        <title>::TwitterCat:: - [Catch phrase here]</title>
        <link href="design_put.css" type="text/css" rel="stylesheet" />
    </head>
    
    <body>
        <div id="meow">
		</div>

		<div id="main_box">
			<div id="top_bar">
				<img class="left" src="images/logo.png" />
			</div>
        
        <form id="top_search" action="publicusertimeline.php" method="get" >
                <div class="centered">
									<input class="searchbox" type="text" value="Twitter Username" name="twitter_sname" />
									<input type="hidden" name="pagevalue" value=1>
                                    <input class="searchbutton" type="submit" value="Categorize Feeds" />
                </div>
        
        </form>
        
        
        <div id="main_content">
        
            <?php
            
            include("phpfunctions.php");
            
            $screenname = $_GET[twitter_sname];
            
            $pagevalue = $_GET[pagevalue];
              
            $userinfo = json_decode(CurlGetUserInfo($screenname));
            if(array_key_exists("error",$userinfo))
            {
                echo "<p align=\"center\">@".$screenname.": User not found. Try again.<br>";
                echo "<br><br><br><br><br><br><br><br><br>";
            }
            else
            {
                PrintUserInfo($userinfo);
            
            //echo "<pre>";
            //print_r($userinfo);
            //echo "</pre>";

            
                if ($userinfo->protected == 1)
                {
                echo "<br>";
                echo "<p align=\"center\">@".$screenname."'s Tweets are protected.<br>";
                echo "Only confirmed followers have access to @".$screenname."'s Tweets and complete profile.<br>";
                echo "<img src=\"images/darker.png\" align=\"center\"><br></p>";
                }

                else
                {
                
                echo "<form id=\"top_search\" action=\"category.php\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"pagevalue\" value=1>";
                echo "<input type=\"hidden\" name=\"twitter_sname\" value=\"".$screenname."\">"; 
                echo "<input class = \"categorybutton\" type=\"submit\" name=\"Photos\" value=\"Photos\">    ";
                echo "<input class =\"categorybutton\" type=\"submit\" name=\"Videos\" value=\"Videos\">    ";
                echo "<input class = \"categorybutton\" type=\"submit\" name=\"Links\" value=\"Links\">";
                echo "</form>";

                echo "<br>";
                
                $tweets = json_decode(CurlGetTweets($screenname,$pagevalue));
                PrintTweets($tweets);
                //echo "<pre>";
                //print_r($tweets);
                //echo "</pre>";
                
                if(count($tweets)) 
                { 
                    if($pagevalue>1) { PrevButton($screenname,$pagevalue); }
                    NextButton($screenname,$pagevalue); 
                }
                else { echo "No more tweets to load.<br><br><br><br><br><br><br><br>"; }
                }
            }
            
                                                                                                        
            ?>
        
        </div>
        
        <div id="footer">
				<table width="100%">
					<tr>
						<td>
							<h3>About</h3>
						</td>
						<td>
							<h3>Documentation</h3>
						</td>
						<td>
							<h3>Contact</h3>
						</td>
					</tr>
					<tr>
						<td colspan="3" align="center">
							<font color="#704b22">Copyright &copy 2011 Group 6</font>
						</td>
					</tr>
				</table>
        </div>
        
    </body>
</html>

