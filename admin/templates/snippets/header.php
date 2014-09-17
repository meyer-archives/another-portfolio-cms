<!DOCTYPE HTML>
<html>
<head>
	<title>Portfolio Admin</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>admin/style.css" media="all">
	<script type="text/javascript" src="<?php echo INCLUDES_URL ?>jquery-1.4.1.min.js"></script>
	<script type="text/javascript" src="<?php echo INCLUDES_URL ?>jquery-ui-1.7.2.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo INCLUDES_URL ?>jquery.timeago.js"></script>
	<script type="text/javascript" src="<?php echo INCLUDES_URL ?>admin.js"></script>
<?php /*	<script type="text/javascript" src="<?php echo INCLUDES_URL ?>jquery.Jcrop.min.js"></script> */ ?>
</head>
<body>
	<div id="container">
<?php if( logged_in() ){ ?>
		<div id="logout-link"><a href='<?php echo ADMIN_URL; ?>logout'>Logout</a></div>
		<p>Last updated: <span id="last-updated" title="<?php echo date("Y-m-d H:i:s",$cache_age) ?>"><?php echo date("l \\t\h\e jS \a\\t g:i:s a",$cache_age) ?></span></p>
		<form action="<?php echo ADMIN_URL ?>publish" method="post" accept-charset="utf-8">
			<button type="submit">Publish</button>
		</form>
		<?php /* echo $cache_status */ ?>
<?php } ?>