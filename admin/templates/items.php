<?php if( !defined( "SITE_PATH" ) ) die( "Can't touch this." ); ?>
<div id="portfolio-items">

<ul id="project-list">
	<li class="project" id="add-project">
		<form action="<?php echo ADMIN_URL ?>project/add" method="post" accept-charset="utf-8">
			<input type="text" name="project_title" value="">
			<button type="submit">Add new project &rsaquo;</button>
		</form>
	</li>
<?php

foreach( $projects as $pid => $project ) {
if( $pid > 0 || ( $pid == 0 && !empty( $items[$pid] ) ) ) {

?>
	<li class="project" id="project-<?php echo $pid; ?>">
		<h2><?php echo $project["title"]; ?><?php if( $pid > 0 ) { ?> <a href="<?php echo ADMIN_URL . "project/$pid/delete" ?>">delete</a>/<a href="<?php echo ADMIN_URL . "project/$pid/edit" ?>">edit</a>/<a href="#" class="reorder">reorder</a><?php } ?></h2>
		<ul class="item-list">
<?php

if( !empty( $items[$pid] ) ) {
foreach( $items[$pid] as $item ){

?>
			<li class="item" id="item<?php echo $item["id"]; ?>">
				<div class="item-thumbnail">
					<div class="thumbnail-inner" style="background-image:url('<?php echo IMAGE_URL."image".$item["id"]."_100.jpg"; ?>')"></div>
					<ul class="item-options">
						<li class="edit"><a href="<?php echo ADMIN_URL ."item/". $item["id"] ."/edit" ?>">Edit</a></li>
						<li class="delete"><a href="<?php echo ADMIN_URL ."item/". $item["id"] ."/delete"; ?>">Delete</a></li>
					</ul>
				</div>
				<h3><?php echo $item["title"]; ?></h3>
				<p><?php echo $item["desc"]; ?></p>
			</li>
<?php } } ?>
		</ul>
<?php if( $pid > 0 ) { ?>
		<div class="item-add">
			<h4>Add a new item to this project</h4>
			<form enctype="multipart/form-data" action="<?php echo ADMIN_URL; ?>item/add" method="POST"> 
				<input type="hidden" name="item_project" value="<?php echo $pid; ?>">
				<input name="image_original" type="file" /> <button type="submit">Upload</button> 
			</form>
		</div>
<?php } ?>
	</li>
<?php } } // foreach( $p ) ?>
</ul>

</div>