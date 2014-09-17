<?php

if( !defined( "SITE_PATH" ) ) die( "Can't touch this." );

$url_string = $_SERVER["REQUEST_URI"];

if( substr( $url_string, -5, 5 ) == ".json" ){
	define( "IS_AJAX", true );
	$url_string = substr( $url_string, 0, -5 );
} else {
	define( "IS_AJAX", false );
}

$url_string = explode("?",$url_string);

// Parse the URL into something useful
$url = array_values( array_filter( explode( "/", $url_string[0] ) ) );

if( !empty( $url[0] ) && $url[0] == "admin" ){
	array_shift( $url );
	define( "IS_ADMIN", true );
} else {
	define( "IS_ADMIN", false );
}
if( !empty( $url[0] ) && $url[0] == "index.php" )
	array_shift( $url );

// Let the routing begin
if( !logged_in() ){
	if( empty( $url[0] ) || sizeof($url) > 1 ){
		go_to("login");
	} else {
		if( $url[0] != "login" ){
			go_to("login");
		} else {
			if(
				!empty( $_POST["username"] ) &&
				!empty( $_POST["userpass"] ) &&
				$_POST["username"] == USERNAME &&
				$_POST["userpass"] == USERPASS
			){
				@setcookie( "pw_hash", sha1( md5( USERPASS ) . md5( USERNAME ) . "i like salt" ), time()+60*60*24*1 );
				go_to("items");
			} else {
				$t = new Template("login",true);
			}


		}
	}
} else {

	if( !empty( $url ) && !empty( $url[0] ) ){
		$portfolio = Portfolio::get_instance();

		switch( $url[0] ){
			case "login":
			go_to("items");
			break;

			case "logout":
			@setcookie( "pw_hash", false, time()-60*60*24*365 );
			go_to("login");
			break;

			case "reinstall":

			unlink( SITE_PATH.'storage/gallery.sqlite3' );
			go_to( "install" );

			break;

			case "install":

			// Not implemented yet

			break;

			case "publish":
			Cache::update();
			go_to("items?updated=true");

			break;


			case "items":

			$t = new Template("items");
			$t->set('items', $portfolio->items());
			$t->set('projects', $portfolio->projects());
			$t->render();

			break;

			case "item":
			if( !empty( $url[1] ) ){
	 			if( $url[1] == "add" ){
					if( !empty( $_FILES ) ) {

						try{
							$image = WideImage::load('image_original');
						} catch (Exception $e) {
							die( "Image isn't valid" );
						}

						$title = "New Portfolio Item";
						$desc = "";
						$project = 0;

						if( !empty($_FILES["image_original"]["name"]) )
							$title = array_shift(explode( ".", $_FILES["image_original"]["name"] ) );

						if( !empty($_POST["item_project"] ) )
							$project = escape( $_POST["item_project"] );

						$insert_id = $portfolio->item_add(
							$title,
							$desc,
							$project
						);

						$watermark = WideImage::load(SITE_PATH . "storage/watermark.png");

						$thumb = $image->resize(100, 100, "outside")->crop("50%-50","50%-50",100,100);
						$thumb_hover = $image->resize(100, 100, "inside")->crop("50%-50","50%-50",100,100);
						$thumb_h = $thumb_hover->getHeight();
						$thumb_w = $thumb_hover->getWidth();

						if( $thumb_h == 100 ){
							// Portait image
							$h_offset = 100;
							$w_offset = 50-($thumb_w/2);
						} else {
							// Landscape image
							$h_offset = 100+50-($thumb_h/2);
							$w_offset = 0;
						}

						$thumb_bkg = WideImage::load(SITE_PATH . "storage/thumbnail-frame-sprite-bkg.png");
						$thumb_bkg
							->merge( $thumb, 0, 0)
							->merge( $thumb_hover, $w_offset,$h_offset )
							->merge(WideImage::load(SITE_PATH . "storage/thumbnail-frame-sprite.png"),0,0)
							->saveToFile(IMAGE_PATH . 'image'.$insert_id.'_100.jpg');

						//->saveToFile(IMAGE_PATH . 'image'.$insert_id.'_100_hover.jpg')

						$image->resize(500, 500)->saveToFile(IMAGE_PATH . 'image'.$insert_id.'_500.jpg');
						$image->resize(1000, 1000)->saveToFile(UPLOAD_PATH . 'image'.$insert_id.'_orig.jpg');
						$image->resize(700,700)->merge($watermark, '0', '100%-350')->saveToFile(IMAGE_PATH . 'image'.$insert_id.'_700.jpg');

						go_to("item/$insert_id/edit");
					}
				} else {
					$id = (int) $url[1];
					if( !empty( $url[2] ) ) {
						if( $url[2] == "delete" ) {
							if( $portfolio->item_delete($id) ){
								@unlink(IMAGE_PATH . 'image'.$id.'_100.jpg');
								@unlink(IMAGE_PATH . 'image'.$id.'_700.jpg');
								@unlink(UPLOAD_PATH . 'image'.$id.'_orig.jpg');
								go_to("items?success=d");
							}
						} elseif( $url[2] == "edit" ) {

							$t = new Template("item-edit");
							$t->set('id', $id);
							$t->set('item', $portfolio->item($id));
							$t->set('items', $portfolio->items(true));
							$t->set('projects', $portfolio->projects());
							$t->render();

						} elseif( $url[2] == "save" ) {
							if( $_POST ) {
								$portfolio->item_update(
									$id, $_POST["item_title"], $_POST["item_desc"], $_POST["item_project"]
								);
								go_to("items?success=s");
							}
						}
					} else {
						echo $portfolio->item($id);
					}
				}
			} else {
				echo "Error: Second parameter required (integer)";
			}
			break;

			case "project":
			if( !empty( $url[1] ) ){
				if( $url[1] == "add" ){
					if( !empty( $_POST["project_title"] ) ){
						$portfolio->project_add( $_POST["project_title"] );
						go_to("items");
					} else {
						go_to("items");
					}
				} else {
					$id = (int) $url[1];
					if( !empty( $url[2] ) ) {
						if( $url[2] == "delete" ) {
							if( $portfolio->project_delete($id) ){
								go_to("items");
							} else {
								go_to("items");
							}
						} elseif( $url[2] == "edit" ) {

							$t = new Template("project-edit");
							$t->set('id', $id);
							$t->set('project', $portfolio->project($id));
							$t->render();

						} elseif( $url[2] == "save" ) {
							if( !empty( $_POST["project_title"] ) ){
								$portfolio->project_update( $id, $_POST["project_title"] );
								go_to("items");
							}
						}
					} else {
						// $portfolio->project($id);
					}
				}
			} else {
				go_to("items");
			}
			break;


			default:
			if( IS_AJAX ) {
				die( "{}" );
			} else {
				die( "Error: Parameter not recognized." );
			}
			break;
		}
	} else {
		if( logged_in() ){
			go_to("items");
		} else {
			go_to("login");
		}
	}
}

?>