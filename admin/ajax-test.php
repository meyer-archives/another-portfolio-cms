<?php

include_once( "../load.php" );

?><html>
<head>
	<title></title>
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" title="CSS" charset="utf-8">

	<script src="<?php echo INCLUDES_URL; ?>jquery-1.3.2.min.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {

			$upload = $("#upload-image");

			$form = $("form#url-test");
			$("#url-to-test").focus(function(){
				$form.addClass("focus");
			}).blur(function(){
				$form.removeClass("focus");
			})
			$form.submit(function(){
				var testURL = $("#url-to-test").val();
				if( testURL ){
					$.ajax({
						url: "/admin/index.php/"+testURL,
						dataType: "json",
						success: function(e){
							var objectData = [];
							for( i in e ){
								var o = e[i];
								if( typeof( e[i] ) == "object" ){
									o = [];
									for( q in e[i] ){
										o.push( "<strong>"+ q + ":</strong> " + e[i][q] );
									}
									o = "<ul><li>"+o.join("</li><li>")+"</li></ul>";
									objectData.push( "<h4>"+i+"</h4>" + o );
								} else {
									objectData.push( "<strong>"+ i+":</strong> "+o );
								}
							}
							$("#content").html( "<h3>" + this.url + "</h3><div class='response'><ul><li>" + objectData.join("</li><li>") + "</li></ul></div>"  );
						},
						error: function(e){
							$("#content").html( "<h3>" + this.url + "</h3><div class='response'><p>" + e.responseText + "</p></div>" );
						}
					});
				}
				return false;
			});
		});
	</script>
</head>
<body>
	<div id="container">
		<form id="url-test" action="ajax.php" method="post" accept-charset="utf-8">
			<p>URL to test: <strong>/admin/index.php/</strong><input type="text" name="url-to-test" value="" id="url-to-test"> <input type="submit" value="TEST"></p>
		</form>
		<div id="content">
			<p>No data loaded.</p>
		</div>
	</div>
</body>
</html>