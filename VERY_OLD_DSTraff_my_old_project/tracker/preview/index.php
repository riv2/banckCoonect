<?php
$https = array('reklamnews.ru');


if (!isset($_REQUEST['tracker_hash'])) {
    die();
}

$REQUEST      = explode('/', $_REQUEST['tracker_hash']);
$tracker_hash = $REQUEST[0];

unset($_REQUEST);
unset($_GET);
unset($_POST);

if (strlen($tracker_hash) != 32) {
    die();
}

?>
<html>
<head>
</head>
<body>
<? if (in_array($_SERVER['SERVER_NAME'] ,$https)) { ?>
 <script async id = "<?php echo $tracker_hash; ?>" src = "https://<?php echo $_SERVER['SERVER_NAME']; ?>/<?php echo $tracker_hash; ?>"></script>
<? } else { ?>
 <script async id = "<?php echo $tracker_hash; ?>" src = "https://<?php echo $_SERVER['SERVER_NAME']; ?>/<?php echo $tracker_hash; ?>"></script>
<? } ?>
</body>
</html>