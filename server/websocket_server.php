<?php
set_time_limit(0);

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
require_once '/var/www/html/messaging_application/vendor/autoload.php';

class Chat implements MessageComponentInterface {
	protected $clients;
	protected $users;

	public function __construct() {
		$this->clients = new \SplObjectStorage;
	}

	public function onOpen(ConnectionInterface $conn) {
		$this->clients->attach($conn);
	}

	public function onClose(ConnectionInterface $conn) {
		$this->clients->detach($conn);
	}

	public function onMessage(ConnectionInterface $from,  $data) {

		$randNames = ["John","Jacob","Carl","Karl","Joseph","Aaron","Angelina","Sarah","Carter","Cartman"];
		$from_id = $from->resourceId;
		$data = json_decode($data);
		$type = $data->type;
		switch ($type) {
			case 'chat':
				$user_id = $randNames[$data->user_id];
				$chat_msg = $data->chat_msg;
				$chat_msg = strip_tags($chat_msg);
				$response_from = "<span><b style='color: purple;'>".$user_id.":</b> ".$chat_msg."</span><br>";
				$response_to = "<b>".$user_id."</b>: ".$chat_msg."<br>";
				$from->send(json_encode(array("type"=>$type,"msg"=>$response_from)));
				foreach($this->clients as $client)
				{
					if($from!=$client)
					{
						$client->send(json_encode(array("type"=>$type,"msg"=>$response_to)));
					}
				}
				break;

		}
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		$conn->close();
	}
}
$server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Chat()
            )
        ),
        8080
    );
$server->run();
?>
