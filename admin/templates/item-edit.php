<?php

if( !defined( "SITE_PATH" ) ) die( "Can't touch this." );

?>
<div id="portfolio-item">
	<h3>Editing <em><?php echo $item["title"]; ?></em></h3>

	<form action="<?php echo ADMIN_URL . "item/$id/save"; ?>" method="post" accept-charset="utf-8">
		<img src="<?php echo IMAGE_URL ."image".$id; ?>_500.jpg" alt="<?php echo $item["title_src"]; ?>" id="thumbnailify">
		<ul>
			<li><label for="item_title">Title</label><input type="text" name="item_title" value="<?php echo $item["title_src"]; ?>" id="item_title"></li>
			<li><label for="item_desc">Description</label><textarea name="item_desc" id="item_desc" rows="8" cols="40"><?php echo $item["desc_src"]; ?></textarea></li>
			<li><label for="item_project">Project</label>
				<select name="item_project" id="item_project">
					<?php foreach( $projects as $pid => $p ) { ?>
					<option <?php echo $item["project"] == $pid ? " selected='selected'" : ""; ?> value="<?php echo $pid; ?>"><?php echo $p["title"] ?></option>
					<?php } ?>
				</select>
			</li>
		</ul>
		<p><button type="submit">Save Changes</button> or <a href="<?php echo ADMIN_URL . "items"; ?>">Cancel and go back</a>.</p>
	</form>
</div>