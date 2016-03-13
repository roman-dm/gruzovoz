$(document).ready (function () {
	$( document ).on('click','.rotatearrow', function(){
		$(this).parent().siblings(".subgroup").slideToggle();
		$(this).toggleClass("rotate");
	});
});
	function GetNewDate(newData){
		newData2=newData.split(".");
		newData2=newData2[2] +'-'+ newData2[1]+'-'+ newData2[0];
		return newData2;
	}
	function checkFormNews(idFrom){
		idFrom.find("input[type=text],textarea").each(function(){
			$(this).removeClass("error");
		});
		err=0;
		idFrom.find("input[type=text],textarea").each(function(){
			if(!emptyOrNo($(this))){
				err++;
				$(this).addClass("error");
			}
			if($(this).attr("id")=="name_news" && !lengthInput($(this).val(),50)){
				//Проверяем что бы в поле названия события было не больше 50 символов
				err++;
				$(this).addClass("error");
			}
		});
		if(err>0){
			return false;
		}
		else{
			return true;
		}
	}
	function checkFormTask(idFrom){
		idFrom.find("input[type=text],textarea").each(function(){
			$(this).removeClass("error");
		});
		err=0;
		now=new Date();
		// if(GetNewDate($("#startdateevent").val()) < new Date()){
		// 	alert("fck off");
		// }
		if(GetNewDate($("#startdatetask").val()) > GetNewDate($("#enddatetask").val())){
			err++;
			$("#startdatetask").addClass("error");
			$("#enddatetask").addClass("error");
		}
		idFrom.find("input[type=text],textarea").each(function(){
			if(!emptyOrNo($(this))){
				err++;
				$(this).addClass("error");
			}
			if($(this).attr("id")=="name_event" && !lengthInput($(this).val(),50)){
				//Проверяем что бы в поле названия события было не больше 50 символов
				err++;
				$(this).addClass("error");
			}
			var today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
			if((today.valueOf()) > (new Date(GetNewDate($("#startdatetask").val())).valueOf())){
					err++;
					$("#startdatetask").addClass("error");
			}
			if($(this).attr("id")=="startdateevent" || $(this).attr("id")=="enddateevent"){
				str2=GetNewDate($(this).val());
				//Проверяем на валидность дату
				if(new Date(str2)=='Invalid Date'){
					err++;
					$(this).addClass("error");
				}
			}
		});
		if(err>0){
			return false;
		}
		else{
			return true;
		}
	}
	function checkFormEvent(idFrom){
		idFrom.find("input[type=text],textarea").each(function(){
			$(this).removeClass("error");
		});
		err=0;
		now=new Date();
		// if(GetNewDate($("#startdateevent").val()) < new Date()){
		// 	alert("fck off");
		// }
		if(GetNewDate($("#startdateevent").val()) > GetNewDate($("#enddateevent").val())){
			err++;
			$("#startdateevent").addClass("error");
			$("#enddateevent").addClass("error");
		}
		var today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		if((today.valueOf()) > (new Date(GetNewDate($("#startdateevent").val())).valueOf())){
				err++;
				$("#startdateevent").addClass("error");
		}
		idFrom.find("input[type=text],textarea").each(function(){
			if(!emptyOrNo($(this))){
				err++;
				$(this).addClass("error");
			}
			if($(this).attr("id")=="name_task" && !lengthInput($(this).val(),50)){
				//Проверяем что бы в поле названия события было не больше 50 символов
				err++;
				$(this).addClass("error");
			}
			if($(this).attr("id")=="startdatetask" || $(this).attr("id")=="enddatetask"){
				str2=GetNewDate($(this).val());
				//Проверяем на валидность дату
				if(new Date(str2)=='Invalid Date'){
					err++;
					$(this).addClass("error");
				}
			}
		});
		if(err>0){
			return false;
		}
		else{
			return true;
		}
	}
	function emptyOrNo(input){
		if($.trim(input.val())==""){
			return false;
		}
		else{
			return true;
		}
	}
	function lengthInput(input,lengthin){
		if(input.length>lengthin-1){
			return false;
		}
		else {
			return true;
		}
	}

