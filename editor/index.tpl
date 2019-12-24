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
	
	$(document).on('change', '#is_disable_button', function () {
		var sData = {
			is_disable_button:  $(this).val(),
			id_question: $(this).data("id"),
		}
		$.ajax({
			data: sData, type: 'POST', dataType: 'json', success: function(data){
				
			}});
	});
	$(document).on('change', '#is_next_question', function () {
		var sData = {
			is_next_question:  $(this).val(),
			id_question: $(this).data("id"),
		}
		$.ajax({
			data: sData, type: 'POST', dataType: 'json', success: function(data){
				
			}});
	});
    
	$(document).on('change', '#question', function() {
        $.ajax({
            data : {
                text_question : $('#question').val(),
                question_id : $('#question').data('id')
            },
            dataType: 'json',
			type: 'POST',
            success : function(data) {
                if (data.code == 'ok')
                	$("#save").text("Сохранено").show().delay(300).fadeOut();
            }
        })
    })
    
    $(document).on('click', '#add_question', function() {
        $.ajax({
            data : {
                question_add : $('#question_add').val(),
                id_father_question_add : $('#id_father_question_add').val()
            },
            dataType: 'json',
			type: 'POST',
            success : function(data) {
                alert('Добавлено');
                
              	document.location.reload(true);
            }
        })
    })
    
    $(document).on('click', '#addAnswer', function() {
    	sendingData = {
        	answer_add : $(this).data('id')
        }
        $.ajax({
            data : sendingData,
            dataType: 'json',
			type: 'POST',
            success : function(data) {
            	alert('Добавлено');
                
              	document.location.reload(true);
            }
        })
    })
    
    
    $(document).on('change', '#allQuestionsEdit', function() {
    	window.location.replace("https://daniilak.ru/bot_tg/snochuvsu/editor/index.php?id=" + $('#allQuestionsEdit :selected').val());
    })
    
    $(document).on('change', '#id_father_question', function() {
        $.ajax({
            data : { 
            	id_father_question : $('#id_father_question :selected').val(), 
            	question_id : $('#question').data('id') 
            },
            dataType: 'json',
			type: 'POST',
            success : function(data) {
                if (data.code == 'ok')
                	$("#save").text("Сохранено").show().delay(300).fadeOut();
            }
        })
    })
    
    
    $(document).on('change', '#select_function_code', function() {
    	sendingData = { 
            	select_function_code_val : $(this).val(), 
            	select_function_code_id : $(this).data('id') 
            }
        $.ajax({
            data : sendingData,
            dataType: 'json',
			type: 'POST',
            success : function(data) {
                if (data.code == 'ok')
                	$("#save").text("Сохранено").show().delay(300).fadeOut();
            }
        })
    })
    
    
    $(document).on('change', '#select_answer', function() {
    	sendingData = { 
            	select_answer : $(this).val(), 
            	id_answer : $(this).data('id') 
            }
        $.ajax({
            data : sendingData,
            dataType: 'json',
			type: 'POST',
            success : function(data) {
                if (data.code == 'ok')
                {
                	$("#get_next_answer[data-id='" + sendingData.id_answer + "']")
                		.data('next', 		sendingData.select_answer)
                		.attr('data-next',	sendingData.select_answer);
                	$("#save").text("Сохранено").show().delay(300).fadeOut();
                }
            }
        })
    })
    
    
    $(document).on('change', '#function_code', function() {
    	let sendingData = { 
           	function_code_val : $(this).val(), 
          	function_code_id : $(this).data('id') 
        };
        $.ajax({
            data : sendingData,
            dataType: 'json',
			type: 'POST',
            success : function(data) {
                if (data.code == 'ok')
                	$("#save").text("Сохранено").show().delay(300).fadeOut();
            }
        })
    })
    
    $(document).on('change', '#textarea_answer', function() {
    	let sendingData = { 
           	textarea_answer_val : $(this).val(), 
          	textarea_answer_id : $(this).data('id') 
        };
        $.ajax({
            data : sendingData,
            dataType: 'json',
			type: 'POST',
            success : function(data) {
                if (data.code == 'ok')
                	$("#save").text("Сохранено").show().delay(300).fadeOut();
            }
        })
    })
    
    
    $(document).on('click', '#get_next_answer', function() {
    	next = $(this).data('next')
        window.location.replace("https://daniilak.ru/bot_tg/snochuvsu/editor/index.php?id=" + next);
    })
    
    $(document).on('click', '#get_delete_answer', function() {
    	sendingData = {
    		id_delete_answer: $(this).data('id')
    	}
    	if (!confirm('Вы действительно хотите удалить ответ?')) {
			return;
		}
        $.ajax({
            data : sendingData,
            dataType: 'json',
			type: 'POST',
            success : function(data) {
            	document.location.reload(true);
            }
        })
    })
    
    $(document).on('click', '#deleteQuestion', function() {
    	if (!confirm('Вы действительно хотите удалить вопрос?')) {
			return;
		}
        $.ajax({
            data : { 
            	deleteQuestion : $('#deleteQuestion').data('id') 
            },
            dataType: 'json',
			type: 'POST',
            success : function(data) {
            	alert('Удалено');
                window.location.replace("https://daniilak.ru/bot_tg/snochuvsu/editor/index.php?id=1");
            }
        })
    })
    
	</script>
