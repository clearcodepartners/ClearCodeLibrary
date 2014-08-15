	<script>
	var oldieCheck = Boolean(document.getElementsByTagName('html')[0].className.match(/\soldie\s/g));
	if(!oldieCheck) document.write('<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"><\/script>');
	else document.write('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"><\/script>');
	if(!window.jQuery) {
		if(!oldieCheck) document.write('<script src="js/libs/jquery-2.0.2.min.js"><\/script>');
		else document.write('<script src="js/libs/jquery-1.10.1.min.js"><\/script>');
	}
	</script>
	<script gumby-touch="js/libs" src="js/libs/gumby.js"></script>
	<script src="js/libs/ui/gumby.retina.js"></script>
	<script src="js/libs/ui/gumby.fixed.js"></script>
	<script src="js/libs/ui/gumby.skiplink.js"></script>
	<script src="js/libs/ui/gumby.toggleswitch.js"></script>
	<script src="js/libs/ui/gumby.checkbox.js"></script>
	<script src="js/libs/ui/gumby.radiobtn.js"></script>
	<script src="js/libs/ui/gumby.tabs.js"></script>
	<script src="js/libs/ui/gumby.navbar.js"></script>
	<script src="js/libs/ui/jquery.validation.js"></script>
	<script src="js/libs/gumby.init.js"></script>
	<script src="js/plugins.js"></script>
	<script src="js/main.js"></script>
    <script src="js/autosave.fields.jquery.js"></script>
    <!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
    <![endif]-->
	
	<script>
		var $loginform = $('form.login');
		
		$loginform.find('.primary.btn > a').click(function(e){
			e.preventDefault();
			$loginform.submit();
		});
		
		$loginform.validation({
			required: [{name: 'u'},{name: 'p'}],
			fail: function() { Gumby.error('Please check the login form'); },
		});
		
	</script>
	
  </body>
</html>