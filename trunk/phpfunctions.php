<?php

function CurlGetUserInfo($screenname)
{
    $ch  = curl_init();
                                                
    $url = "http://api.twitter.com/1/users/show.json?screen_name=".$screenname;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER,0);

    $userinfo_json = curl_exec($ch);
    curl_close ($ch);
    
    return $userinfo_json;
    
}

function CurlGetTweets($screenname,$pagevalue)
{
    $ch  = curl_init();
                                                
    $url = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=".$screenname."&include_rts=1&page=".$pagevalue;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER,0);

    $tweets_json = curl_exec ($ch);
    curl_close ($ch);
    
    return $tweets_json;
}

function PrintUserInfo($userinfo)
{
    echo "<table cellpadding=5><tr><td>";
    echo "<img src=".$userinfo->profile_image_url.">";
    echo "</td><td>";
    echo "<h2> ".$userinfo->name."</h2>";
    echo "<h3> @".$userinfo->screen_name."</h3>";
    echo $userinfo->description."<br>";
    echo "<a href=".$userinfo->url.">".$userinfo->url."</a>";
    echo "</td></tr></table>";
}

function PrintTweets($tweets)
{
            echo "<table>";
            
            echo "<tr>";
            $c = count($tweets);
            
            for($i=0; $i<$c; $i++)
            {
                echo "<td valign=\"top\">";
                
                if(array_key_exists("retweeted_status",$tweets[$i])) { $text = $tweets[$i]->retweeted_status->text; }
                else { $text = $tweets[$i]->text; }
                                        
                $ifLink = CheckIfLink($text);
                
                if($ifLink[0])
                {
                    $ifImage = CheckIfImage($ifLink[1]);
                    if ($ifImage[0]) 
                    {   
                        echo "<p align=\"center\"><img src=\"".$ifImage[1]."\"></p>";
                    }
                    else
                    {
                        echo "<p align=\"center\"><img src=\"images/link.png\"></p>";
                    }
        
                }
                else
                {
                    echo "<p align=\"center\"><img src=\"images/tweet.png\"></p>";
                }
                
                echo "<table class=\"tweets\"><tr><td valign=\"top\">";
                if(array_key_exists("retweeted_status",$tweets[$i]))
                { echo "<img src=\"".$tweets[$i]->retweeted_status->user->profile_image_url."\" class=\"user\"></td><td>"; }
                else { echo "<img src=\"".$tweets[$i]->user->profile_image_url."\" class=\"user\"></td><td>"; }
                echo "<p>".$tweets[$i]->text."</p></td></tr></table>";
            
                
                
                echo "</td>";
                if((($i+1)%3)==0) echo "</tr><tr>";
            }
            
            echo "</tr>";
            echo "</table>";
            
}

function NextButton($screenname,$pagevalue)
{
    echo "<form id=\"top_search\" action=\"publicusertimeline.php\" method=\"get\" >";
    echo "<div class=\"centered\">";
    
    $nextpage = $pagevalue + 1;
    
    echo "<input type=\"hidden\" name=\"pagevalue\" value=".$nextpage.">";
    echo "<input type=\"hidden\" name=\"twitter_sname\" value=\"".$screenname."\">";
    echo "<input class=\"searchbutton\" type=\"submit\" value=\"Next\" />";
    echo "</div>";
    echo "</form>";

}

function PrevButton($screenname,$pagevalue)
{
    echo "<form id=\"top_search\" action=\"publicusertimeline.php\" method=\"get\" >";
    echo "<div class=\"centered\">";
    
    $prevpage = $pagevalue - 1;
   
    
    echo "<input type=\"hidden\" name=\"pagevalue\" value=".$prevpage.">";
    echo "<input type=\"hidden\" name=\"twitter_sname\" value=\"".$screenname."\">";
    echo "<input class=\"searchbutton\" type=\"submit\" value=\"Previous\" />";
    echo "</div>";
    echo "</form>";
}

