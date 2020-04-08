<div class="container">
<?php if(isset($_GET['kuldes_sikeres'])):?>
<div style="background:#9AFB84;color:green;padding: 10px 20px;">Levél küldése sikeres!</div>
<?php endif;?>
<form action="" method="post" name="exportupload" enctype="multipart/form-data">
	<br /><h2>Számlaexport feltöltése:</h2><br />
	<input type="file" name="szamlaexport" id="szamlaexport"><br /><br />
	<input type="submit" value="Feltöltés indítása" name="submit" style="width: 215px;">
</form>

<table class="table"> 
	<?php if(!empty($lista)) foreach($lista as $sor):?>
	<tr>
		<td><?= $sor->file; ?></td>
		<td><?= $sor->ido; ?></td>
		<td ><a href="?kuldes=<?= $sor->id; ?>">Küldés Katinak</a></td>
		<td><a href="?letolt=<?= $sor->id; ?>">Letöltés</a></td>
	</tr>
	<?php endforeach; ?>
</table>	
		
</div>
