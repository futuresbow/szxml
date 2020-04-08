<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu" lang="hu">
	<head>
		  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		  <meta name="robots" content="noindex">
		  <title>hotelkupon.hu admin</title>
		  <link rel="stylesheet" href="<?= base_url() ; ?>/css/admin/960.css" type="text/css" media="screen" charset="utf-8" />
		  <link rel="stylesheet" href="<?= base_url() ; ?>/css/admin/template.css" type="text/css" media="screen" charset="utf-8" />
		  <link rel="stylesheet" href="<?= base_url() ; ?>/css/admin/colour.css" type="text/css" media="screen" charset="utf-8" />

	<body>

		<h1 id="head">hotelkupon.hu admin</h1>
		
		<div id="content" class="container_16 clearfix">
		    <div class="grid_5">
			<p>
			</p>
		    </div>
		    <div class="grid_6">
			<div class="box">
			    <h2>Login</h2>
			    <form action="<?php echo base_url(); ?>admin/login" method="POST">
				<p>
				    <br /><label for="felhasznalonev">felhasználónév:</label>
				    <input type="text" name="felhasznalonev" /><br />
				</p>
				<p>
				    <label for="jelszo">jelszó:</label>
				    <input type="password" name="jelszo" /><br />
				</p>
				<p>
				    <input type="submit" value="bejelentkezés" />
				</p>
			    </form>
			</div>
		    </div>
		</div>
	</body>
</html>
