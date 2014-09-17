<?php

if( !defined( "SITE_PATH" ) ) die( "Can't touch this." );

?>
<div id="login-form">
<h3>Login</h3>
<form action="<?php echo ADMIN_URL; ?>login" method="post" accept-charset="utf-8">
	<ul>
		<li><label for="username">Username</label><input type="text" name="username" value="" id="username"></li>
		<li><label for="userpass">Password</label><input type="password" name="userpass" value="" id="userpass"></li>
	</ul>
	<p><button type="submit">Login</button></p>
</form>
</div>