<?php
//$localsocket = 'tcp://127.0.0.1:1234';
$localsocket = 'tcp://127.0.0.1:2345';
$group = $_POST['group'];
$uploaddir = './uploads/';

$message = "<li>". $_POST['text']."</li>";


// ����������� � ��������� tcp-��������
$instance = stream_socket_client($localsocket);

 // . - ������� ����� ��� ��������� submit.php
 
    // �������� ����� ���� � ���
if(isset($_FILES['media']))
{ 
    if(!is_dir($uploaddir)) mkdir($uploaddir, 0777);
  

    // ���������� ����� �� ��������� ���������� � ���������
    foreach($_FILES as $file)
    {
        for($i=0; $i < count($file['name']); $i++)
        {
            if(move_uploaded_file($file['tmp_name'][$i], $uploaddir . basename($file['name'][$i])))
            {
                $message .= "<li><img src='http://s-alte.local/uploads/".$file['name'][$i]."'/></li>";
            }
            else
            {
                $error = true;
            }
        }
        
    }
}    
// ���������� ���������
fwrite($instance, json_encode(['group' => $group, 'text' => $message])  . "\n");
