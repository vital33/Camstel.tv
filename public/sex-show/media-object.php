<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Live Sex Show Player @ Camstel.tv</title>
<meta name="description" content="Random live sex show is playing on this page of Camstel.tv 24/7, Enjoy!">
<meta name="robots" content="all">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" type="text/css" href="/media-object.css" title="default">
<style type="text/css">
.color, a.color, a.color:link, a.color:visited, .start a, .start a:link, .start a:visited, ul.cats li a:hover, a.login:hover {background:none; background-color:#FF6800;/*client_secondarycolor*/color:#000000;/*client_enterpaidcolor*/}
body {
	/* [disabled]background-color: #FFF!important; */
	background-image: none!important;
}
 
#middle {
	top: -10px!important;
	background-position: -20px!important;
	background: url("http://cdn.nsimg.net/cache/landing/common/20111023/content-bg.png")
}
</style> 
    <!-- CORE JQUERY  -->
    <script src="/assets/js/vendors.js"></script>

<script type="text/javascript" src="http://m1.nsimg.net/static/x/google/swfobject/2.2/swfobject.js"></script>
<script type="text/javascript" src="http://cdn.nsimg.net/cache/landing/common/20111023/layout.js"></script>

<script type="text/javascript" src="http://cdn.nsimg.net/cache/landing/common/20111023/jquery-textfill-0.1.js"></script>

<script type="text/javascript" src="http://cdn.nsimg.net/cache/landing/common/20111023/cookies.js"></script>

<script type="text/javascript">
$(function() {

    var $allVideos = $("iframe[src^='f'], iframe[src^='//www.youtube.com'], object, embed"),
    $fluidEl = $("figure");

	$allVideos.each(function() {

	  $(this)
	    // jQuery .data does not work on object/embed elements
	    .attr('data-aspectRatio', this.height / this.width)
	    .removeAttr('height')
	    .removeAttr('width');

	});

	$(window).resize(function() {

	  var newWidth = $fluidEl.width();
	  $allVideos.each(function() {

	    var $el = $(this);
	    $el
	        .width(newWidth)
	        .height(newWidth * $el.attr('data-aspectRatio'));

	  });

	}).resize();

});
var adInfo = {
  at:"d",
  al:""
};

$(function() {
  //Get the publisher
  var queryString = window.location.search;
  var query = {};
  var queryParam, queryParams;
  if(queryString)
  {
    //Remove the ?
    queryString = queryString.substr(1);
    queryParams = queryString.split('&');
    for(var i=0; i<queryParams.length; i++)
    {
      queryParam = queryParams[i].split('=');
      query[queryParam[0]] = queryParam[1];
    }
  }

  if(query.sanp)
  {
    adInfo['p'] = query.sanp;
  }

  if(getCookie("lp") == null)
  {
    $('#flashContent').show();
    $('#swfwrap').show();
    $('#swfinner').show();
    $('#flashReplace').hide();
		
		var flashvars = {};
		flashvars.promoUri = escape("http://www.camstel.com/signup/?AFNO=1-2011");
		flashvars.videosUri = escape("http://cdn.nsimg.net/videos/");	
		flashvars.listUri = escape("http://cdn.nsimg.net/cache/landing/playlist/20130121/master.xml");	
		flashvars.privatenow = "I'M IN A PRIVATE SESSION NOW";
		flashvars.clickhere = "CLICK HERE TO JOIN ME";
		flashvars.chathover = "CLICK HERE TO ENTER MY SHOW";
    										
		flashvars.category = "8";
		flashvars.arrows = 1;	
		flashvars.ismute = "1" ;
		flashvars.isself = "1" ;	

    $.extend(flashvars, adInfo);

		var params = {};
		params.allowscriptaccess = "always";
		params.wmode="transparent";
		var attributes = {};
		swfobject.embedSWF("http://cdn.nsimg.net/cache/landing/chat/20120820/player2.swf", "flashContent", "960", "480", "9.0.0", false, flashvars, params, attributes);
	 }
  else
  {
    $('#flashContent').hide();
    $('#swfinner').hide();
    $('#swfwrap').hide();
    $('#flashReplace').show(); 
  }


	var blank=Boolean("");
	if(blank){	
		$('a').attr("target", "_self");
	}else{
		$('a').attr("target", "_blank");
	}		
	
	$('.join, .login').textfill({ maxFontPixels: 50 });	

  try
  {
    var adInitInfo = $.extend(
    {
      'sc':0,
      'ready': function($)
        {
          var ch=SAN.ad.click;
          var sh=SAN.ad.submit;
          $(document).delegate('a:not([data-san-track~="no-click"])','click',ch);
          SAN.ad.convertForm($('form.csearch-form'));
          $(document).delegate('form.csearch-form', 'submit', sh);
        }
    }, adInfo);

    SAN.ad.externalInit(adInitInfo);
  }
  catch(e)
  {
    // :(
  }var silent = window.location + "";
var silent = window.location + "";
$(window).blur(function() {
	if(window.location.href.indexOf("HTML_mute") == -1) {
   
window.location = silent; }
});       
});

</script>
</head>
<body onFocus="focus();">
<a href="http://www.camstel.com/?AFNO=1-2011&jntp=np"  target="_parent" onClick="_gaq.push(['_trackEvent', 'Popunder', 'Header', 'logo']);"></a>
<div id="controls">
  <div id="frame">
<div class="border">
<div class="player">
<div id="swfwrap" style="width:640px; margin:auto; z-index:6;"><div id="swfinner" style="width:640px; z-index:7; display:none; margin:auto; position:relative;" ><!--<a style="display:block; position:absolute; width:640px; height:480px" title="CLICK HERE TO ENTER MY SHOW" onmousedown="parent.showhide('reglayer', 'block'); parent.showhide('regbox-outer', 'block');  return false;"></a>--></div></div>
<div id="flashContent"></div>
<div id="flashReplace" style="z-index:8; display:none; width:640px; height:480px; margin:auto">
<p class="callout"><a href="/signup.php" onClick="parent.showhide('reglayer', 'block'); parent.showhide('regbox-outer', 'block');  return false" target="_parent" ontClick="_gaq.push(['_trackEvent', 'Popunder', 'End Message', 'see more']);">Want to See More?</a></p>
<p class="start"><a href="/signup.php" onClick="parent.showhide('reglayer', 'block'); parent.showhide('regbox-outer', 'block');  return false"  target="_parent" ontClick="_gaq.push(['_trackEvent', 'Popunder', 'End Message', 'continue']);"><span class="txt">Continue</span> <span class="arrow"></span></a></p>

          </div>
</div>
</div></div>
<div align="right" id="full-screen-icon"><a href="/login.php?aff=1-2012" rel="nofollow" target="_parent" title="Log-in to enter full-screen mode."><img src="../i/full-screen.png"></a></div>


</div>
<div class="clear"></div><div id="middle"></div>
      <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-11664458-2']);
        _gaq.push(['_trackPageview']);

        (function() {
          var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
          ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
      </script>


<script id="sanet" type="text/javascript">
try
{
  if (typeof Naiad !== 'undefined' && Naiad.OnDomReadyQueue) {
    Naiad.OnDomReadyQueue([1, function () {
      SAN.Naiad.init({cookie_domain: '.camzter.com'});
    }]);
  } else {
    $(function () {
      SAN.Naiad.init({cookie_domain: '.camzter.com'});
    });
  }
}
catch(e)
{
  // :(
}
</script>


</body>
</html>