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
    $(document).on('click', '#addField', function() {
        $.ajax({
            data : { 
            	desc : $('#desc').val(),
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
    $(document).on('click', '#deleteID', function() {
    	if (!confirm('Вы действительно хотите удалить поле?')) {
			return;
		}
        $.ajax({
            data : { 
            	listFieldDelete : $('#listFieldDelete').val()
            },
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
	<script>
function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("table");
  switching = true;
  // Set the sorting direction to ascending:
  dir = "asc"; 
  /* Make a loop that will continue until
  no switching has been done: */
  while (switching) {
    // Start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /* Loop through all table rows (except the
    first, which contains table headers): */
    for (i = 1; i < (rows.length - 1); i++) {
      // Start by saying there should be no switching:
      shouldSwitch = false;
      /* Get the two elements you want to compare,
      one from current row and one from the next: */
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      /* Check if the two rows should switch place,
      based on the direction, asc or desc: */
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /* If a switch has been marked, make the switch
      and mark that a switch has been done: */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      // Each time a switch is done, increase this count by 1:
      switchcount ++; 
    } else {
      /* If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again. */
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>
</head>
<body>
	<nav class="navbar navbar-default navbar-static-top" role="navigation">
	  <div class="container"><a class="navbar-brand">Данные пользователей</a><ul class="nav navbar-nav">
	  	<li><a href="index.php">Редактор</a></li>
	  	<li><a href="activeword.php">Активаторы</a></li>
	  	</ul></div>
	</nav>
	<div class="container">
		<div class="row">
			<ul class="nav nav-tabs">
			  <li class="active"><a data-toggle="tab" href="#home">Списки</a></li>
			  <li><a data-toggle="tab" href="#menu1">Добавить поля</a></li>
			  <li><a data-toggle="tab" href="#menu2">Удалить поля</a></li>
			</ul>
			
			<div class="tab-content">
			  <div id="home" class="tab-pane fade in active">
			    <div class="form-group">
				  <label for="id_father_question">Список полей:</label><br>
				  <select class="form-control" id="listField" multiple ><option>...</option>{listField} </select>
				  <!--{listFields2}-->
				</div>
				<table id="table" class="table table-striped table-bordered"></table>
			  </div>
			  <div id="menu1" class="tab-pane fade">
			      <div class="form-group">
				    <label for="name">Название(на англ без пробелов):</label>
				    <input type="text" class="form-control" id="name">
				  </div>
				  <div class="form-group">
				    <label for="desc">Описание (на русском):</label>
				    <input type="text" class="form-control" id="desc">
				  </div>
				  <button type="submit" class="btn btn-default" id="addField">Добавить</button>
			  </div>
			  <div id="menu2" class="tab-pane fade">
			    <div class="form-group">
				  <label for="id_father_question">Удаляется вместе с данными:</label>
				  <select class="form-control" id="listFieldDelete" ><option>...</option>{listField} </select>
				</div>
				  <button type="submit" class="btn btn-default" id="deleteID">Удалить</button>
			  </div>
			</div>
		</div>
	</div>
</body>
</html>
