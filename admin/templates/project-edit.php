<?php

if( !defined( "SITE_PATH" ) ) die( "Can't touch this." );

?>
<div id="portfolio-project">
	<h3>Editing <em><?php echo $project["title"]; ?></em></h3>

	<form action="<?php echo ADMIN_URL . "project/$id/save"; ?>" method="post" accept-charset="utf-8">
		<ul>
			<li><label for="project_title">New Title</label><input type="text" name="project_title" value="<?php echo $project["title_src"]; ?>" id="project_title"></li>
		</ul>
		<p><button type="submit">Save Changes</button> or <a href="<?php echo ADMIN_URL . "items"; ?>">Cancel and go back</a>.</p>
	</form>
</div>