<?php
    require_once __DIR__ . '/vendor/autoload.php';
    use Workerman\Worker;
    use Workerman\Lib\Timer;
    use Workerman\WebServer;
    use Workerman\Connection\TcpConnection;
    
    $target_dir = "uploads/";
    
    $worker = new Worker('websocket://127.0.0.1:8000');
    
    $worker->name = 'MyWebsocketWorker';
    $worker->count = 6;
    $room = [];
    TcpConnection::$maxPackageSize = 263076660;
    
    $worker->onConnect = function($connection) use (&$room)
    {
        
        
        $connection->onWebSocketConnect = function($connection) use (&$room)
        {
            // put get-parameter into $users collection when a new user is connected
            // you can set any parameter on site page. for example client.html: ws = new WebSocket("ws://127.0.0.1:8000/?user=tester01");
            $group = $_GET['group'];
            $room[$group][$connection->id]  = $connection;
            
           // var_dump($connection);
            // or you can use another parameter for user identification, for example $_COOKIE['PHPSESSID']
        };
        echo "new connection from ip " . $connection->getRemoteIp()."\n";
    };
    
    $worker->onWorkerStart = function($worker) use (&$room)
    {
       // $worker->group = $_GET['group'];
       // var_dump($worker);
        
        echo "Worker starting...\n";
        // Timer every 10 seconds
        Timer::add(10, function() use ($worker)
        {
            // Iterate over connections and send the time
            foreach($worker->connections as $connection)
            {
                //$connection->send(time());
            }
        });
        // create a local tcp-server. it will receive messages from your site code (for example from send.php)
    
        /*$tcp_worker = new Worker("tcp://127.0.0.1:8001");
        // create a handler that will be called when a local tcp-socket receives a message (for example from send.php)
        $tcp_worker->onMessage = function($connection, $data) use ($worker) {
            // you have to use for $data json_decode because send.php uses json_encode
            $data = json_decode($data); // but you can use another protocol for send data send.php to local tcp-server
            // send a message to the user by userId
            
            foreach($worker->connections as $con)
            {
                var_dump($con);
                $con->send("reververv");
            }
           $connection->send("reververv");
        };
        $tcp_worker->listen();*/
        // #### http worker ####
        $http_worker = new Worker("tcp://127.0.0.1:2345");
        
        // 4 processes
        $http_worker->count = 4;
        
        // Emitted when data received
        $http_worker->onMessage = function($connection, $data) use (&$room)
        {
            $data = json_decode($data);
            // send data to client
            foreach($room[$data->group] as $con)
            {
                $con->send($data->text);
            }
           
        };
        $http_worker->listen();
    };
    
    
    
    $worker->onMessage = function($connection, $data)
    {
        //$connection->send($connection->id);
        foreach($connection->worker->connections as $con)
        {
            $con->send($data);
        }
        
    };
    
    // Run all workers
    Worker::runAll();