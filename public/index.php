<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
    <script>
        socket = new WebSocket("ws://127.0.0.1:8000/?group=<?=$_GET['group']?>");
        socket.onopen = function() {
          console.log("Соединение установлено.");
        };
        
        socket.onclose = function(event) {
          if (event.wasClean) {
            console.log('Соединение закрыто чисто');
          } else {
            console.log('Обрыв соединения'); // например, "убит" процесс сервера
          }
          console.log('Код: ' + event.code + ' причина: ' + event.reason);
        };
        
        socket.onmessage = function(event) {
            $('#list').append(event.data);
          console.log("Получены данные " + event.data);
        };
        
        socket.onerror = function(error) {
          console.log("Ошибка " + error.message);
        };
        /*$(function(){
            $("#chat").submit(function(){
                var data = $(this).find('textarea').val();
                var user = $(this).find('input').val();
                var group = <?=$_GET['group']?>;
                //var formData = new FormData($that.get(0));
                var data = new FormData(this);
                $.ajax({
                  method: "POST",
                  enctype: 'multipart/form-data',
                  url: "http://127.0.0.1:2345",
                  cache: false,
                  contentType: false,
                  processData: false,
                  data:data
                })
                .done(function(msg) {
                   console.log( "Data Saved: " + msg );
                   
                });
               
                                
               // socket.send(form.elements[0].file);
                return false;            
            });
        });*/
        $(document).ready(function () {

    $("#chat").submit(function(event) {

        //stop submit the form, we will post it manually.
        event.preventDefault();

        // Get form
        var form = $('#chat')[0];

		// Create an FormData object 
        var data = new FormData(form);

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "../start_web.php",
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {

               

            },
            error: function (e) {


            }
        });
        return false; 
    });

});
    </script>
</head>
<body>
<div>
<ul id="list">
<li>text</li>
</ul>
</div>
<form id="chat" method="POST" enctype="multipart/form-data">
<input name="media[]" type="file" multiple />
<input name="user"/>
<input type="hidden" name="group" value="<?=$_GET['group']?>" />
<textarea name="text">

</textarea>
<input type="submit" value="ok" name="submit"/>
</form>
</body>
</html>