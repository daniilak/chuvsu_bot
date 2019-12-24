<!DOCTYPE html>
<html lang="ru">
<head>
<title>Редактор чат-бота</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script>
    $(document).on('change', '#listField', function() {
        $.ajax({
            data : { 
            	idListField : $('#listField').val()
            },
            dataType: 'json',
			type: 'POST',
            success : function(data) {
              	$("#table").html(data.options);
            }
        })
    })
    $(document).on('click', '#add', function() {
        $.ajax({
            data : { 
            	allq : $('#allq').val(),
            	name : $('#name').val()
            },
            dataType: 'json',
			type: 'POST',
            success : function(data) {
            	if (data.msg == 'ok') {
              		alert('Добавлено');
              		document.location.reload(true);
            	} else {
            		alert(data.msg);
            	}
            }
        })
    })
    $(document).on('click', '#delete', function() {
    	if (!confirm('Вы действительно хотите удалить поле?')) {
			return;
		}
		sendingData = { 
            	listWordDelete : $(this).data('id')
            }
        $.ajax({
            data : sendingData,
            dataType: 'json',
			type: 'POST',
            success : function(data) {
            	if (data.msg == 'ok') {
              		alert('Удалено');
              		document.location.reload(true);
            	} else {
            		alert(data.msg);
            	}
            }
        })
    })
	</script>
</head>
<body>
	<nav class="navbar navbar-default navbar-static-top" role="navigation">
	  <div class="container"><a class="navbar-brand">Активаторы</a><ul class="nav navbar-nav">
	  	<li><a href="index.php">Редактор</a></li>
	  	<li><a href="datauser.php">Данные пользователей</a></li>
	  	<!--<li><a href="http://daniilak.ru/">daniilak</a></li>-->
	  	</ul>
	  	</div>
	</nav>
	<div class="container">
		<div class="row">
			<ul class="nav nav-tabs">
			  <li class="active"><a data-toggle="tab" href="#home">Список</a></li>
			  <li><a data-toggle="tab" href="#menu1">Добавить активатор</a></li>
			</ul>
			
			<div class="tab-content">
			  <div id="home" class="tab-pane fade in active">
			    <div class="form-group">
				  <label for="id_father_question">Список активаторов:</label>
				  <table class="table table-striped table-bordered">
				  	{tableWord}
				  </table>
				</div>
			  </div>
			  <div id="menu1" class="tab-pane fade">
			      <div class="form-group">
				    <label for="name">Активирующее слово (короткое желательно):</label>
				    <input type="text" class="form-control" id="name">
				  </div>
				  <div class="form-group">
				    <label for="desc">Какой вопрос активируется:</label>
				    <select class="form-control" id="allq" ><option>...</option>{allq} </select>
				  </div>
				  <button type="submit" class="btn btn-default" id="add">Добавить</button>
			  </div>
			</div>
		</div>
	</div>
</body>
</html>
