$(window).on('load', function () {
    var $preloader = $('#page-preloader'),
        $spinner   = $preloader.find('.spinner');
    $spinner.fadeOut();
    $preloader.delay(350).fadeOut('slow');
});
$(document).ready (function () {
	//функция выбора иконки на визу
	function getIcon(iduservisa){
		if (iduservisa > 0){
			return "<i class='fa fa-check' style='color:green'></i>";
		} else {
			return "<i class='fa fa-times red' style='color:red'></i>";
		}
	}
	//перевороты даты 2 функции
	function ReversDate(RevDate) {
		return RevDate.substr(-4,4)+'-'+RevDate.substr(-7,2)+'-'+RevDate.substr(0,2);
	}
	function ReversDate2(RevDate2) {
		return RevDate2.substr(8,2)+'.'+RevDate2.substr(5,2)+'.'+RevDate2.substr(0,4);
	}
	GetUsers();
	getCurDate();
	GetTen();
	GetDetailTen();
	//кнопки и год (заполнение календаря при смене)
  $(document).on('click','.fc-button-next', function(){
  	getCurDate();
  });
  $(document).on('click','.fc-button-prev', function(){
    getCurDate();
  });
  $(document).on('change','#year_calendar', function(){
    getCurDate();
  });

  	//заполнение ленты на главной
	function GetTen(){
	$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				iUserId: $(".idUser").val(),
				type_request: 'getListEventTask'
			}
		}).done(function(data){
			result=JSON.parse(data);
			// console.log(result);
			visa='';
			icon='';
			resp="";
			for (var i = 0; i < result.all.length; i++) {
				if (result.all[i].viza[0].length == 0){
					visa='нет';
					if (result.all[i].type == 'task'){
						if (result.all[i].iTaskUserIdOt == $(".idUser").val()){
							resp="<span class='label label-important'>Вы ответственный</span>";
						} else {
							resp='';
						}
						$(".visa_body").append("<tr class='success'><td class='visa'>"+visa+"</td><td><a href='/"+$('.mainPageUser').val()+"/detailtask/?id_task="+result.all[i].iTaskId+"'>"+result.all[i].sTaskName+"</a>"+resp+"</td><td>"+ReversDate2(result.all[i].dTaskDateEnd)+"</td><td>"+result.all[i].sUserSecondName+' '+result.all[i].sUserName.substr(0,1)+'. '+result.all[i].sUserThirdName.substr(0,1)+'.'+"</td></tr>");
					}
					if (result.all[i].type == 'event'){
						$(".visa_body").append("<tr class='info'><td class='visa'>"+visa+"</td><td><a href='/"+$('.mainPageUser').val()+"/detailevent/?id_event="+result.all[i].iEventId+"'>"+result.all[i].sEventName+"</a></td><td>"+ReversDate2(result.all[i].dEventDateEnd)+"</td><td>"+result.all[i].sUserSecondName+' '+result.all[i].sUserName.substr(0,1)+'. '+result.all[i].sUserThirdName.substr(0,1)+'.'+"</td></tr>");
					}
				}
				if (result.all[i].viza[0].length !== 0){
					visa="<span class='check'>"+getIcon(result.all[i].iVisa)+"</span>"+result.all[i].viza[0].sUserSecondName+' '+result.all[i].viza[0].sUserName.substr(0,1)+'. '+result.all[i].viza[0].sUserThirdName.substr(0,1)+'.';
				} else {
					visa='Нет';
				}
				if (result.all[i].type == 'task'){
					if (result.all[i].iTaskUserIdOt == $(".idUser").val()){
							resp="<span class='label label-important'>Вы ответственный</span>";
						} else {
							resp='';
						}
					$(".ten_body").append("<tr class='success'><td class='visa'>"+visa+"</td><td><a href='/"+$('.mainPageUser').val()+"/detailtask/?id_task="+result.all[i].iTaskId+"'>"+result.all[i].sTaskName+"</a>"+resp+"</td><td>"+ReversDate2(result.all[i].dTaskDateEnd)+"</td><td>"+result.all[i].sUserSecondName+' '+result.all[i].sUserName.substr(0,1)+'. '+result.all[i].sUserThirdName.substr(0,1)+'.'+"</td></tr>");
				}
				if (result.all[i].type == 'event'){
					$(".ten_body").append("<tr class='info'><td class='visa'>"+visa+"</td><td><a href='/"+$('.mainPageUser').val()+"/detailevent/?id_event="+result.all[i].iEventId+"'>"+result.all[i].sEventName+"</a></td><td>"+ReversDate2(result.all[i].dEventDateEnd)+"</td><td>"+result.all[i].sUserSecondName+' '+result.all[i].sUserName.substr(0,1)+'. '+result.all[i].sUserThirdName.substr(0,1)+'.'+"</td></tr>");
				}
			}

			for (var i = 0; i < result.tasks.length; i++) {
				if (result.tasks[i].viza[0].length !== 0){
					visa="<span class='check'>"+getIcon(result.tasks[i].iVisa)+"</span>"+result.tasks[i].viza[0].sUserSecondName+' '+result.tasks[i].viza[0].sUserName.substr(0,1)+'. '+result.tasks[i].viza[0].sUserThirdName.substr(0,1)+'.';
				} else {
					visa='Нет'
				}
				if (result.tasks[i].iTaskUserIdOt == $(".idUser").val()){
							resp="<span class='label label-important'>Вы ответственный</span>";
						} else {
							resp='';
						}
				$(".task_body").append("<tr class='success'><td class='visa'>"+visa+"</td><td><a href='/"+$('.mainPageUser').val()+"/detailtask/?id_task="+result.tasks[i].iTaskId+"'>"+result.tasks[i].sTaskName+"</a>"+resp+"</td><td>"+ReversDate2(result.tasks[i].dTaskDateEnd)+"</td><td>"+result.tasks[i].sUserSecondName+' '+result.tasks[i].sUserName.substr(0,1)+'. '+result.tasks[i].sUserThirdName.substr(0,1)+'.'+"</td></tr>");
			}

			for (var i = 0; i < result.events.length; i++) {
				if (result.events[i].viza[0].length !== 0){
					visa="<span class='check'>"+getIcon(result.events[i].iVisa)+"</span>"+result.events[i].viza[0].sUserSecondName+' '+result.events[i].viza[0].sUserName.substr(0,1)+'. '+result.events[i].viza[0].sUserThirdName.substr(0,1)+'.';
				} else {
					visa='Нет'
				}
				$(".event_body").append("<tr class='info'><td class='visa'>"+visa+"</td><td><a href='/"+$('.mainPageUser').val()+"/detailevent/?id_event="+result.events[i].iEventId+"'>"+result.events[i].sEventName+"</a></td><td>"+ReversDate2(result.events[i].dEventDateEnd)+"</td><td>"+result.events[i].sUserSecondName+' '+result.events[i].sUserName.substr(0,1)+'. '+result.events[i].sUserThirdName.substr(0,1)+'.'+"</td></tr>");
			}
		});
	}
	//получение ленты на странице редактирования
	function GetDetailTen(){
	$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				iUserId: $(".idUser").val(),
				type_request: 'getListEventTaskUser'
			}
		}).done(function(data){
			result=JSON.parse(data);
			resp='';
			for (var i = 0; i < result.all.length; i++) {
				if (result.all[i].type == 'task'){
					$(".ten_body_edit").append("<tr class='success'><td><a href='/"+$('.mainPageUser').val()+"/detailtask/?id_task="+result.all[i].iTaskId+"'>"+result.all[i].sTaskName+"</a></td><td>"+ReversDate2(result.all[i].dTaskDateEnd)+"</td><td><div class='groupbtn'><div class='fabtn'><a class='deletebtn' name='task-"+result.all[i].iTaskId+"' href='#'><i class='fa fa-trash-o fa-fw'></i></a></div><div class='fabtn'>	<a class='edittask' href='#edittask' name="+result.all[i].iTaskId+" data-toggle='modal'><i class='fa fa-cog fa-fw'></i></a></div></div></td></tr>");
				}
				if (result.all[i].type == 'event'){
					$(".ten_body_edit").append("<tr class='info'><td><a href='/"+$('.mainPageUser').val()+"/detailevent/?id_event="+result.all[i].iEventId+"'>"+result.all[i].sEventName+"</a></td><td>"+ReversDate2(result.all[i].dEventDateEnd)+"</td><td><div class='groupbtn'><div class='fabtn'><a class='deletebtn' name='event-"+result.all[i].iEventId+"' href='#'><i class='fa fa-trash-o fa-fw'></i></a></div><div class='fabtn'>	<a class='editevent' href='#editevent' name="+result.all[i].iEventId+" data-toggle='modal'><i class='fa fa-cog fa-fw'></i></a></div></div></td></tr>");
				}
			}

			for (var i = 0; i < result.tasks.length; i++) {
				$(".task_body_edit").append("<tr class='success'><td><a href='/"+$('.mainPageUser').val()+"/detailtask/?id_task="+result.tasks[i].iTaskId+"'>"+result.tasks[i].sTaskName+"</a></td><td>"+ReversDate2(result.tasks[i].dTaskDateEnd)+"</td><td><div class='groupbtn'><div class='fabtn'><a class='deletebtn' name='"+result.tasks[i].iTaskId+"' href='#'><i class='fa fa-trash-o fa-fw'></i></a></div><div class='fabtn'>	<a class='edittask' href='#edittask' name="+result.tasks[i].iTaskId+" data-toggle='modal'><i class='fa fa-cog fa-fw'></i></a></div></div></td></tr>");
			}
			
			for (var i = 0; i < result.events.length; i++) {
				$(".event_body_edit").append("<tr class='info'><td><a href='/"+$('.mainPageUser').val()+"/detailevent/?id_event="+result.events[i].iEventId+"'>"+result.events[i].sEventName+"</a></td><td>"+ReversDate2(result.events[i].dEventDateEnd)+"</td><td><div class='groupbtn'><div class='fabtn'><a class='deletebtn' name='"+result.events[i].iEventId+"' href='#'><i class='fa fa-trash-o fa-fw'></i></a></div><div class='fabtn'>	<a class='editevent' href='#editevent' name="+result.events[i].iEventId+" data-toggle='modal'><i class='fa fa-cog fa-fw'></i></a></div></div></td></tr>");
			}
		});
	}
	//получение списка юзеров
	function GetUsers(){
	$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				query_type: 'user_list',
				type_request: 'getUser'
			}
		}).done(function(data){
		});
	}
	//очистка календаря от событий
	function delTd(){
		$("#calendar3").find("td").each(function(){
			$(this).find(".fc-day-content").empty();
		});
	}
	//очистка формы
	function clearForm(idform){
		$("#"+idform).trigger( 'reset' );
		$("#"+idform).find("[type='checkbox']").each(function(){
			$(this).attr('checked',false);
		});
	}
	//заполнение календаря событиями
	function getCurDate(){
	$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				id_user: $(".idUser").val(),
				month: parseInt($("#month").val())+1,
				year: $("#year_calendar").val(),
				type_request: 'getCurDate'
			}
		}).done(function(data){
			result=JSON.parse(data);
			console.log(result);
			for (var i = 0; i < result.length; i++) {
				if (result[i].type == 'event'){
					$("[data-current="+parseInt(result[i].dCurDate.substr(-2,2))+"]").find(".fc-day-content").append("<a href='/"+$('.mainPageUser').val()+"/detailevent/?id_event="+result[i].iEventId+"' title='"+result[i].sEventName+"'><div class='calendar_event'><div class=''><span class=''>"+result[i].sEventName.substr(0,12)+"</span></div></div></a>");
					if (result[i].iEventUserIdOt == $(".idUser").val()){
						$(".calendar_event").addClass("ce_ot");
					}
				}
				if (result[i].type == 'task'){
					$("[data-current="+parseInt(result[i].dCurDate.substr(-2,2))+"]").find(".fc-day-content").append("<a href='/"+$('.mainPageUser').val()+"/detailtask/?id_task="+result[i].iTaskId+"' title='"+result[i].sTaskName+"'><div class='calendar_task'><div class=''><span class=''>"+result[i].sTaskName.substr(0,12)+"</span></div></div></a>");
					if (result[i].iTaskUserIdOt == $(".idUser").val()){
						$(".calendar_task").addClass("ce_ot");
					}
				}
			}
		});
	}
	//отправка формы создания события
	$(document).on('click','#AddEventBtn', function(e){
		e.preventDefault();
		btnthis=$(this);
		user_list= [];
		files=[];
		files_str="";
		$("#AddEvent").find(".subgroup").each(function(){
			if($(this).find("input[type=checkbox]").is(":checked")){
				user_list.push($(this).find("input[type=checkbox]").attr("data-user"));
			}
		});
		$("#AddEvent").find(".ok").each(function(){
			obj=new Object;
			obj.fold=$(this).attr("data-fold");
			obj.name=$(this).attr("data-name");
			files.push(obj);
		});
		user_list.push($(".idUser").val());
		json_str=JSON.stringify(user_list);
		console.log(files);
		if(files.length>0){
			files_str=JSON.stringify(files);
		}
		if(checkFormEvent($("#form_event"))){
			$.ajax({
				url: '/ajax/',
				type: 'POST',
				data: {
					sEventName: $('#name_event').val(),
					sEventDesc: $('#text_event').val(),
					dEventDateStart: ReversDate($('#startdateevent').val()),
					dEventDateEnd: ReversDate($('#enddateevent').val()),
					iEventUserIdOt: $("#ot_event").val(),
					aUsers:json_str,
					fFiles:files_str,
					iEventUserCreate: $(".idUser").val(),
					type_request: 'processEvent',
				}
			}).done(function(data){
			clearFilesForm();
			btnthis.attr("data-dismiss","modal");
			$(".close").click();
			$(".ten_body").empty();
			$(".task_body").empty();
			$(".event_body").empty();
			$(".visa_body").empty();
			GetTen();
			delTd();
			clearForm("form_event");
			getCurDate();
			});
		}
	});
	//отправка формы создания задачи
	$(document).on('click','#AddTaskBtn', function(){
		btnthis=$(this);
		user_list= [];
		files=[];
		files_str="";
		$("#AddTask").find(".subgroup").each(function(){
			if($(this).find("input[type=checkbox]").is(":checked")){
				user_list.push($(this).find("input[type=checkbox]").attr("data-user"));
			}
		});
		console.log(user_list);
		$("#AddTask").find(".ok").each(function(){
			obj=new Object;
			obj.fold=$(this).attr("data-fold");
			obj.name=$(this).attr("data-name");
			files.push(obj);
		});
		user_list.push($(".idUser").val());
		json_str=JSON.stringify(user_list);
		if(files.length>0){
			files_str=JSON.stringify(files);
		}
		if(checkFormTask($("#form_task"))){
			$.ajax({
				url: '/ajax/',
				type: 'POST',
				data: {
					sTaskName: $('#name_task').val(),
					sTaskDesc: $('#text_task').val(),
					dTaskDateStart: ReversDate($('#startdatetask').val()),
					dTaskDateEnd: ReversDate($('#enddatetask').val()),
					iTaskUserIdOt: $("#taskot").val(),
					aUsers:json_str,
					fFiles:files_str,
					iTaskUserCreate: $(".idUser").val(),
					type_request: 'processTask',
				}
			}).done(function(data){
			clearFilesForm();
			btnthis.attr("data-dismiss","modal");
			$(".close").click();
			$(".ten_body").empty();
			$(".task_body").empty();
			$(".event_body").empty();
			$(".visa_body").empty();
			GetTen();
			delTd();
			clearForm("form_task");
			getCurDate();
			});
		}
	});
	//отправка формы создания новости
	$(document).on('click','#AddNewsBtn', function(){
		btnthis=$(this);
		user_list= [];
		files=[];
		files_str="";
		$("#AddNews").find(".subgroup").each(function(){
			if($(this).find("input[type=checkbox]").is(":checked")){
				user_list.push($(this).find("input[type=checkbox]").attr("data-user"));
			}
		});
		$("#AddNews").find(".ok").each(function(){
			obj=new Object;
			obj.fold=$(this).attr("data-fold");
			obj.name=$(this).attr("data-name");
			files.push(obj);
		});
		user_list.push($(".idUser").val());
		json_str=JSON.stringify(user_list);
		if(files.length>0){
			files_str=JSON.stringify(files);
		}
		if(checkFormNews($("#form_news"))){
			$.ajax({
				url: '/ajax/',
				type: 'POST',
				data: {
					sNewsName: $('#name_news').val(),
					sNewsDetail: $('#text_news').val(),
					aUsers:json_str,
					fFiles:files_str,
					iNewsUserCreate: $(".idUser").val(),
					type_request: 'processNews',
				}
			}).done(function(data){
				clearForm("form_news");
				clearFilesForm();
				btnthis.attr("data-dismiss","modal");
				$(".close").click();
			});
		}
	});
