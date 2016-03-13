function Calendar3(id, year, month) {
	//Добавление тестовых данных, необходимо удаление
	$("#day_mon").empty();
	//Добавление тестовых данных, необходимо удаление
var Dlast = new Date(year,month+1,0).getDate(),
    D = new Date(year,month,Dlast),
    DNlast = D.getDay(),
    DNfirst = new Date(D.getFullYear(),D.getMonth(),1).getDay(),
    calendar = '<tr>',
    m = document.querySelector('#month option[value="' + D.getMonth() + '"]'),
    g = document.querySelector("#year_calendar");
  day_prevmonth=new Date(year, month, 0).getDate()-DNfirst+2;
  //alert(DNfirst);
if (DNfirst != 0) {
  for(var  i = 1; i < DNfirst; i++) {calendar += "<td class='fc-widget-content fc-other-month'><div style='min-height: 85px'><div class='fc-day-number'>"+day_prevmonth+"</div><div class='fc-day-content'></div></div></td>"; day_prevmonth ++;}
}else{
  day_prevmonth=new Date(year, month, 0).getDate()-5;
  for(var  i = 0; i < 6; i++) {calendar += "<td class='fc-widget-content fc-other-month'><div style='min-height: 85px'><div class='fc-day-number'>"+day_prevmonth+"</div><div class='fc-day-content'></div></div></td>"; day_prevmonth ++;}
}
for(var  i = 1; i <= Dlast; i++) { //с первого по последний день
  td_class=''
	//Добавление тестовых данных, необходимо удаление
	 $("#day_mon").append($("<option>"+i+"</option>"));
   if (DNfirst == 0 || DNfirst % 7 == 0) {
      td_class='fc-last';
      // DNfirst=0;
    }
    DNfirst++;
	//Добавление тестовых данных, необходимо удаление
  if (i == new Date().getDate() && D.getFullYear() == new Date().getFullYear() && D.getMonth() == new Date().getMonth()) {
    calendar += "<td class='today fc-widget-content "+td_class+"' data-current="+i+"><div style='min-height: 85px'><div class='fc-day-number'>"+i+"</div><div class='fc-day-content'></div></div></td>"; //добавление сегодня
  }else{
    if (  // список официальных праздников
        (i == 1 && D.getMonth() == 0 && ((D.getFullYear() > 1897 && D.getFullYear() < 1930) || D.getFullYear() > 1947)) || // Новый год
        (i == 2 && D.getMonth() == 0 && D.getFullYear() > 1992) || // Новый год
        ((i == 3 || i == 4 || i == 5 || i == 6 || i == 8) && D.getMonth() == 0 && D.getFullYear() > 2004) || // Новый год
        (i == 7 && D.getMonth() == 0 && D.getFullYear() > 1990) || // Рождество Христово
        (i == 23 && D.getMonth() == 1 && D.getFullYear() > 2001) || // День защитника Отечества
        (i == 8 && D.getMonth() == 2 && D.getFullYear() > 1965) || // Международный женский день
        (i == 1 && D.getMonth() == 4 && D.getFullYear() > 1917) || // Праздник Весны и Труда
        (i == 9 && D.getMonth() == 4 && D.getFullYear() > 1964) || // День Победы
        (i == 12 && D.getMonth() == 5 && D.getFullYear() > 1990) || // День России (декларации о государственном суверенитете Российской Федерации ознаменовала окончательный Распад СССР)
        (i == 7 && D.getMonth() == 10 && (D.getFullYear() > 1926 && D.getFullYear() < 2005)) || // Октябрьская революция 1917 года
        (i == 8 && D.getMonth() == 10 && (D.getFullYear() > 1926 && D.getFullYear() < 1992)) || // Октябрьская революция 1917 года
        (i == 4 && D.getMonth() == 10 && D.getFullYear() > 2004) // День народного единства, который заменил Октябрьскую революцию 1917 года
       ) {
      calendar += "<td class='fc-widget-content "+td_class+" holiday' data-current="+i+"><div style='min-height: 85px'><div class='fc-day-number'>"+i+"</div><div class='fc-day-content'></div></div></td>"; //добавление выха
    }else{
      calendar += "<td class='fc-widget-content "+td_class+"' data-current="+i+"><div style='min-height: 85px'><div class='fc-day-number'>"+i+"</div><div class='fc-day-content'></div></div></td>"; //добавление будней
    }
  }
  if (new Date(D.getFullYear(),D.getMonth(),i).getDay() == 0) {
    calendar += '</tr>';
  }
}
day_flag=0;
if (DNlast != 0) {
for(var  i = DNlast; i < 7; i++){
  day_flag ++;
  calendar += "<td class='fc-other-month fc-widget-content'><div style='min-height: 85px'><div class='fc-day-number'>"+day_flag+"</div><div class='fc-day-content'></div></div></td>";
}
}
  $('#'+id).append(calendar);
g.value = D.getFullYear();
m.selected = true;
if (document.querySelectorAll('#'+id+' tr').length < 6) {
    document.querySelector('#'+id).innerHTML += '<tr class="empty"><td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;';
}
document.querySelector('#month option[value="' + new Date().getMonth() + '"]').style.color = 'rgb(220, 0, 0)'; // в выпадающем списке выделен текущий месяц
}
function get_border(){
	if($(".fc-border-separate tbody tr").eq(-1).hasClass("empty")){
		$(".fc-border-separate tbody tr").eq(-2).addClass("fc-last");
	}
	else{
		$(".fc-border-separate tbody tr").eq(-1).addClass("fc-last");	
	}
	$(".fc-last td:last").addClass("fc-last");
}
$(document).ready(function(){
	Calendar3("calendar3",new Date().getFullYear(),new Date().getMonth());
	get_border();
	$(document).on("change","#month, #year_calendar",function(){
     $('#calendar3').empty();
		Calendar3("calendar3",parseInt($("#year_calendar").val()),parseInt($("#month option:selected").val()));
		get_border();
	});
	$(document).on("click","#add_event",function(){	
		$("[data-current="+$("#day_mon option:selected").text()+"]").css("background-color","green");
	});
  $(document).on("click",".fc-button-next",function(){
  	if (parseInt($('#month').val())+1==12){ 
  		$('#month').val('0');
  		$('#year_calendar').val(parseInt($('#year_calendar').val())+1);
  	}
  	else{
    	$('#month').val(parseInt($('#month').val())+1);
  	}
    $('#calendar3').empty();
    Calendar3("calendar3",parseInt($("#year_calendar").val()),parseInt($("#month option:selected").val()));
    get_border();
  });
  $(document).on("click",".fc-button-prev",function(){ 
  	if (parseInt($('#month').val())-1==-1){
  		$('#month').val('11');
  		$('#year_calendar').val($('#year_calendar').val()-1);
  	} 
  	else {
    	$('#month').val(parseInt($('#month').val())-1);
	}
    $('#calendar3').empty();
    Calendar3("calendar3",parseInt($("#year_calendar").val()),parseInt($("#month option:selected").val()));
	  get_border();
  });
});