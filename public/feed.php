<?php
$url='http://www.livedolls.tv/f/stm.xml';
$xml = simplexml_load_file($url);

if($xml ===  FALSE)
{
 echo '<div style="display:block;width:640px;margin:0 auto"><iframe src="http://www.camstel.com/exports/tour_20/index.php?cat=2&cols=4&rows=12&AFNO=1-20&clr_fg=474747&clr_ln=157D96&df=7170" width="640" height="2550" frameborder="0" scrolling="no"></iframe></div>';
} 
// check if mobile
$animateHeadline = "animate";
$pagedBttns = "4";
$mobilePerPage = 100;

$useragent=$_SERVER['HTTP_USER_AGENT'];
			if(preg_match('/(Android|webOS|iPhone|iPad|iPod|BlackBerry|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
			$mobilePerPage = 50;
			$pagedBttns = 2;
			$animateHeadline = " ";
			}
	// Setting up variables
$totalPerfs = count($xml->AvailablePerformers->Performer);
$perPage = $mobilePerPage;
$page = isset($_GET['page']) && ($page = intval($_GET['page'])) > 0 ? $page : 1;
$start = ($page - 1) * $perPage;
$end = $start + $perPage;
$cur = ceil($start / $perPage)+1;
$pagedPerfs = array();
for ($a=$start; $a<$end; ++$a) {
if (isset($xml->AvailablePerformers->Performer[$a])){
$pagedPerfs[] = $xml->AvailablePerformers->Performer[$a];
}
}
echo "<div id='feed-content'>
      <ul class='md__grid'>";
foreach ($pagedPerfs as $perf) {
	
$rating = $perf->attributes()->Rating;
$audio = $perf->attributes()->Audio;
$phone = $perf->attributes()->Phone;
$pregold = $perf->attributes()->PreGoldShow;
$gold = $perf->attributes()->GoldShow;
$party = $perf->attributes()->PartyChat;
$status = $perf->attributes()->StreamType;
$pid = $perf->attributes()->StreamType;

if ( $rating > 474 && $rating < 501 ) {
	$rating = "rating5";
}
if ( $rating > 424 && $rating < 475 ) {
	$rating = "rating4_half";
}
if ( $rating > 374 && $rating < 425 ) {
	$rating = "rating4";
}
if ( $rating > 324 && $rating < 375 ) {
	$rating = "rating3_half";
}
if ( $rating > 274 && $rating < 325 ) {
	$rating = "rating3";
}
if ( $rating > 224 && $rating < 275 ) {
	$rating = "rating2_half";
}
if ( $rating > 174 && $rating < 225 ) {
	$rating = "rating2";
}
if ( $rating > 124 && $rating < 175 ) {
	$rating = "rating1_half";
}
if ( $rating > 74 && $rating < 125 ) {
	$rating = "rating1";
}
if ( $rating > 24 && $rating < 75 ) {
	$rating = "rating0_half";
}
if ( $rating > 0 && $rating < 25 ) {
	$rating = "rating0";
}
if ( $audio == "true" ) {
	$audio = "audio";
}
else { ($audio = "no");
}
if ( $phone == "true" ) {
	$phone = "phone";
}
else { $phone = "no";
}
if ($pregold == "1" && $party == "0") {
	$st = "gold-show";
	}
elseif ($pregold == "1" && $party == "1") {
	$st = "gold-show";
	}
if ($party == "1" && $gold == "0" && $pregold == "0") { 
$st = "party-chat";
}
if ($gold == "1") { 
$st = "gold-show-in-progress";
}
/*elseif ($pregold == "1" && $gold == "1" && $party == "0") {
	$st = "gold-show-in-progress";
	}*/
if ($pregold == "0" && $party == "0" && $gold == "0" && $status == "live") {
	$st = "online";
	}
if ($status == "offline") { 
$st = "offline";
}

$status_text = $st;
$status_text = preg_replace('/-/', ' ', $st);

echo "<li class='tn ".$animateHeadline." status'>
<div class='md__wrapper animated flipInX'>
<div class='md__overlay'>";
echo "<a href='/cam-girl/" .$perf->attributes()->Name ."' >
        <img src='http://m2.nsimg.net" .$perf->Media->Pic->Full->attributes()->Src."' class='morph md__img' alt='" .$perf->attributes()->Name ."'>
      </a>";
echo "<a href='/cam-girl/" .$perf->attributes()->Name ."' class='md__bottom'>";
echo "<div class='md__rating'>
          <div class='rating-stars $rating'>
  <i class='fa icon--star'></i>
  <i class='fa icon--star'></i>
  <i class='fa icon--star'></i>
  <i class='fa icon--star'></i>
  <i class='fa icon--star'></i>
</div>";
echo "</div>";
echo "<div class='md__headline'>".$perf->attributes()->Headline ."</div>
      </a>";
echo "<a href='/cam-girl/" .$perf->attributes()->Name ."' class='md__name'>
        <div class='md__about'>
          ".$perf->attributes()->Country ."
          <i class='md__separator fa fa-ellipsis-v'></i>
          " .$perf->attributes()->Age ."
        </div>

        " .$perf->attributes()->Name ."
      </a>
	   </div>";
	   
echo "<a href='/cam-girl/" .$perf->attributes()->Name ."' class='md__status $st'>
  $status_text
</a>

  </div>
</li>";

}
echo "</ul>";
// Here we get the number of pages (results / #per page). ceil is used to round up
$pages = ceil($totalPerfs / $perPage);
$showeachside = $pagedBttns;
if(empty($start))$start=0;
$next = ($cur + 1);
$prev = ($cur - 1);
echo "<div style='clear:both;height:30px;width:100%'></div>";
echo "<div style='width:250px;margin:0px auto'><ul class='page-numbers' align='center'>";
// Simple for loops to display the proper # of page links
?>



<?php

if(($start-$perPage) >= 0)
{

echo "<li><a class='prev-next toFeedTop' onclick=\"$('div#feed-content').load('/feed.php?page=".$prev."')\" href='#' data-target='feed-content'><i class='fa fa-arrow-circle-left'></i></a></li>";

}
 
?>
<?php
$eitherside = ($showeachside * $perPage);
//if($start+1 > $eitherside)print ("<li>...</li>");

for($y=0;$y<$totalPerfs;$y+=$perPage)
{
    $class=($y==$start)?"pageselected":"";
    if(($y > ($start - $eitherside)) && ($y < ($start + $eitherside)))
    {
$pg = ($y / $perPage + 1);
?>
<li><a class="<?php print($class);?> toFeedTop" href="/?page=<?php print($pg);?>" data-target="feed-content" onclick="$('div#feed-content').load('/feed.php?page=<?php print($pg);?>')"><?php print($pg);?></a></li>
<?php
    }
}
//if(($start+$eitherside)<$totalPerfs)int ("<li>...</li>");
?>
 
 <?php
if($start+$perPage<$totalPerfs)
{

echo "<li><a class='prev-next toFeedTop' onclick=\"$('div#feed-content').load('/feed.php?page=".$next."')\" href='#!' data-target='feed-content'><i class='fa fa-arrow-circle-right'></i></a></li";

}

echo "</ul></div>
      </div>";
?>