function CheckIfLink($text)
{
    $startpos = strpos($text,"http:");
        
    if($startpos) 
    {
        $endpos = strpos($text," ",$startpos);
        if ($endpos) { 
        $len = $endpos - $startpos; 
        }
        else { $len = strlen($text) - $startpos; }
        $link = substr($text,$startpos,$len);
        
        $ifLink = array(1,$link);
        return $ifLink;
    }
    
    else
    {
        $ifLink = array(0,"none");
        return $ifLink;
    }
    
}

function CheckIfImage($link)
{
    $images = array("twitpic","yfrog","plixi","flickr","instagr.am");
    
    $c = count($images);
    $check = 0;
    
    for($i=0; ($i<$c)&&($check==0); $i++) { if(strpos($link,$images[$i])) { $check = $i + 1; } }
    
    switch ($check)
    {
        case 1:
        $pos = strlen($link) - strpos($link,"twitpic.com");
        $url = substr_replace($link,"/show/thumb",$pos,0);
        break;
        
        case 2:
        $url = $link.":small";
        break;
        
        default:
        $url = "images/image.png";
    }
    
    $IfImage = array($check, $url);
    return $IfImage;

}



function printform($i,$screenname)
{
    echo "<form id=\"top_search\" action=\"category.php\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"pagevalue\" value=1>";
    echo "<input type=\"hidden\" name=\"twitter_sname\" value=\"".$screenname."\">"; 
    
    if($i==1) {echo "<input class = \"selectedbtn\" type=\"submit\" name=\"Photos\" value=\"Photos\">    "; }
    else { echo "<input class = \"categorybutton\" type=\"submit\" name=\"Photos\" value=\"Photos\">    "; }
    
    if($i==2) { echo "<input class =\"selectedbtn\" type=\"submit\" name=\"Videos\" value=\"Videos\">    "; }
    else { echo "<input class =\"categorybutton\" type=\"submit\" name=\"Videos\" value=\"Videos\">    "; }
    
    if($i==3) { echo "<input class = \"selectedbtn\" type=\"submit\" name=\"Links\" value=\"Links\">"; }
    else { echo "<input class = \"categorybutton\" type=\"submit\" name=\"Links\" value=\"Links\">"; }
    
    echo "</form>";

    echo "<br>";

}

function Photos($screenname)
{
    printform(1,$screenname);
    
    $phototweets = json_decode(CurlGetTweets($screenname,1));
    
    echo "<table>";
    echo "<tr>";
    
    
    for($i=0; $i<20; $i++)
    {
        if(array_key_exists("retweeted_status",$phototweets[$i])) { $text = $phototweets[$i]->retweeted_status->text; }
        else { $text = $phototweets[$i]->text; }
        
        $ifLink = CheckIfLink($text);
        
        if($ifLink[0])
        {
            $ifImage = CheckIfImage($ifLink[1]);
            if($ifImage[0]) { 
                $i = $i + 1;
                
                echo "<td valign=\"top\">";
                echo "<p align=\"center\"><img src=\"".$ifImage[1]."\"></p>";
                
                echo "<table class=\"tweets\"><tr><td valign=\"top\">";
                if(array_key_exists("retweeted_status",$phototweets[$i]))
                { echo "<img src=\"".$phototweets[$i]->retweeted_status->user->profile_image_url."\" class=\"user\"></td><td>"; }
                else { echo "<img src=\"".$phototweets[$i]->user->profile_image_url."\" class=\"user\"></td><td>"; }
                echo "<p>".$phototweets[$i]->text."</p></td></tr></table>";
            
                echo "</td>";
                if((($i+1)%3)==0) echo "</tr><tr>";
            }
        
        }
        
        echo "</tr>";
        echo "</table>";
    }
}

function Videos($screenname)
{
    printform(2,$screenname);
}

function Links($screenname)
{
    printform(3,$screenname);
}



?>