</head>
<body>
	<nav class="navbar navbar-default navbar-static-top" role="navigation">
	  <div class="container"><a class="navbar-brand">Редактор</a><ul class="nav navbar-nav">
	  	<li><a href="datauser.php">Данные пользователей</a></li>
	  	<li><a href="activeword.php">Активаторы</a></li>
	  	<li><a href="#" data-toggle="modal" data-target="#myModal"><b>Добавить новый вопрос в БД</b></a></li>
	  	</ul></div>
	</nav>
	<div class="container-fluid">
		<b id="save"></b>
		<div class="form-group">
			<label for="allQuestionsEdit">Вопрос, который бы будем редактировать:</label>
			<select class="form-control" id="allQuestionsEdit">{allQuestionsEdit} </select>
		</div>
		<ul class="breadcrumb">
			{breadcrumb}
		    <!--<li class="active">Accessories</li>-->
		</ul>
		<hr>
		<div class="row">
		  <div class="col-md-4">
		  	<h4>Редактирование вопроса: #{id_question}</h4>
			<div class="form-group">
			  <textarea class="form-control" rows="7" id="question" data-id="{id_question}">{questionTextarea}</textarea>
			</div>
			<div class="form-group">
			  <label for="id_father_question">Родительский вопрос (Для кнопки Назад):</label>
			  <select class="form-control" id="id_father_question">{allQuestions} </select>
			</div>
			<div class="form-group">
				 <label for="id_father_question">Выберите пункт, чтобы кнопок не было и считывалось в поле пользователя:</label>
				<select class="form-control" id="is_disable_button" data-id="{id_question}"><option value="0">Не выбрано</option>{is_save_all_string}</select>
			</div>
			<div class="form-group">
				 <label for="id_father_question">Какой следующий вопрос (Выбирайте если выбран предыдущий пункт):</label>
				<select class="form-control" id="is_next_question" data-id="{id_question}"><option value="0">Не выбрано</option>{allQuestionsNext}</select>
			</div>
			
			<div class="form-group">
				 <button type="button" class="btn btn-danger" id="deleteQuestion"  data-id="{id_question}">Удалить вопрос</button>
			</div>
		
		  </div>
		  <div class="col-md-8">
		  	<div id="listAnswers">{listAnswers}</div>
				<button type="button" id="addAnswer" class="btn btn-info" data-id="{id_question}">Добавить ответ</button>
			</div>
		</div>

</div>
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Добавление вопроса:</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
			<label for="question">Вопрос:</label>
			<textarea class="form-control" rows="7" id="question_add"></textarea>
		</div>
		<div class="form-group">
			<label for="id_father_question_add">Родительский вопрос (Для кнопки Назад):</label>
			<select class="form-control" id="id_father_question_add">{allQuestions} </select>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="add_question">Добавить вопрос в БД</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>
