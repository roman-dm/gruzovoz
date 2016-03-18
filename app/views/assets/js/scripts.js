digitalWatch(); 
  function digitalWatch() {
    var date = new Date();
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();
    if (hours < 10) hours = "0" + hours;
    if (minutes < 10) minutes = "0" + minutes;
    if (seconds < 10) seconds = "0" + seconds;
    $("#digital_watch").html("<i class='fa fa-clock-o'></i>&nbsp" + hours + ":" + minutes + ":" + seconds);
    setTimeout("digitalWatch()", 1000);
  }
$(function() {
	$( document ).on('click','.rotatearrow', function(){
		$(this).parent().siblings(".subgroup").slideToggle();
		$(this).toggleClass("rotate");
	});
    $('.hide-sidebar').click(function() {
	  $('#sidebar').hide('fast', function() {
	  	$('#content').removeClass('span9');
	  	$('#content').addClass('span12');
	  	$('.hide-sidebar').hide();
	  	$('.show-sidebar').show();
	  });
	});

	$('.show-sidebar').click(function() {
		$('#content').removeClass('span12');
	   	$('#content').addClass('span9');
	   	$('.show-sidebar').hide();
	   	$('.hide-sidebar').show();
	  	$('#sidebar').show('fast');
	});
	$(document).on('change', '.check_all', function () {
  		link=$(this);
  	if (link.is(':checked')){
  		$(this).closest(".leadgroup").find(".subgroup").each(function(){
  			$(this).find(".chk").attr("checked","checked");
  			$(this).find(".chk").prop("checked", true);
  			$(this).find(".chk").parent().addClass("checked");
  		});
  	}else{
  		$(this).closest(".leadgroup").find(".subgroup").each(function(){
  			$(this).find(".chk").removeAttr("checked");
  			$(this).find(".chk").prop("checked", false);
  			 $(this).find(".chk").parent().removeClass("checked");
  		});
  	}
	});
	function hideBlock(){
		$(".hideblock").each(function(){
			$(this).css("display","none");
		});
	}
	//кнопки в ленте на главной
	$(document).on('click', '.allten', function () {
		$(this).parent().addClass('fc-state-active');
		$(this).parent().siblings('.fc-state-active').removeClass('fc-state-active');
		hideBlock();
		$(".ten_table").css("display","block");
	});
	$(document).on('click', '.taskten', function () {
		$(this).parent().addClass('fc-state-active');
		$(this).parent().siblings('.fc-state-active').removeClass('fc-state-active');
		hideBlock();
		$(".task_table").css("display","block");
	});
	$(document).on('click', '.eventten', function () {
		$(this).parent().addClass('fc-state-active');
		$(this).parent().siblings('.fc-state-active').removeClass('fc-state-active');
		hideBlock();
		$(".event_table").css("display","block");
	});
	$(document).on('click', '.visaten', function () {
		$(this).parent().addClass('fc-state-active');
		$(this).parent().siblings('.fc-state-active').removeClass('fc-state-active');
		hideBlock();
		$(".visa_table").css("display","block");
	});
	//кнопки в ленте на таске
	$(".nowtasks_table").css("display","none");
	$(".donetasks_table").css("display","none");
	$(".expiredtasks_table").css("display","none");
	$(document).on('click', '.alltasks', function () {
		$(this).parent().addClass('fc-state-active');
		$(this).parent().siblings('.fc-state-active').removeClass('fc-state-active');
		hideBlock();
		$(".alltasks_table").css("display","block");
	});
	$(document).on('click', '.nowtasks', function () {
		$(this).parent().addClass('fc-state-active');
		$(this).parent().siblings('.fc-state-active').removeClass('fc-state-active');
		hideBlock();
		$(".nowtasks_table").css("display","block");
	});
	$(document).on('click', '.donetasks', function () {
		$(this).parent().addClass('fc-state-active');
		$(this).parent().siblings('.fc-state-active').removeClass('fc-state-active');
		hideBlock();
		$(".donetasks_table").css("display","block");
	});
	$(document).on('click', '.expiredtasks', function () {
		$(this).parent().addClass('fc-state-active');
		$(this).parent().siblings('.fc-state-active').removeClass('fc-state-active');
		hideBlock();
		$(".expiredtasks_table").css("display","block");
	});
	//отправка визы аяксик
	$(document).on('click', '#oktask', function () {
		$(".curvisatask").empty();
		$(".curvisatask").html("<div class='alert alert-success' style='width:250px'><strong>Одобрено!</strong> Ваша виза добавлена.</div>");
		$(".curvisatask").removeClass("curvisa");
		$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				idUser: $(".idUser").val(),
				idtask: $("#idTask").val(),
				type_request: 'addVisaTask'
			}
		}).done(function(data){
		});
	});
	$(document).on('click', '#neoktask', function () {
		$(".curvisatask").empty();
		$(".curvisatask").html("<div class='alert alert-error' style='width:250px'><strong>Отклонено!</strong> Ваша виза добавлена.</div>");
		$(".curvisatask").removeClass("curvisa");
		$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				idUser: parseInt($(".idUser").val())*(-1),
				idtask: $("#idTask").val(),
				type_request: 'addVisaTask'
			}
		}).done(function(data){
		});
	});
	$(document).on('click', '#okevent', function () {
		$(".curvisaevent").empty();
		$(".curvisaevent").html("<div class='alert alert-success' style='width:250px'><strong>Одобрено!</strong> Ваша виза добавлена.</div>");
		$(".curvisaevent").removeClass("curvisa");
		$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				idUser: $(".idUser").val(),
				idevent: $("#idEvent").val(),
				type_request: 'addVisaEvent'
			}
		}).done(function(data){
		});
	});
	$(document).on('click', '#neokevent', function () {
		$(".curvisaevent").empty();
		$(".curvisaevent").html("<div class='alert alert-error' style='width:250px'><strong>Отклонено!</strong> Ваша виза добавлена.</div>");
		$(".curvisaevent").removeClass("curvisa");
		$.ajax({
			url: '/ajax/',
			type: 'POST',
			data: {
				idUser: parseInt($(".idUser").val())*(-1),
				idevent: $("#idEvent").val(),
				type_request: 'addVisaEvent'
			}
		}).done(function(data){
		});
	});
	$(document).on('click', '#AddEventBtnOpen', function () {
		$(".uploader").attr("id","EventOpenForm");
	});
	$(document).on('click', '#AddTaskBtnOpen', function () {
		$(".uploader").attr("id","TaskOpenForm");
	});
	$(document).on('click', '#AddNewsBtnOpen', function () {
		$(".uploader").attr("id","NewsOpenForm");
	});
	$(document).on('click', '.edit', function (e) {
		e.preventDefault();
		$(this).find("a").html("Сохранить");
		$(this).removeClass("edit");
		$(this).addClass("save");
		$(".redactor_icon").each(function(){
			$(this).css("display","inline-block");
		});
	});
	$(document).on('click', '.save', function (e) {
		e.preventDefault();
		$(this).find("a").html("Редактировать");
		$(this).removeClass("save");
		$(this).addClass("edit");
		$(".redactor_icon").each(function(){
			$(this).css("display","none");
		});
	});
	$(document).on('click', '#EditName', function (e) {
		e.preventDefault();
		$(".caption").html("<input value='"+$.trim($(".caption").text())+"'>")
	});
	$(document).on('click', '#EditDesc', function (e) {
		e.preventDefault();
		$(".detail").html("<textarea>"+$.trim($(".detail").text())+"</textarea>")
	});
});