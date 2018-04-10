<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

	<!-- FONTS -->
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700" >	
	
    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="jumbotron.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link href="/css/default.css" rel="stylesheet">
    <link href="/css/footer.css" rel="stylesheet">
    <link href="/css/faq.css" rel="stylesheet">
	
</head>
<body>

<!--------------------------------------------------------------------------------->			
<!-- Nav Bar ---------------------------------------------------------------------->			
<!--------------------------------------------------------------------------------->			

<?php echo $this->element('main-menu', ['frontPage' => true]); ?>
           
<!---------------------------------------------------------------------------------!>			
<!-- Page Content -----------------------------------------------------------------!>			
<!---------------------------------------------------------------------------------!>			
	
<div class="clearfix headerHeightOffset"></div>
	
<div class="" style="position: relative; min-height: 500px;">
    
	<?= $this->Flash->render() ?>
	
	<?= $this->fetch('content') ?>
</div>
		
<!--------------------------------------------------------------------------------->			
<!-- Footer ---------------------------------------------------------------------->			
<!--------------------------------------------------------------------------------->			

<?php echo $this->element('footer', ['frontPage' => true]); ?>

<!--------------------------------------------------------------------------------->			
<!-- Scroll Up thumbnail ---------------------------------------------------------->			
<!--------------------------------------------------------------------------------->			

<!--
<div id="scrollUp">
	<img src="css/images/arrow.png" width=11 height=11 align=absmiddle />
	<a href="/" class="">TOP</a>
</div>
-->

<!--------------------------------------------------------------------------------->			
<!-- JavaScript / jQuery / Bootstrap ---------------------------------------------->			
<!--------------------------------------------------------------------------------->			
	
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/js/ie10-viewport-bug-workaround.js"></script>
	
	<script>
		
		var slider = 1;
		var slides = 3;
		var slideTime = 10000;
		var fadeTime = 800;
		var timer = null;
		
		$(document).ready(function(){		
			//setMenuHeight();
			clearInterval(timer);
			timer = setInterval(fadeOut, slideTime);
		});

		$(window).resize(function(){
			//setMenuHeight();
		});
				
		$("#slider").click(function(){
		
			clearInterval(timer);
			
			slider++;
			if (slider > slides)
				slider = 1;
				
			$("#slider").css("background-image", "url('/img/theme1/slider" + slider + ".jpg')");			
			$("#slider").show();
		});
				
		function fadeOut()
		{
			slider++;
			if (slider > slides)
				slider = 1;
			
			$("#slider").fadeOut();	
			
			clearInterval(timer);
			timer = setInterval(fadeIn, fadeTime);
		}
		
		function fadeIn()
		{
			var f = "/img/theme1/slider" + slider + ".jpg";
			var image = new Image();
			
			image.onload = function () {
				$("#slider").css("background-image", "url(" + f + ")");
				
				$("#slider").fadeIn("slow");
				
				clearInterval(timer);
				timer = setInterval(fadeOut, slideTime);
			}
			
			image.onerror = function () {
				alert("error loading: " + f);
			}
			
			image.src = f;			
		}
		
		// if navbar top is fixed, need to adjust page content when header logo contracts for xs devices
		function setMenuHeight() {
		
			// first check if navbar top is 'fixed'
			if ($(".navbar-fixed-top").css('position') == 'fixed')
			{
				// first check if header needs an adjustment
				var h = $("#navMain").height();
				var hMax = parseInt($(".headerHeightOffset").css('max-height'));

				// change the page top to match header height
				if (h != hMax)
				{
					$(".headerHeightOffset").css('height', h);
				}
			}
		}		
	
	</script>
	
  </body>
</html>
