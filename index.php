<?php

include("load.php");

if( !file_exists( STORAGE_PATH . "cache.php" ) ){
	die( "No object cache exists. This must be generated on the <a href='/admin/'>admin page</a>." );
} else {
	include( STORAGE_PATH."cache.php" );
}

if(!empty($cache)) extract($cache);

?>
<!DOCTYPE HTML>
<head>
	<title>Cassia Leidigh &rsaquo; Portrait Artist in Greenville, SC</title>
	<link rel="stylesheet" href="/includes/style.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<script src="/includes/jquery-1.4.1.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="/includes/site.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<ul id="project-list">

<?php

foreach( $projects as $pid => $p ) {
if( $pid > 0 || ( $pid == 0 && !empty( $items_by_project[$pid] ) ) ) {

?>
	<li class="project" id="project-<?php echo $pid; ?>">
		<h2><?php echo $p["title"]; ?></h2>
		<ul class="item-list">
<?php

if( !empty( $items_by_project[$pid] ) ) {
foreach( $items_by_project[$pid] as $item ){

?>
			<li class="item">
				<h3 class="item-title"><?php echo $item["title"]; ?></h3>
				<p class="item-desc"><?php echo $item["desc"]; ?></p>
				<div class="item-thumbnail"><a href="<?php echo IMAGE_URL."image".$item["id"]."_700.jpg"; ?>"><img src="<?php echo IMAGE_URL."image".$item["id"]."_100.jpg"; ?>"></a></div>
			</li>
<?php } } ?>
		</ul>
	</li>
<?php } } ?>

</ul>
</body>
</html>