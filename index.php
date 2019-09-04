<?php
	$session = mt_rand(0,9);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Messaging Application</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<script src="js/jquery.js" type="text/javascript"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<link rel="stylesheet" href="css/main.css" type="text/css">
	</head>
	<body>



		<div class="container-fluid">
			<div class="row">
				<div class="col-xl" style="padding: 0px;">
					<h1 id="head" class="text-center">Messaging Application</h1>
				</div>
			</div>

			<div class="row">
				<div class="col-xl" id="chatBox">
					<div class="container-fluid">
						<div id="chat_output"></div>
					</div>
				</div>
			</div>



			<textarea id="chat_input" placeholder="Enter a Message"></textarea>




			<script type="text/javascript">

			function scroll(){
				var objDiv = document.getElementById("chatBox");
				objDiv.scrollTop = objDiv.scrollHeight;
			}

			jQuery(function($){
				var websocket_server = new WebSocket("ws://zyonbessette.com:8080/messaging_application");
				websocket_server.onopen = function(e) {
					websocket_server.send(
						JSON.stringify({
							'type':'socket',
							'user_id':<?php echo $session; ?>
						})
					);
				};
				websocket_server.onerror = function(e) {
					var xcb = "http://stackoverflow.com/search?q=" + e.message;
					window.open(xcb, '_blank');
				}
				websocket_server.onmessage = function(e)
				{
					var json = JSON.parse(e.data);
					switch(json.type) {
						case 'chat':
							$('#chat_output').append(json.msg);
							break;
					}
					scroll();
				}
				$('#chat_input').on('keyup',function(e){
					if(e.keyCode==13 && !e.shiftKey)
					{
						var chat_msg = $(this).val();
						websocket_server.send(
							JSON.stringify({
								'type':'chat',
								'user_id':<?php echo $session; ?>,
								'chat_msg':chat_msg
							})
						);
						$(this).val('');
					}
				});
			});
			</script>
		</div>
	</body>
</html>
