
<!-- FOOTER -->
<footer id="footer">
	<div class="container">

		<div class="row">
						
			<div class="col-md-3">

				<!---------------------------------------------------->
				<!-- Small Description -->
				<!---------------------------------------------------->
				<h4 class="letter-spacing-1">CONTACT INFORMATION</h4>

				<!---------------------------------------------------->
				<!-- Contact Address -->
				<!---------------------------------------------------->
				<address>
					<ul class="FooterListButton list-unstyled">
						<li class="address"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-map-marker"></span>EpicTravelGuide.com<br></li>
						<li class="phone"><button id='phoneButton' class="btn btn-success"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-earphone"></span>Show Phone Number</button></li>
						<li class="email"><button id='emailButton' class="btn btn-success"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-envelope"></span>Show Email Address</button></li>
					</ul>
				</address>
				<!-- /Contact Address -->

						</div>

						<div class="col-md-3">

							<!---------------------------------------------------->
							<!-- Latest kb's -->
							<!---------------------------------------------------->
							<h4 class="letter-spacing-1">FAQ</h4>
							<ul class="footer-posts list-unstyled">
								<?php if (isset($kbase) && $kbase != null) : ?>
								<?php $cnt = 1; foreach($kbase as $rec) : ?>
								<?php if ($rec['category']['nickname'] == 'faq'): ?>
								<?php if ($cnt++ > 3) break; ?>
									<li>
										<?php echo $this->html->link($rec['title'], '/kbase/faq/faq'); ?>
										<!-- small>29 June 2015</small -->
									</li>
								<?php endif; ?>
								<?php endforeach; ?>
								<?php endif; ?>
							</ul>

						</div>

						<div class="col-md-2">

							<!---------------------------------------------------->
							<!-- Links -->
							<!---------------------------------------------------->
							<h4 class="letter-spacing-1">SITE MAP</h4>
							<ul class="FooterList list-unstyled">
								<li><a href="/"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-home"></span>Home</a></li>
								<li><a href="/"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-question-sign"></span>FAQ</a></li>
								<li><a href="/"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-envelope"></span>Contact Us</a></li>
								<li><a href="/login"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-user"></span>Login</a></li>
							</ul>

						</div>

						<div class="col-md-4">

							<!---------------------------------------------------->
							<!-- Newsletter Form -->
							<!---------------------------------------------------->
							<h4 class="letter-spacing-1">HAVE QUESTIONS?</h4>
							<p>Enter your email address and we will contact you.</p>

							<form class="validate" action="/contacts/add/" method="post" data-success="Request sent, thank you!" data-toastr-position="bottom-right">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
									<input type="email" id="email" name="email" class="form-control required" placeholder="Enter your email address">
									<span class="input-group-btn">
										<button class="btn btn-success" type="submit">Send</button>
									</span>
								</div>
							</form>
							<!-- /Newsletter Form -->

							<!-- Social Icons >
							<div class="margin-top-20">
								<a href="#" class="social-icon social-icon-border social-facebook pull-left" data-toggle="tooltip" data-placement="top" title="Facebook">

									<i class="icon-facebook"></i>
									<i class="icon-facebook"></i>
								</a>

								<a href="#" class="social-icon social-icon-border social-twitter pull-left" data-toggle="tooltip" data-placement="top" title="Twitter">
									<i class="icon-twitter"></i>
									<i class="icon-twitter"></i>
								</a>

								<a href="#" class="social-icon social-icon-border social-gplus pull-left" data-toggle="tooltip" data-placement="top" title="Google plus">
									<i class="icon-gplus"></i>
									<i class="icon-gplus"></i>
								</a>

								<a href="#" class="social-icon social-icon-border social-linkedin pull-left" data-toggle="tooltip" data-placement="top" title="Linkedin">
									<i class="icon-linkedin"></i>
									<i class="icon-linkedin"></i>
								</a>

								<a href="#" class="social-icon social-icon-border social-rss pull-left" data-toggle="tooltip" data-placement="top" title="Rss">
									<i class="icon-rss"></i>
									<i class="icon-rss"></i>
								</a>
					
							</div>
							<!-- /Social Icons -->

						</div>

					</div>

				</div>

				<div class="copyright">
					<div class="container center">
						<!-- ul class="pull-right nomargin list-inline mobile-block">
							<li><a href="#">Terms &amp; Conditions</a></li>
							<li>&bull;</li>
							<li><a href="#">Privacy</a></li>
						</ul -->
						<div class="text-center">
							&copy; <?= date("Y"); ?> - All Rights Reserved - Todos Derechos Reservados - &copy; <?= date("Y"); ?>
						</div>
					</div>
	</div>
</footer>
<!-- /FOOTER -->
		
<script src="/js/jquery-2.1.4.min.js"></script>	
<script>

$(document).ready(function() {

$('#phoneButton').click(function() { 
	$('#phoneButton').remove();
	$('.phone').append('<span>+1 800 210 2618</span>');
});
$('#emailButton').click(function() { 
	$('#emailButton').remove();
	$('.email').append('<span>info&#64;epictravelguide&#46;com</span>');
});

});

</script>	