<?php if(isset($_SESSION['login']['lock'])) {?>
	<script>
		lockdown();
	</script>
<?php } else {?>
	<script>
		var myObj = {
		  callMeMaybe: function () {
		    var myRef = this;
		    var val = setTimeout(function () {
					getthedate();
		      myRef.callMeMaybe();
		    }, 1000);
		   }
		};
		myObj.callMeMaybe();
		checkNotify();
	</script>
<?php }?>
	</body>
</html>