//механизм выполнения задачи
	$(document).on('click', '#task_done', function () {
		$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				idTask: $("#idTask").val(),
				type_request: 'taskDone'
			}
		}).done(function(data){
			$(".need_done").html("<div class='martop inline'><div class='alert' style='width:145px' id='task_complite'>Вы выполнили задачу</div></div>");
		});
	});
	$(document).on('click', '.delete', function () {
		$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				typeiditem: $(this).attr("name"),
				type_request: 'delete_item'
			}
		}).done(function(data){
			alert("/"+$(".mainPageUser").val()+"/tasks/");
			location.href = "/"+$(".mainPageUser").val()+"/tasks/"
		});
	});
	$(document).on('click', '.deletebtn', function () {
		$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				typeiditem: $(this).attr("name"),
				type_request: 'delete_item'
			}
		}).done(function(data){
			$(".ten_body_edit").empty();
			$(".task_body_edit").empty();
			$(".event_body_edit").empty();
			GetDetailTen();
		});
	});
	$(document).on('click', '.edittask', function () {
		$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				idTask: $(this).attr("name"),
				type_request: 'get_one_task'
			}
		}).done(function(data){
		});
	});
	$(document).on('click', '.editevent', function () {
		$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				idEvent: $(this).attr("name"),
				type_request: 'get_one_event'
			}
		}).done(function(data){
		});
	});
	$(document).on('click', '.save', function () {
		idTen="";
		if ($(".type").attr("value") == "task"){
			idTen = "idTask";
		}
		if ($(".type").attr("value") == "event"){
			idTen = "idEvent";
		}
		$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				type: $(".type").attr("value"),
				idTen: $("#"+idTen).val(),
				type_request: 'update_oneten'
			}
		}).done(function(data){
		});
	});
});
//функция чистит файлы с формы
function clearFilesForm(){
	$(".uploader").each(function(){
		$(this).siblings("ul").find("li").remove();
	});
}