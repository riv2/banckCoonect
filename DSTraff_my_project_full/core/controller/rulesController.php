<? 
class rulesController {
	
public function indexAction () {
ob_start();	?>
<div id = "rulesMainConteiner">
<style> 
.rulesLayer {
	position:fixed;
	width:100%;
	height:100%;
	z-index:3000;
	background-color:rgba(0,0,0,0.3);
	top:0px; left:0px;
}

.rulesConteiner {
	position:fixed;
	width:70%;
	height:88%;
	z-index:3100;
	background-color:rgba(255,255,255,1);
	top:5%; left:15%;
	
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	-webkit-box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.75);
	-moz-box-shadow:    0px 0px 5px 0px rgba(50, 50, 50, 0.75);
	box-shadow:         0px 0px 5px 0px rgba(50, 50, 50, 0.75);
	padding:15px;
	overflow: auto;
	font-size:13px;
}

.rulesConteiner a {
	text-decoration:underline;
	font-size:16px;
	color: rgba(0,0,255,0.7);
	cursor:pointer;
}

.rulesClose {
position:absolute;
top:10px;
right:10px;
width:20px;
height:20px;
background-image: url("../../../public/img/close_2.png");
z-index:3200;
cursor:pointer;
}
</style>
<script>
<?php if (AuthID or AuthLogin) { ?>
$('#rulesClose').click(function () {
$('#rulesMainConteiner').remove();	
});
<?php } ?>

<?php if (!AuthID or !AuthLogin) { ?>
$('#rulesApply').click(function () {
$('#rulesMainConteiner').remove();
$('#rule').val('apply');
});
<?php } ?>

<?php if (!AuthID or !AuthLogin) { ?>
$('#rulesDiscard').click(function () {
$('#rulesMainConteiner').remove();
});
<?php } ?>

</script>
<div class = "rulesLayer">
<div class = "rulesConteiner">
<?php if (AuthID or AuthLogin) { ?> <div class = "rulesClose" id = "rulesClose"></div> <?php } ?>

<?php echo LANG['rules']['rule']; ?>

<?php if (!AuthID or !AuthLogin) { ?>
<br>
<center> <a id = "rulesApply"><?php echo LANG['rules']['aggree']; ?> </a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a id = "rulesDiscard"><?php echo LANG['rules']['notaggree']; ?></a></center>
<?php } ?>
</div>
</div>

</div>	
<?php		
$rules = ob_get_contents();
ob_end_clean();  	
echo $rules;
} }
?>