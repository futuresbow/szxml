

<?php if(!isset($hiba)) $hiba = '';if(!isset($uzenet)) $uzenet = ''; if($hiba!=''):?>
<div class="alert alert-error"><?= $hiba; ?></div>
<?php endif;?>
<?php if($uzenet!=''):?>
<div class="alert alert-ok"><?= $uzenet; ?></div>
<?php endif;?>


<style>
.dropdown {
    position: relative;
    display: inline-block;
}
.dropdown a {
	text-decoration:none;
	padding: 7px 4px;
	display:inline-block;
}

.dropdown-content a {
	display: block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #000;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    padding: 12px 8px;
    z-index: 1;

}

.dropdown:hover .dropdown-content {
    display: block;
}
</style>
