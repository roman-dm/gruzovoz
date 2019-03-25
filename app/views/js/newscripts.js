$(document).ready(function() {
	var domain=location.hostname;
	//Потеря фокуса списком городов
	// $(document).mouseup(function (e){
	//     var container = $(".dropdown");
	//     if(!$(".city").is(":focus")){
	// 	    if (!container.is(e.target) && container.has(e.target).length === 0) {
	// 	     	container.hide();
	// 	    }
	// 	}else{
	// 		container.show()
	// 	}
	// });

	//Назад
	$(document).on('click', ".my_back_btn", function (e){
		e.preventDefault;
		window.history.back();
	});

	//Tabs
	$(document).on('click', ".driver_tab", function (e){
		e.preventDefault();
		if(location.hash==''){
			document.location.href=location.href+"#"+$(this).attr("data");
		}else if(location.hash!=$(this).attr("data")){
			text=location.hash;
			document.location.href=location.href.replace(text,'#'+$(this).attr("data"));
		}
	});
	if(location.hash=='#orders'){
		$('.nav a[href="#orders"]').tab('show');
	}
	if(location.hash=='#subscribe'){
		$('.nav a[href="#subscription"]').tab('show');
	}
	if(location.hash=='#car'){
		$('.nav a[href="#car"]').tab('show');
	}
	if(location.hash=='#profile'){
		$('.nav a[href="#profile"]').tab('show');
	}

	//Форма обратного звонка
	$(document).on('click', '.callback', function (){
		$("#call-back").show();
		$("#call-back").val("");
		$(".callback-modal-text").html("Укажите Ваш номер телефона и мы свяжемся с Вами в ближайшее время");
		$("#callback-modal-footer").show();
		$("#callback-modal-header").find("button").show();
		$("#call-back").focus();
	});
	$(document).on('click', '#send-callback', function (){
		$.ajax({
			url: '/api/v1/callback/',
			type: 'POST',
			data: {
				phone: $("#call-back").val(),
			}
		}).done(function(data){
			result=data;
			if (result.result){
				$(".callback-modal-text").html("Спасибо, ожидайте звонка!");
				$("#call-back").hide();
				$("#callback-modal-footer").hide();
				$("#callback-modal-header").find("button").hide();
				setTimeout(function () {
					$('#get_call').modal('toggle');
				}, 2000);
			}else{
				$("#callback-modal-body").html("Произошла ошибка, повторите попытку позднее");
				$("#callback-modal-footer").html("<button type='button' class='btn btn-default' data-dismiss='modal'>Закрыть</button>");
			}
			
		});
	});
	$(document).on('change, keyup', '#call-back', function (){
		if(is7Phone($(this).val())){
			$("#send-callback").show("slow");
		}else{
			$("#send-callback").hide("slow");
		}
	});
	$("#call-back").mask("+7 (999) 999-99-99");

	//Регулярные проверки + функции проверок
	function junkDelete(param){
		if (param.length==0){
			return ''
		}
		return "+7"+param.substring(2).replace(/[^0-9]/g,'')
	}
	function isnumber(param) {
		return /^[ 0-9]/.test(param);
	}
	function isAlphaOrParen(str) {
	  	return /^[a-zA-Z()]+$/.test(str);
	}
	function isMail(str){
		return /^[-\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$/.test(str);
	}
	function isPhone(str){
		return /^\d{10}$/.test(str);
	}
	function is7Phone(param){
		return /^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/.test(param);
	}
	function is5number(param){
		return /^\d{5}$/.test(param);
	}
	function checkPhone(phone){
		if (phone[0]=='8'){
			return '+7'+phone.substring(1);
		}
		if (phone[0]=='9'){
			return '+7'+phone
		}
		return phone
	}

	//Запрос не получение списка водителей
	$(document).on('click', '#more-drivers', function (e){
		e.preventDefault();
		$.ajax({
			url: '/api/v1/get_more_drivers/',
			type: 'POST',
			data: {
				offset: $('.driver-div').length,
				limit: 3,
			},
			// beforeSend: function() {
			// 	$("#driver-preloader").show();
			// },
			// complete: function() {
			// 	$("#driver-preloader").hide();
			// }
		}).done(function(data){
			result=data;
			if(result.result){
				for (var i = 0; i < result.list_driver.length; i++){
					if(result.list_driver[i].loaders){
						loaders='есть';
					}else{
						loaders='нет';
					}
					if(result.list_driver[i].car_type=='single'){
						car_type='Одиночка';
					}else if(result.list_driver[i].car_type=='semitrailer'){
						car_type='Полуприцеп';
					}else{
						car_type='Одиночка+прицеп'
					}
					temp=''
					if(result.list_driver[i].load_type_top){
						temp=temp+("<i title='Верхняя' class='fa fa-arrow-up' aria-hidden='true'></i>");
					}else if(result.list_driver[i].load_type_rear){
						temp=temp+("<i title='Задняя' class='fa fa-arrow-down' aria-hidden='true'></i>");
					}else if(result.list_driver[i].load_type_side){
						temp=temp+("<i title='Боковая' class='fa fa-arrow-right' aria-hidden='true'></i>");
					}
					if(result.list_driver[i].avatar==''){
						avatar='/app/views/images/noavatar.png'
					}else{
						avatar=result.list_driver[i].avatar
					}
					$("#main-driver-div").append("<div class='row flow-offset-1 product-info driver-div'><div class='col-lg-4 text-center text-md-left wow fadeInLeft'><img src='"+avatar+"' width='370' height='274' alt=''></div><div class='col-lg-8'><div class='product-info-body text-center text-md-left'><h3><a href='/get_one_driver/"+result.list_driver[i].id+"/'>"+result.list_driver[i].name+"</a></h3><p class='heading-4 product-info-price'><a href='#'>Автомобиль: "+result.list_driver[i].car_name+"</a></p><p>Рейтинг: "+result.list_driver[i].rating+"</p><div class='row row-sm-justify'><div class='col-sm-4 col-lg-3 text-sm-left'><h6 class='text-capitalize'>Стоимость</h6><ul class='product-info-list text-sm-left '><li>По городу: "+result.list_driver[i].rate+" руб/ч</li></ul></div><div class='col-sm-4 col-lg-5 text-sm-left'><h6 class='text-capitalize preffix-1'>Параметры автомобиля</h6><ul class='product-info-list text-sm-left preffix-1'><li>Грузоподъемность: "+result.list_driver[i].capacity+" тонн</li><li>Способ загрузки: "+temp+"</li><li>Тип: "+car_type+"</li><li>Грузчики: "+loaders+"</li></ul></div><div class='col-sm-4 col-md-3'><div class='inline-block'><ul class='product-info-list-1'><li><div class='box'><div class='box__left box__middle'><span class='icon icon-xs icon-default fa-thumbs-o-up'></span></div><div class='box__body box__middle'>Всего заказов: 20</div></div></li></ul><a href='#' class='btn btn-md btn-sunglow wow fadeInRight' id='"+result.list_driver[i].id+"'>Подробнее</a></div></div></div></div></div></div><hr>");
				}	
			}else{

			}
		});
	});

	//Запрос на получение данных для закрытия заказа
	$(document).on('click', '#close_order', function (e){
		page=$(this).attr('data-page');
		e.preventDefault();
		if(page=='customer'){
			$.ajax({
				url: '/api/v1/get_profile_driver/',
				type: 'POST',
				data: {
					id_driver: $(this).attr("data-driver"),
				}
			}).done(function(data){
				result=data;
				if(result.profile.name==null){
					name='<br/>'
				}else{
					name="<br/>"+result.profile.name+"<br/>";
				}
				if(result.profile.avatar==''){
					avatar="/app/views/images/noavatar.png"
				}else{
					avatar=result.profile.avatar
				}
				$("#body-text").html("<div class='avatar'><div class='col-lg-12 text-center'><div class='capt_close'>Водитель"+name+"выполнил Ваш заказ</div><div><img src="+avatar+" width='306px' height='226px'></div>");

			});
		}else if(page=='driver'){
			$.ajax({
				url: '/api/v1/get_profile_customer/',
				type: 'POST',
				data: {
					id_customer: $(this).attr("data-customer"),
				}
			}).done(function(data){
				result=data;
				if(result.profile.name==null){
					name='<br/>'
				}else{
					name="<br/>"+result.profile.name+"<br/>";
				}
				if(result.profile.avatar==''){
					avatar="/app/views/images/noavatar.png"
				}else{
					avatar=result.profile.avatar
				}
				$("#body-text").html("<div class='avatar'><div class='col-lg-12 text-center'><div class='capt_close'>Заказчик"+name+"закрыл этот заказ</div><div><img src="+avatar+" width='306px' height='226px'></div>");
			});
		}
	});

	//Закрытие заказа
	$(document).on('click', '#close_order_btn_driv', function (e){
		$.ajax({
			url:'/api/v1/rating_order/',
			type: 'POST',
			data: {
				id_order: $(".container").find("h2").attr("id-order"),
				rating: $(".vote-success").html().substr(-1),
			}
		}).done(function(data){


		});
	});
			
	$(document).on('click', '#close_order_btn_cus', function (e){
		$.ajax({
			url: '/api/v1/close_order/',
			
			type: 'POST',
			data: {
				id_order: $(".container").find("h2").attr("id-order"),
				rating: $(".vote-success").html().substr(-1),
			}
		}).done(function(data){
			// result=data;

		});
	});
	
	//фильтр в подписках
	$(document).on('click', '.go-filter', function (e){
		e.preventDefault();

		$.ajax({
			url: '/api/v1/get_more_orders/',
			type: 'GET',
			data: {
				offset: 0,
				limit: 9,
			}
		}).done(function(data){

		});
		location.href(domain+"/get_orders/")
	});
		
	//Запрос не получение списка заказов
	$(document).on('click', '#more-orders', function (e){
		e.preventDefault();
		if($('.customer-div').length>9){
			location.href="/get_orders/";
		}else{
			$.ajax({
				url: '/api/v1/get_more_orders/',
				type: 'POST',
				data: {
					offset: $('.customer-div').length,
					limit: 3,
				}
			}).done(function(data){
				result=data;
				console.log(data);
				if(result.result){
					for (var i = 0; i < result.list_order.length; i++){
						temp=''
						if(result.list_order[i].comment.length>75){
							temp="...";	
						}else{
							temp=result.list_order[i].comment
						}
						if(result.list_order[i].finish_city.id == ''){
							city='По городу';
						}else{
							city=result.list_order[i].finish_city.name;
						}
						if(result.list_order[i].price==0 || result.list_order[i].price==''){
							price='По договоренности';
						}else{
							price=result.list_order[i].price;
						}
						$("#main-customer-div").append("<div class='col-sm-6 col-lg-4 wow fadeIn customer-div'><div class='order-1'><a href='http://"+domain+"/detail_order/"+result.list_order[i].id+"' target='blank' class='order-price-1 heading-5'>"+price+"</a><div style='clear:both;'></div><a href='http://"+domain+"/detail_order/"+result.list_order[i].id+"' target='blank' class='order-title-1 heading-4'>"+result.list_order[i].start_city.name+"<br/>"+city+"</a><p class='order-caption-1'><span>"+result.list_order[i].cargo_name+"</span><br/>"+temp+"</p><div class='incenter'><a href='http://"+domain+"/detail_order/"+result.list_order[i].id+"' target='blank' class='btn btn-md btn-sunglow wow fadeInRight marbot' id='"+result.list_order[i].id+"'>Подробнее</a></div></div></div>");
					}	
				}else{

				}	
			});	
		}
	});

	//Удаление подписки
	$(document).on('click', '.delete_subscribe', function (){
		$("#ask").modal();
		$("#btn-delete-sub").attr("data-id",$(this).attr("data-id"))
		$("#btn-delete-sub").attr("data-loop",$(this).attr("data-loop"));
		document.location.href=location.href+"#subscribe";
	});
	$(document).on('click', '#btn-delete-sub', function (){
		loop=$(this).attr("data-loop");
		$.ajax({
			url: '/api/v1/delete_subscribe/',
			type: 'POST',
			data: {
				id: $(this).attr("data-id"),
			}
		}).done(function(data){
			result=data;
			if(result.result){
				if(result.count_subscribe<3){
					$("#subscription-tab-new_sub").css("display","block")
				}
				$("#sub-mega-div-"+loop).remove();
				if(location.hash!='#subscribe'){
					document.location.href=location.href+"#subscribe";
				}
				location.reload();
			}else{

			}	
		});
	});
	
	//Редактирование подписки
	$(document).on('click', '.save_sub', function (e){
		e.preventDefault();
		cur_num=$(this).attr("data-loop-id");
		if(ValidationSub(cur_num)){
			payment_type='';
			if($("#subscription-checkbox-fixprice-"+cur_num).is(":checked")){
				payment_type='fixed';
			}
			if($("#subscription-checkbox-allprice-"+cur_num).is(":checked")){
				payment_type='open';
			}
			if($("#subscription-checkbox-fixprice-"+cur_num).is(":checked") && $("#subscription-checkbox-allprice-"+cur_num).is(":checked")){
				payment_type='both';
			}
			payment_method='';
			if($("#subscription-checkbox-cash_pay-"+cur_num).is(":checked")){
				payment_method='cash';
			}
			if($("#subscription-checkbox-non_cash_pay-"+cur_num).is(":checked")){
				payment_method='non_cash';
			}
			if($("#subscription-checkbox-cash_pay-"+cur_num).is(":checked") && $("#subscription-checkbox-non_cash_pay-"+cur_num).is(":checked")){
				payment_method='both';
			}
			if($("#subscription-checkbox-incity-"+cur_num).is(":checked")){
				order_type='in_city'
			}
			if($("#subscription-checkbox-intercity-"+cur_num).is(":checked")){
				order_type='intercity'
			}
			if($("#subscription-checkbox-incity-"+cur_num).is(":checked") && $("#subscription-checkbox-intercity-"+cur_num).is(":checked")){
				order_type='both';
			}
			finish='';
			start='';
			if (!$("#subscription-checkbox-open_date-"+cur_num).is(":checked")){
				finish=$("#subscription-enddatetask-"+cur_num).val();
				start=$("#subscription-startdatetask-"+cur_num).val();
			}
			$.ajax({
				url: '/api/v1/edit_subscribe/',
				type: 'POST',
				data: {
					id: $(this).attr("sub-id"),
					payment_method: payment_method,
					payment_type: payment_type,
					start_id: $("#subscription-city-ot-"+cur_num).attr("city-id"),
					finish_id: $("#subscription-city-to-"+cur_num).attr("city-id"),
					order_type: order_type,
					start_date: start,
					finish_date: finish,
					min_killo: $("#subscription-cargo_weight-ot-"+cur_num).val(),
					max_killo: $("#subscription-cargo_weight-to-"+cur_num).val(),
					// active: ,
				}
			}).done(function(data){
				result=data;
				console.log(data);
				if(result.result){
					$("#add_subscribe_done").modal("show");
					$("#add_subscribe_done_body").find("p").html('Ваша подписка успешно сохранена.<br/>Теперь все заказы будут фильтроваться с учетом этой подписки');
				}else{

				}	
			});
		}else{
			var id  = $(this).parent().attr('href');
            var top = $(id).offset().top;
        	$('body,html').animate({scrollTop: top}, 1500);
		}
	});

	//проверка веса
	$(document).on('change, keyup', '#cargo_weight', function (){
		if(!isnumber($('#cargo_weight').val())){
			$('#cargo_weight').val('');
		}

	});
	//Кнопка отправки кода при регистрации
	$(document).on('change, keyup', '#phone', function (){
		if (is7Phone($('#phone').val())){
			$('#div-reg-send').show("slow");
		}else{
			$('#div-reg-send').hide("slow");
		}
	});

	//Значок теолефона или почты при авторизации
	$(document).on('change, keyup', '#login-inpt', function (){
		text=$(this).val();
		flag=0;
		n=0;
		if(text[0]=='8'){
			n=1
			text=text.substring(1);
		}
		if(text[0]=='+' && text[1]=='7'){
			n=2
			text=text.substring(2);
		}
		if(isPhone(text)){
			flag=1;
			$('.fa-envelope-o').hide();
			$('.fa-mobile').show();			
			$(".login-mark").css("color","white");
			$(".mark-place").css("background-color","#8bc745");	
			$("#div-code-auth").show("slow");
		// }else if(isMail(text)){
		// 	flag=1;
		// 	$(".login-mark").css("color","white");
		// 	$(".mark-place").css("background-color","#8bc745");
		// 	$("#div-pass").show("slow");
		// 	$('#enter').show("slow");
		}else{
			flag=0;
			$(".login-mark").css("color","#555");
			$(".mark-place").css("background-color","#eee");
			// $("#div-pass").hide("slow");
			$("#div-code-auth").hide("slow");
			$('#enter').hide("slow");
		}
		if(text.length!=10 && text.length<12){
			flag=0;
			$(".login-mark").css("color","#555");
			$(".mark-place").css("background-color","#eee");
			// $("#div-pass").hide("slow");
			$('#enter').hide("slow");
		}
		if(text.length>10 && isnumber(text)){
			$('.fa-envelope-o').show();
			$('.fa-mobile').hide();				
		}
		if (text==''){
			$('.fa-envelope-o').hide();
			$('.fa-mobile').hide();				
		}
		// if (flag==0){
		// 	for (var i = n; i < $(this).val().length; i++){
		// 		if(text[i]=='@'){
		// 			$('#sobaka').val(1)			
		// 		}else if (isnumber(text[i]) || $(this).val()[0]=='+'){
		// 			$('#numeric').val(1)
		// 		}else if (isAlphaOrParen(text[i])){
		// 			$('#alpha').val(1)
		// 		}else{
		// 			$('#sobaka').val(0)
		// 		}
		// 	}
		// 	if($('#alpha').val()=='1'){
		// 		$('.fa-envelope-o').show();
		// 		$('.fa-mobile').hide();
		// 	}else if($('#sobaka').val()=='1'){
		// 		$('.fa-envelope-o').show();
		// 		$('.fa-mobile').hide();			
		// 	}else if($('#numeric').val()=='1' && $('#alpha').val()=='0'){
		// 		$('.fa-envelope-o').hide();
		// 		$('.fa-mobile').show();						
		// 	}else{
		// 		$('.fa-envelope-o').hide();
		// 		$('.fa-mobile').hide();				
		// 	}
		// 	$('#alpha').val(0);
		// 	$('#sobaka').val(0);
		// 	$('#numeric').val(0);
		// }
	});

	//Выбор водитель/заказчик при регистрации
	$(document).on('click', '#voditel', function (){
		$(this).addClass("iamchecked");
		$("#zakazchik").removeClass("iamchecked");
		$('#phone-reg-send').attr("name","driver");
	});
	$(document).on('click', '#zakazchik', function (){
		$(this).addClass("iamchecked");
		$("#voditel").removeClass("iamchecked");
		$('#phone-reg-send').attr("name","customer");
	});

	//Отправка номера телефона при регистрации
	$(document).on('click', '#phone-reg-send', function (){
		$("#inpt-for-phone").val($("#phone").val());
		$.ajax({
			url: '/api/v1/auth/registration/',
			type: 'POST',
			data: {
				phone: $('#phone').val(),
				user_type: $(this).attr("name"),
			}
		}).done(function(data){
			result=data;
			if (result.result){
				$(".yellow-border").hide();
				$("#div-reg-send").hide();
				$(".phone-reg-inpt").html("<div class='col-md-12'><h6 class='text-none text-regular'>Введите код</h6><label data-add-placeholder><input type='text' id='reg-code' name='' autocomplete='off' placeholder='12345' data-constraints=''/></label></div>");
			}else{
				if (result.error==1){
					$(".yellow-border").hide();
					$("#div-reg-send").hide();
					$(".red-error-reg").show();
				}
			}
		});
	});
	//Отправка номера телефона при авторизации
	$(document).on('click', '#phone-auth-send-next', function (){
		phone=$('#inpt-for-phone-enter').val();
		$("#timer").html("");
		$.ajax({
			url: '/api/v1/auth/authorization_site/',
			type: 'POST',
			data: {
				phone: phone,
			}
		}).done(function(data){
			result=data;
			if (result.result){
				$("#div-code-auth").hide();
				$(".phone-auth-inpt").html("<div class='col-md-12'><h6 class='text-none text-regular'>Введите код</h6><label data-add-placeholder><input type='text' id='auth-code' name='' autocomplete='off' placeholder='_____' data-constraints=''/></label><div class='martop'><div class='inline-block code-repeat-text'>Повторно выслать код через:&nbsp</div><div class='inline-block' id='timer'>2:00</div></div></div>");
				var ts = 120;//600
				newstime(ts);
				$("#auth-code").focus();
				setTimeout(function () {
					$("#timer").hide();
					$("#div-code-auth").html("<div class='col-md-12' style='text-align: center;'><a class='btn btn-xl btn-sunglow' href='#' id='phone-auth-send-next'>Выслать код</a></div>");
					$("#div-code-auth").show();
					$('.code-repeat-text').hide();
				}, 120000); // время в мс
			}else{
				if (result.error==1){
					$("#div-auth-send").hide();
					$("#div-code-auth").show();
				}				
			}
		});
	});
	$(document).on('click', '#phone-auth-send', function (){
		phone='';
		$("#timer").html("");
		phone=checkPhone($('#login-inpt').val());
		$("#inpt-for-phone-enter").val(junkDelete(phone));
		$.ajax({
			url: '/api/v1/auth/authorization_site/',
			type: 'POST',
			dataType: "JSON",
			data: {
				phone: phone,
			}
		}).done(function(data){
			result=data;
			if (result.result){
				$("#div-code-auth").hide();
				$(".phone-auth-inpt").html("<div class='col-md-12'><h6 class='text-none text-regular'>Введите код</h6><label data-add-placeholder><input type='text' id='auth-code' name='' autocomplete='off' placeholder='_____' data-constraints=''/></label><div class='martop'><div class='inline-block code-repeat-text'>Повторно выслать код через:&nbsp</div><div class='inline-block' id='timer'>2:00</div></div></div>");
				var ts = 120;
				newstime(ts);
				$("#auth-code").focus();
				setTimeout(function () {
					$("#timer").hide();
					$("#div-code-auth").html("<div class='col-md-12' style='text-align: center;'><a class='btn btn-xl btn-sunglow' href='#' id='phone-auth-send-next'>Выслать код</a></div>");
					$("#div-code-auth").show();
					$('.code-repeat-text').hide();
				}, 120000);
			}else{
				if (result.error==1){
					$(".login-mark").css("color","555");
					$(".mark-place").css("background-color","red");
					$(".red-error-auth").show();
					$("#phone-auth-send").hide();
					$("#div-auth-send").hide();
					$("#div-code-auth").show();
				}				
			}
		});
	});

	//Ввод кода из смс при авторизации
	$(document).on('change, keyup', '#auth-code', function (){
		$(".red-error-auth").hide("slow");
		if(is5number($('#auth-code').val())){
			$("#div-code-auth").html("<div class='col-lg-12 text-center'><a class='btn btn-xl btn-sunglow' id='code-auth-send' name='' href='#'>Войти</a></div>")
			$("#div-code-auth").show("slow");
		}else{
			$("#div-code-auth").hide("slow");
		}
	});

	//Ввод кода из смс при регистрации
	$(document).on('change, keyup', '#reg-code', function (){
		if(is5number($('#reg-code').val())){
			$("#div-reg-send").html("<div class='col-lg-12'><a class='btn btn-xl btn-sunglow' id='code-reg-send' name='' href='#'>Зарегистрироваться</a></div>")
			$("#div-reg-send").show("slow");
		}else{
			$("#div-reg-send").hide("slow");
		}
	});

	//При регистрации ввели номер, который уже зареган
	$(document).on('click', '#enter-link-reg', function (){
		temp=$("#phone").val();
		$("#lk-block").click();
		$(".red-error-auth").hide();
		$("#login-inpt").val(junkDelete(temp));
		if(is7Phone($("#login-inpt").val())){
			$('#div-code-auth').show("slow");
			$('.fa-envelope-o').hide();
			$('.fa-mobile').show();
			$(".login-mark").css("color","white");
			$(".mark-place").css("background-color","#8bc745");				
		}else{
			$('#div-code-auth').hide("slow");
		}
	});
	$(document).on('click', '#enter-link-auth', function (){
		$("#registration-block").click();
		$(".red-error-reg").hide();
	});


	//Запрос на авторизацию
	$(document).on('click', '#code-auth-send', function (){
		$.ajax({
			url: '/api/v1/auth/check_code_enter/',
			type: 'POST',
			data: {
				code: $('#auth-code').val(),
				phone: junkDelete($("#inpt-for-phone-enter").val()),
			}
		}).done(function(data){
			result=data;
			if (result.result){
				location.href="/"+result.link+"/";
			}else{
				$(".red-error-auth").html("Не верный код. Выслать еще раз.");
				$(".red-error-auth").show("slow");
			}
			
		});		
	});

	//Запрос на регистрацию, отправка кода из смс
	$(document).on('click', '#code-reg-send', function (){
		$.ajax({
			url: '/api/v1/auth/check_code_site/',
			type: 'POST',
			data: {
				code: $('#reg-code').val(),
				phone: junkDelete($("#inpt-for-phone").val()),
			}
		}).done(function(data){
			result=data;
			if (result.result){
				location.href="/"+result.link+"/";
			}else{
				
			}
			
		});		
	});

	//Проверка на ввод только цифр в поле кода из смс
	$(document).on('keyup', '#reg-code', function (){
		if(!isnumber($(this).val())){
			$(this).val('');
		}
	});

	//Вывод списка городов в поля гордов
	$(document).on('change, keyup', '.city', function (){
		temp_inp=$(this);
		temp=$(this).attr('id');
		temp_sibl=$(this).siblings("ul").attr("id");
		if($(this).val().length==0){
			$("#"+temp_sibl).css({
				"opacity":"0",
				"visibility":"hidden"
			});
		}
		if($(this).val().length>2){
			$("#"+temp_sibl).html("");
			$("#"+temp_sibl).css({
				"opacity":"0",
				"visibility":"hidden"
			});
			if(!$(this).prop("readonly")){
				$.ajax({
					url: '/api/v1/geocoding/search/',
					type: 'GET',
					data: {
						query: $(this).val(),
						country_id: 1,
						site: "Y",
					},
					beforeSend: function() {
	        			$('[data-id-loader='+temp+']').show();
	    			},
	    			complete: function() {
	        			$('[data-id-loader='+temp+']').hide();
	    			}
				}).done(function(data){
					result=data;
					console.log(data)
					if(result.count_s==temp_inp.val().length){
						$("#"+temp_sibl).css({
							"opacity":"1",
							"visibility":"visible",
							"margin-top":"0"
						});
						if(result.total<=0){
							$("#"+temp_sibl).html("");
							$("#"+temp_sibl).append("<div class='no-result'>К сожалению, по Вашему городу ничего не нашлось</div>");
						}else{
							for (var i = 0; i < result.data.geo_objects.length; i++){
								parent=''
								if(result.data.geo_objects[i].region=="true"){
									data_temp=" data-country-name=' "+result.data.geo_objects[i].country_name+"' data-parent-name=' "+$.trim(result.data.geo_objects[i].parent_name)+"'"
									parent="<div class='sub-caption-li'>"+$.trim(result.data.geo_objects[i].parent_name)+", "+result.data.geo_objects[i].country_name+"</div>"
								}else{
									data_temp=" data-country-name=' "+result.data.geo_objects[i].country_name+"' data-parent-name=''"
									parent="<div class='sub-caption-li'>"+result.data.geo_objects[i].country_name+"</div>"
								}
								$("#"+temp_sibl).append("<li class='option city-item' data-name='"+result.data.geo_objects[i].name+"' data-id="+result.data.geo_objects[i].id+" data-parent-id="+temp_sibl+" data-parent-temp="+temp+data_temp+"'>"+result.data.geo_objects[i].name+parent+"</li>");
							}
						}
					}
				});
			}
		}
	});

	//Возвращение модалки аватара в исходную
	$(document).on('click', '#change_avatar_driver','#change_avatar_customer', function (){
		if($(".avatar").find("img").attr("src")=="/app/views/images/noavatar.png"){

			$("#delete-old-avatar").hide();
		}else{
			$("#delete-old-avatar").show();
		}
		$("#drop").css("display","block")
        $("#drop2").css("display","none");
        $("#drop").siblings().find('p').html('');
        $("#close-or-delete").html("Закрыть");
        $("#save_avatar").hide();
	});

	//Удаление аватара
	$(document).on('click', '#delete-old-avatar', function (){
		dc=$(this).attr("data-name");
		$.ajax({
			url: '/api/v1/delete_avatar_'+dc+'/',
			type: 'POST',
			data:{
				avatar: ""
			}
		}).done(function(data){
			result=data;
			$(".avatar").find("img").attr("src","/app/views/images/noavatar.png");
		});		
	});

	//Не сохранения аватара после загрузки новой фотки
	$(document).on('click', '.delete_avatar', function (){
		dc=$(this).attr("data-name");
		$.ajax({
			url: '/api/v1/delete_avatar_'+dc+'/',
			type: 'POST',
			data:{
				avatar: $(".avatar").find("img").attr("src")
			}
		}).done(function(data){
			result=data;
			$(".mbd").removeClass("delete_avatar");
		});		
	});

	//Сохранение аватара
	$(document).on('click', '#save_avatar', function (){
		$(".avatar").find("img").attr("src",$("#img-for-avatar").attr("src"));
	});

	//Запрет на изменение инпута города
	$(".city").each(function(){
		if ($(this).val()==''){
			$(this).prop('readonly', false);
		}else{
			$(this).prop('readonly', true);
		}
	});
	
	//Вставка выбранного города из списка в главный инпут города
	$(document).on('click','.city-item', function(){

		// $('#'+$(this).attr("data-parent-temp")).attr("data-value",($(this).attr("data-name")+$(this).attr("data-parent-name")+$(this).attr("data-country-name")));
		$('#'+$(this).attr("data-parent-temp")).val($(this).attr("data-name")+$(this).attr("data-parent-name")+$(this).attr("data-country-name")).attr("value",$(this).attr("data-name")+$(this).attr("data-parent-name")+$(this).attr("data-country-name"));
		$('#'+$(this).attr("data-parent-id")).html("");
		$('#'+$(this).attr("data-parent-id")).css({
			"opacity":"0",
			"visibility":"hidden",
		});
		$('#'+$(this).attr("data-parent-temp")).attr("city-id",$(this).attr("data-id"));
		$('#'+$(this).attr("data-parent-temp")).prop('readonly', true);
		$('#'+$(this).attr("data-parent-temp")).css("border","1px solid #e3e3e3"); 
		$('#'+$(this).attr("data-parent-temp")).parents().find(".err").css("display","none");

	});
	// проверка чекбоксов и радио
	$(document).on('click','.int_search', function(){
		this_radio=$(this);
		$(this).closest(".martop").find("[type=radio]").each(function(){
			$(this).removeAttr("checked");
		})

		if(this_radio.find("input").attr("type")=="checkbox"){
			if(this_radio.find("input[type=checkbox]").is(':checked')){
				this_radio.find("input[type=checkbox]").removeAttr("checked");
			}else{
				this_radio.find("input[type=checkbox]").attr("checked","checked");
			}
		}
		
		if($("#tonna-driv").is(':checked')){
			$("#ton-driv-div").hide();
			$("#tonna-driv").siblings("label").text("Включить");
		}else{
			$("#ton-driv-div").show();
			$("#tonna-driv").siblings("label").text("Отключить");
		}
		if($("#tonna-cus").is(':checked')){
			$("#ton-cus-div").hide("slow");
			$("#tonna-cus").siblings("label").text("Включить");
		}else{
			$("#ton-cus-div").show("slow");
			$("#tonna-cus").siblings("label").text("Отключить");
		}
		if($("#r-customer-2").is(':checked')){
			$("#city-search-to-customer").show("slow");
		}else{
			$("#city-search-to-customer").hide("slow");
		}
		if($("#zakaz-radio-5").is(':checked')){
			$("#price-block").show("slow");
			$("#price").removeAttr("disabled");
			$("#price").focus();
		}else{
			$("#price-block").hide("slow");
		}
		if($("#detail-order-city-to").is(':checked')){
			$(".edit-row-to").show("slow");
		}else{
			$(".edit-row-to").hide("slow");
			$("#detail-edit-zakaz-to").val("");
			$('#detail-edit-zakaz-to').prop('readonly', false);
			$('#detail-edit-zakaz-to').attr("city-id",'');
			$('#detail-edit-zakaz-to').focus();
		}
		if($("#zakaz-checkbox-1").is(':checked')){
			$(".row-to").show("slow");
		}else{
			$(".row-to").hide("slow");
			$("#new-zakaz-to").val("");
			$('#new-zakaz-to').prop('readonly', false);
			$("#edit-zakaz-to").val("");
			$("#edit-zakaz-to").attr("city-id","0");
		}
		if ($("#radio-3").is(':checked')){
			$('#city-zakaz-to').hide("slow");
		}
		if($("#radio-4").is(':checked')){
			$('#city-zakaz-to').show("slow");
		}
		if($("#radio-5").is(':checked')){
			$("#price").focus();
			$("#price").removeAttr("disabled");
		}
		if($("#radio-5").is(':checked')){
			$(".int-price").show("slow");
			$("#price").val("")
		}
		if($("#zakaz-radio-5").is(':checked')){
			$("#radio-6").removeAttr("checked");
		}
		if($("#radio-6").is(':checked')){
			$(".int-price").hide("slow");
			$("#zakaz-radio-5").removeAttr("checked");
			$("#err_price").css("display","none");
			$("#price").css("border","none");
			$("#price").val("");

		}
		if($("#zakaz-checkbox-2").is(':checked')){
			$(".zakaz-dates").hide("slow");
		}else{
			$(".zakaz-dates").show("slow");
			if($("#oc-date").val()=='open'){
				$("#startdatetask").val('');
				$("#enddatetask").val('');
			}
		}
		if($("#checkbox-10").is(':checked')){
			$("#city-price").show("slow");
			$("#loaders").show("slow");
		}else{
			$("#city-price").hide("slow");
			$("#loaders").hide("slow");
		}
		if($("#checkbox-11").is(':checked')){
			$("#ctc-price").show("slow");
		}else{
			$("#ctc-price").hide("slow");
		}
		this_radio.find("[type=radio]").attr("checked","checked");

		//Проверка чекбоксов
		loop=this_radio.attr("data-loop");
		if($("#subscription-checkbox-fixprice-"+loop).is(':checked')){
			$("#price-block-subscription-"+loop).show("slow");
			$("#price-subscription-"+loop).focus();
		}else{
			$("#price-block-subscription-"+loop).hide("slow");
		}
		if(!$("#subscription-checkbox-open_date-"+loop).is(':checked')){
			$("#subscription-dates-"+loop).show("slow");
			$("#subscription-startdatetask-"+loop).val('');
			$("#subscription-enddatetask-"+loop).val('');
		}else{
			$("#subscription-dates-"+loop).hide("slow");	
		}
		if($("#subscription-checkbox-intercity-"+loop).is(':checked')){
			$("#subscription-to-div-"+loop).show("slow");
		}else{
			$("#subscription-to-div-"+loop).hide("slow");
			$("#subscription-city-to-"+loop).val("");
			$('#subscription-city-to-'+loop).prop('readonly', false);
		}
		// if($("#subscription-checkbox-intercity-n").is(":checked")){
		// 	$(".row-sub-to").show("slow");
		// }else{
		// 	$(".row-sub-to").hide("slow");
		// }
		// if($("#subscription-checkbox-fixprice-n").prop("checked")){alert('yes');}
		label=$(this);
		if(label.find(".subscription-active-checkbox").is(':checked')){
			label.find("[for^=subscription-checkbox-active]").html("Подписка активна");
			label.parent().siblings(".accordion-item").find("h3").css("color","#d24b4b")
		}
		if(!label.find(".subscription-active-checkbox").is(':checked')){
			label.find("[for^=subscription-checkbox-active]").html("Подписка не активна");
			label.parent().siblings(".accordion-item").find("h3").css("color","#CDD1DA")
		}

		//Отправка активности подписки
		if(this_radio.attr("id")=="subscription-div-active-"+this_radio.attr("data-loop-id")){
			cur_num=this_radio.attr("data-loop-id");
			active=false
			if($("#subscription-active-checkbox-"+cur_num).is(":checked")){
				active=true
			}
			$.ajax({
				url: '/api/v1/edit_subscribe/',
				type: 'POST',
				data: {
					id: this_radio.attr("data-id"),
					active: active,
				}
			}).done(function(data){
				result=data;
				console.log(data);
				if(result.result){
						
				}else{

				}	
			});				
		}
		
		
	});

	//Возврат в исходное положение табов лк и реги
	$(document).on('click','#registration-block', function(){
		$(".yellow-border").show();
		$(".red-error-reg").hide();
		$("#div-reg-send").html("<div class='col-lg-12'><a class='btn btn-xl btn-sunglow' id='phone-reg-send' name='driver' href='#'>Выслать код</a></div>");
		$("#div-reg-send").hide();
		$(".phone-reg-inpt").html("<div class='col-md-12'><h6 class='text-none text-regular'>Введите номер телефона</h6><label data-add-placeholder><input type='text' name='name' id='phone' placeholder='+7 (___) ___-__-__' autocomplete='off' data-constraints=''/></label></div><script type='text/javascript'>jQuery(function($){$('#phone').mask('+7 (999) 999-99-99');});</script>");
	});
	$(document).on('click','#lk-block', function(){
		$(".red-error-auth").hide();
		$("#login-inpt").val("");
		$('#inpt-for-phone-enter').val();
		$('.phone-auth-inpt').html("<div class='col-md-12'><h6 class='text-none text-regular'>Ваш телефон</h6><label data-add-placeholder><input type='hidden' value='0' id='sobaka'><input type='hidden' value='0' id='numeric'><input type='hidden' value='0' id='alpha'><input type='text' id='login-inpt' name='name' autocomplete='off' placeholder='+7__________'  data-constraints=''/><div class='mark-place'><i class='fa fa-envelope-o login-mark' aria-hidden='true' style='display: none;'></i><i class='fa fa-mobile login-mark' aria-hidden='true' style='display: none;'></i></div></label></div>");
	});


	//селект
	function ToggleSelect(item){
		$("#"+item+"-dropdown").css({
			"opacity":"1",
			"visibility":"visible",			
		})
	};
	function SaveItemsSelect(item,parent,name,data_name,data_value){
		$("#"+parent).html($("#"+item).attr("data-name"));
		$("#"+parent).attr("data",name);
		$("#"+parent).attr("data-value",data_value);
		$("#"+parent).attr("data-name",data_name);
		$("#"+parent).css({
			"color":"#000",
		})
		$("#"+parent+"-dropdown").css({
			"opacity":"0",
			"visibility":"hidden",			
		})		
	};

	//чекбоксы профилей
	if($('#int-for-spec').val()=='in_city'){
		$("#checkbox-10").attr("checked","checked");
		$("#checkbox-10").addClass("ch_new");
		$('#city-price').show();
		$('#loaders').show();
		if($("#checkbox-12").attr("data")=="true"){
			$("#checkbox-12").attr("checked","checked");
			$("#checkbox-12").addClass("ch_new");
		}
	}else if($('#int-for-spec').val()=='intercity'){
		$("#checkbox-11").attr("checked","checked");
		$("#checkbox-11").addClass("ch_new");
		$('#ctc-price').show();
	}else{
		$("#checkbox-10").attr("checked","checked");
		$("#checkbox-10").addClass("ch_new");
		$('#city-price').show();
		$("#checkbox-11").attr("checked","checked");
		$("#checkbox-11").addClass("ch_new");
		$('#ctc-price').show();
		$('#loaders').show();
		if($("#checkbox-12").attr("data")=="true"){
			$("#checkbox-12").attr("checked","checked");
			$("#checkbox-12").addClass("ch_new");
		}
	}
	//может тут??
	$(".ch-type").each(function(){
		if($(this).attr("data-type")=='true'){
			$(this).attr("checked","checked");
			$(this).addClass("ch_new");
		}
	});
	if($("#typeavto").attr("data-type")=='single'){
		$(this).attr("data","Одиночка");
		$(this).attr("data-name","single");
		$("#typeavto").html("Одиночка");
		$("#typeavto").css("color","#000");
	}else if($("#typeavto").attr("data-type")=='semitrailer'){
		$(this).attr("data","Полуприцеп");
		$(this).attr("data-name","semitrailer");
		$("#typeavto").html("Полуприцеп");
		$("#typeavto").css("color","#000");
	}else{
		$(this).attr("data","Одиночка-прицеп");
		$(this).attr("data-name","trailer");
		$("#typeavto").html("Одиночка-прицеп");
		$("#typeavto").css("color","#000");
	}


	//Редактирование профиля водителя
	$(document).on('click','#update_profile_driver', function(){
		$.ajax({
			url: '/api/v1/update_profile_driver/',
			type: 'POST',
			data: {
				name: $("#name_driver").val(),
				phone: $("#phone-driver-profile").val(),
				country_id: "1",
				city_id: $("#profile-driver-from").attr("city-id"),
				email: $("#mail_driver").val(),
			}
		}).done(function(data){
			console.log(data);
			result=data;
			console.log(data);
			if (result.result){
				$("#result").html("<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'>&times;</button><h4 class='modal-title'>Статус</h4></div><div class='modal-body'><p>Ваши изменения сохранены.</p></div><div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss='modal'>Закрыть</button></div></div></div>");
				$("#result-link").click();
			}else{
				$("#result").html("<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'>&times;</button><h4 class='modal-title'>Статус</h4></div><div class='modal-body'><p>Произошла ошибка, изменения не сохранены.<br/>Повторите попытку позже.<br/>Если ошибка повториться, обратитесь в техническую поддержку.</p></div><div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss='modal'>Закрыть</button></div></div></div>");
				$("#result-link").click();
			}
		});			
	});

	//Кнопка закрыть
	$(document).on('click','.close_reload', function(){
		location.reload();
	});

	//Редактирование авто водителя
	$(document).on('click','#update_car_driver', function(e){
		e.preventDefault();
		load_type_top=false;
		load_type_rear=false;
		load_type_side=false;
		loaders=false;
		if(!$("#driver-profile-checkbox-1").is(':checked') && !$("#driver-profile-checkbox-2").is(':checked') && !$("#driver-profile-checkbox-3").is(':checked')){
			alert("Не выбран тип загрузки");
		}else{
			if($("#driver-profile-checkbox-1").is(':checked')){
				load_type_top=true;
			}
			if($("#driver-profile-checkbox-2").is(':checked')){
				load_type_rear=true;
			}
			if($("#driver-profile-checkbox-3").is(':checked')){
				load_type_side=true;
			}
			if($("#checkbox-10").is(':checked')){
				ds='in_city';
			}
			if($("#checkbox-11").is(':checked')){
				ds='intercity';
			}
			if($("#checkbox-11").is(':checked') && $("#checkbox-10").is(':checked')){
				ds='both';
			}
			if($("#checkbox-12").is(':checked')){
				loaders=true;			
			}
		
			$.ajax({
				url: '/api/v1/update_profile_driver/',
				type: 'POST',
				data: {
					car_name: $("#car_name").val(),
					car_type: $("#typeavto").attr("data-name"),
					body_type_id: $("#type").attr("data-name"),
					capacity: $("#capacity").val(),
					volume: $("#volume").val(),
					load_type_top: load_type_top,
					load_type_rear: load_type_rear,
					load_type_side: load_type_side,
					driver_specialization: ds,
					loaders: loaders,
					rate: $("#rate").val(),
					// rateundercity
				}
			}).done(function(data){
				result=data;
				if (result.result){
					$("#result-car-modal-body").html("Ваши изменения сохранены.");
					$("#result_car").modal("show");
				}else{
					$("#result-car-modal-body").html("Произошла ошибка, изменения не сохранены.<br/>Повторите попытку позже.<br/>Если ошибка повториться, обратитесь в техническую поддержку.");
					$("#result_car").modal("show");
				}
			});
		}			
	});

	//Редактирование профиля заказчика
	$(document).on('click','#update_profile_customer', function(e){
		e.preventDefault();
		$.ajax({
			url: '/api/v1/update_profile_customer/',
			type: 'POST',
			data: {
				name: $("#name_customer").val(),
				organization: $("#org_customer").val(),
				phone: $("#phone-customer-profile").val(),
				country_id: "1",
				city_id: $("#profile-customer-from").attr("city-id"),
				email: $("#mail_customer").val(),
			}
		}).done(function(data){
			result=data;
			if (result.result){
				$("#result").html("<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'>&times;</button><h4 class='modal-title'>Статус</h4></div><div class='modal-body'><p>Ваши изменения сохранены.</p></div><div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss='modal'>Закрыть</button></div></div></div>");
				$("#result-link").click();
			}else{
				$("#result").html("<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'>&times;</button><h4 class='modal-title'>Статус</h4></div><div class='modal-body'><p>Произошла ошибка, изменения не сохранены.<br/>Повторите попытку позже.<br/>Если ошибка повториться, обратитесь в техническую поддержку.</p></div><div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss='modal'>Закрыть</button></div></div></div>");
				$("#result-link").click();
			}
		});			
	});

	//Кнопка загрузки фото
	$(document).on('click','#btn-file', function(){
		$("#uploadbtn").click();
	});

	//Редактирование заказа
	$(document).on('click','.edit-name', function(e){
		e.preventDefault();
		$(this).addClass("save-edit-order blue-btn edit-save-name");
		$(this).find("a").html("<i class='fa fa-floppy-o' aria-hidden='true'></i>Сохранить");
		$("#edit-tab").find("a").css({
			"background-color": "#04c!important;",
			"opacity":"0.8",
			"color":"#fff",
		});

		if($("#detail-edit-zakaz-to").attr("city-id")=='0' || $("#detail-edit-zakaz-to").attr("city-id")==0 || $("#detail-edit-zakaz-to").attr("city-id")==''){
			$("#detail-order-city-to").removeAttr("checked","checked");
			$("#detail-order-city-to").removeClass("ch_new");
			$(".edit-row-to").hide();
		}else{
			$("#detail-order-city-to").attr("checked","checked");
			$("#detail-order-city-to").addClass("ch_new");		
		}
		$("#edit-tab").find("a").addClass("save-edit-order");
		$(".disb").each(function(){
			$(this).hide();
		});
		$(".disn").each(function(){
			$(this).show();
		});
		$(".compulsory_attr").each(function(){
      		$(this).show();
    	});
		// $("#zakaz-checkbox-1").attr("checked","checked");
		// $("#zakaz-checkbox-1").addClass("ch_new");
		// $("#edit-zakaz-to").val("");
		// $(".row-to").show("slow");
		if($("#oc-date").val()=='open'){
			$("#zakaz-checkbox-2").attr("checked","checked");
			$("#zakaz-checkbox-2").addClass("ch_new");
			$(".zakaz-dates").hide();
		}else{

		}
    	if($("#price").val()=='' || $("#price").val()=='0'){
    		$("#radio-6").attr("checked","checked");
    		$("#radio-6").addClass("r_new");
    	}else{
    		$("#price-block").show("slow");
    		$("#zakaz-radio-5").attr("checked","checked");
    		$("#zakaz-radio-5").addClass("r_new");
    	}
    	if($("#cash-div").attr("data")=='cash'){
    		$("#cash-div").find("#cash_pay").attr("checked","checked");
    		$("#cash-div").find("#cash_pay").addClass("ch_new");
    	}else if($("#cash-div").attr("data")=='non_cash'){
			$("#cash-div").find("#non_cash_pay").attr("checked","checked");
    		$("#cash-div").find("#non_cash_pay").addClass("ch_new");
    	}else{
    		$("#cash-div").find("#cash_pay").attr("checked","checked");
    		$("#cash-div").find("#cash_pay").addClass("ch_new");
    		$("#cash-div").find("#non_cash_pay").attr("checked","checked");
    		$("#cash-div").find("#non_cash_pay").addClass("ch_new");
    	}
    	if($("#money").attr("data-value")=="ton"){
    		$("#money").attr("data","тонн");
    	}
    	if($("#money").attr("data-value")=="kilo"){
    		$("#money").attr("data","кг")
    	}
    	$("#money").html($("#money").attr("data"));
    	$("#money").css("color","#000");
    	$(this).removeClass("edit-name");
	});


	//Запрос на редактирование заказа
	$(document).on('click','.save-edit-order', function(e){
		e.preventDefault();
		this_elem=$(this);
		if(ValidationZakaz()){
			method=''
			if($("#cash_pay").is(':checked')){
				method='cash';
			}
			if($("#non_cash_pay").is(':checked')){
				method='non_cash';
			}
			if($("#cash_pay").is(':checked') && $("#non_cash_pay").is(':checked')){
				method='both';
			}
			if($('#zakaz-checkbox-2').is(':checked')){
				$("#startdatetask").val('');
				$("#enddatetask").val('');
			}
			$.ajax({
				url: '/api/v1/update_order/',
				type: 'POST',
				data: {
					id_order: $(".idorder").attr("id-order"),
					cargo_name: $("#cargo_name").val(),
					cargo_weight: $("#cargo_weight").val(),
					weight_unit: $("#money").attr("data-value"),
					price: $("#price").val(),
					//заглушка на валюту
					currency: "rub",
					payment_method: method,
					start_city_id: $("#detail-edit-zakaz-ot").attr("city-id"),
					finish_city_id: $("#detail-edit-zakaz-to").attr("city-id"),
					start_date: $("#startdatetask").val(),
					finish_date: $("#enddatetask").val(),
					comment: $("#edit-zakaz-comment").val(),
				}
			}).done(function(data){
				result=data;
				$(".edit-tab").removeClass("save-edit-order blue-btn edit-save-name");
				$(".edit-tab").addClass("edit-name");
				$(".edit-tab").find("a").html("<i class='fa fa-pencil-square-o' aria-hidden='true'></i>Редактировать");
				$(".edit-tab").find("a").css({
					"background-color":"transparent",
				});
				$(".disb").each(function(){
					$(this).show();
				});
				$(".disn").each(function(){
					$(this).hide();
				});
				$("#edit-zakaz-new-comment").html(result.info_order.comment);
				// if(result.info_order.start_date!=''){
				// 	alert(result.info_order.start_date);
				// 	
				// }
				if(result.info_order.start_date!=''){
					$('.date-div').html("<div class='col-lg-12 disb'><div class='row'><div class='subcaption-zakaz col-lg-3'>Начало:</div><div class='col-lg-9 static-inf static-city-name' id='static-stdate'>"+newFormatDate(result.info_order.start_date)+"</div></div><div class='row'><div class='subcaption-zakaz col-lg-3'>Окончание:</div><div class='col-lg-9 static-inf static-city-name' id='static-enddate'>"+newFormatDate(result.info_order.finish_date)+"</div></div></div>");
				}else{
					$('.date-div').html("<div class='row'><div class='subcaption-zakaz col-lg-3'>Открытая дата</div></div>");
				}
				$("#static-cargo-name").html(result.info_order.cargo_name);
				$("#cargo_name").val(result.info_order.cargo_name);
				weight=''
				if(result.info_order.weight_unit=='ton'){
					weight='тонн'
				}else{
					weight='кг'
				}
				$("#static-cargo-unit").html(result.info_order.cargo_weight+" "+weight);
				$("#cargo_weight").val(result.info_order.cargo_weight);
				$("#money").attr("data-value",result.info_order.weight_unit);
				$("#money").attr("data",weight);
				if(result.info_order.price=='' || result.info_order.price=='0'){
					$("#static-fix-price").html("По договоренности");
				}else{
					valuta='';
					if(result.info_order.currency=='rub'){
						valuta='руб.'
					}
					$("#price").val(result.info_order.price);
					$("#static-fix-price").html("Фиксированная цена: <span>"+result.info_order.price+"</span> "+valuta);
				}
				cash=''
				if(result.info_order.payment_type=='cash'){
					cash='Наличными'
				}else if(result.info_order.payment_type=='non_cash'){
					cash='Безнал'
				}else{
					cash='Часть наличными, часть по безналу'
				}
				if ($("#cash_pay")){}
				$("#static-cash").html(cash);
				$("#cash-div").attr("data",result.info_order.payment_type);
				
				$("#detail-static-city-ot").find(".col-lg-12").html(result.info_order.start_city.name+" "+result.info_order.start_city.parent_name+" "+result.info_order.start_city.country_name);
				
				$("#detail-edit-zakaz-ot").val(result.info_order.start_city.name+" "+result.info_order.start_city.parent_name+" "+result.info_order.start_city.country_name);
				if (result.info_order.finish_city.id!=undefined){
					$("#detail-static-city-to").find(".col-lg-12").html(result.info_order.finish_city.name+" "+result.info_order.finish_city.parent_name+" "+result.info_order.finish_city.country_name+"</div>");
					$("#detail-edit-zakaz-to").val(result.info_order.finish_city.name+" "+result.info_order.finish_city.parent_name+" "+result.info_order.finish_city.country_name);
					$("#detail-static-city-to").show();
					$("#detail-static-city-to").attr("city-id",result.info_order.finish_city.id);
					$("#detail-static-city-to").val(result.info_order.finish_city.name+" "+result.info_order.finish_city.parent_name+" "+result.info_order.finish_city.country_name);
				}
				$("#detail-static-city-ot").attr("city-id",result.info_order.start_city.id);
				$("#detail-static-city-ot").val(result.info_order.start_city.name+" "+result.info_order.start_city.parent_name+" "+result.info_order.start_city.country_name);
				$(".compulsory_attr").each(function(){
      				$(this).hide();
    			});
			});	
		}else{
			
		}
	});
	function newFormatDate(cur_date) {
		var date_new = new Date(cur_date.substring(0,10).replace(/(\d+)-(\d+)-(\d+)/, '$2/$3/$1'));
		var month = ["января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"];
    return date_new.getDate() + ' ' + month[date_new.getMonth()] + '.' + date_new.getFullYear();
	}

	//Валидация
	function ValidationSub(idsub){
		err=0;
		if($("#subscription-city-ot-"+idsub).attr("city-id")==''){
			err=1;
			$("#err_city_ot-subscription-"+idsub).css("display","block");
			$("#subscription-city-ot-"+idsub).css("border","1px solid red");
		}else{
			$("#err_city_ot-subscription-"+idsub).css("display","none");
			$("#subscription-city-ot-"+idsub).css("border","1px solid #e3e3e3");
		}
		if($("#subscription-city-to-"+idsub).attr("city-id")=='' && $("#subscription-checkbox-intercity-"+idsub).is(':checked')){
			err=2;
			$("#err_city_to-subscription-"+idsub).css("display","block");
			$("#subscription-city-to-"+idsub).css("border","1px solid red");
		}else{
			$("#err_city_to-subscription-"+idsub).css("display","none");
			$("#subscription-city-to-"+idsub).css("border","1px solid #e3e3e3");
		}
		if(($("#subscription-checkbox-fixprice-"+idsub).is(":checked") || $("#subscription-checkbox-allprice-"+idsub).is(":checked")) && ($("#subscription-checkbox-cash_pay-"+idsub).is(":checked") || $("#subscription-checkbox-non_cash_pay-"+idsub).is(":checked"))){
			$("#err_pay_status-subscription-"+idsub).css("display","none");
		}else{
			$("#err_pay_status-subscription-"+idsub).css("display","block");
			err=3;
		}
		if($("#subscription-cargo_weight-ot-"+idsub).val()==''){
			err=4;
			$("#err_weight_ot-subscription-"+idsub).css("display","block");
			$("#subscription-cargo_weight-ot-"+idsub).css("border","1px solid red");
		}else{
			$("#err_weight_ot-subscription-"+idsub).css("display","none");
			$("#subscription-cargo_weight-ot-"+idsub).css("border","1px solid #e3e3e3");
		}
		if($("#subscription-cargo_weight-to-"+idsub).val()==''){
			err=5;
			$("#err_weight_to-subscription-"+idsub).css("display","block");
			$("#subscription-cargo_weight-to-"+idsub).css("border","1px solid red");
		}else{
			$("#err_weight_to-subscription-"+idsub).css("display","none");
			$("#subscription-cargo_weight-to-"+idsub).css("border","1px solid #e3e3e3");
		}
		if(!$("#subscription-checkbox-open_date-"+idsub).is(":checked")){
			if($("#subscription-startdatetask-"+idsub).val()==''){
				err=8;
				$("#err_startdate-subscription-"+idsub).css("display","block");
				$("#subscription-startdatetask-"+idsub).css("border","1px solid red");
			}else{
				$("#err_startdate-subscription-"+idsub).css("display","none");
				$("#subscription-startdatetask-"+idsub).css("border","1px solid #e3e3e3")			
			}
			if($("#subscription-enddatetask-"+idsub).val()==''){
				err=9;
				$("#err_enddate-subscription-"+idsub).css("display","block");
				$("#subscription-enddatetask-"+idsub).css("border","1px solid red");
			}else{
				$("#err_enddate-subscription-"+idsub).css("display","none");
				$("#subscription-enddatetask-"+idsub).css("border","1px solid #e3e3e3");	
			}
		}
		
		if(err>0){
			return false
		}else{
			return true
		}
	}







	function BigDate(fd,sd){
		var firstValue = fd.split('.');
		var secondValue = sd.split('.');
		var firstDate=new Date();
		firstDate.setFullYear(firstValue[0],(firstValue[1] - 1 ),firstValue[2]);
		var secondDate=new Date();
		secondDate.setFullYear(secondValue[0],(secondValue[1] - 1 ),secondValue[2]);     

		if (firstDate > secondDate)
		{
			return false
		}
		else
		{
		return true
		}
	}
	function ValidationZakaz(){
		
		err=0
		if($("#bt-list").val()==null){
			$("#bt-list-err").css("display","block");
			$(".select2-selection").css("border","1px solid red");
		}else{
			$("#bt-list-err").css("display","none");
			$(".select2-selection").css("border","none");
		}
		if($("#detail-edit-zakaz-to").attr("city-id")=='' && $("#detail-order-city-to").is(':checked')){
			err=12
			$("#detail-err_city_to").css("display","block");
			$("#detail-edit-zakaz-to").css("border","1px solid red");
		}else{
			$("#detail-err_city_to").css("display","none");
			$("#detail-edit-zakaz-to").css("border","none");			
		}
		if($("#detail-edit-zakaz-ot").attr("city-id")==''){
			err=12
			$("#detail-err_city_ot").css("display","block");
			$("#detail-edit-zakaz-ot").css("border","1px solid red");
		}else{
			$("#detail-err_city_ot").css("display","none");
			$("#detail-edit-zakaz-ot").css("border","none");			
		}
		if($("#new-zakaz-ot").attr("city-id")==''){
			err=1;
			$("#err_city_ot").css("display","block");
			$("#new-zakaz-ot").css("border","1px solid red");
		}else{
			$("#err_city_ot").css("display","none");
			$("#new-zakaz-ot").css("border","none");
		}
		if($("#new-zakaz-to").attr("city-id")=='' && $("#zakaz-checkbox-1").is(':checked')){
			err=2;
			$("#err_city_to").css("display","block");
			$("#new-zakaz-to").css("border","1px solid red");
		}else{
			$("#err_city_to").css("display","none");
			$("#new-zakaz-to").css("border","none");
		}
		if(($("#edit-zakaz-to").attr("city-id")=='' || $("#edit-zakaz-to").attr("city-id")=='0') && $("#zakaz-checkbox-1").is(':checked')){
			err=10;
			$("#err_city_to").css("display","block");
			$("#edit-zakaz-to").css("border","1px solid red");
		}else{
			$("#err_city_to").css("display","none");
			$("#edit-zakaz-to").css("border","none");
		}
		// цена по договоренности или фиксированная
		if(!$("#zakaz-radio-5").is(':checked') && !$("#radio-6").is(':checked')){
			err=3;
			$("#pay_status").css("display","block");
		}
		// }else if($("#zakaz-radio-5").is(':checked') && $("#price").val()==''){
		// 	err=1;
		// 	$("#pay_status").show();
		// 	$("#err_price").show();
		// }
		if(($("#zakaz-radio-5").is(':checked') || $("#radio-6").is(':checked')) && ($("#cash_pay").is(':checked') || $("#non_cash_pay").is(':checked'))){
			$("#pay_status").css("display","none");
		}else{
			$("#pay_status").css("display","block");
			err=4;
		}
		if($("#zakaz-radio-5").is(':checked') && $("#price").val()!=''){
			$("#err_price").css("display","none");
			$("#price").css("border","none");
		}else if ($("#radio-6").is(':checked')){
			$("#pay_status").css("display","none");
		}else{
			$("#err_price").css("display","block");
			$("#price").css("border","1px solid red");
			err=5;
		}

		if($("#cargo_name").val()==''){
			err=6;
			$("#cargo_name_err").css("display","block");
			$("#cargo_name").css("border","1px solid red");
		}else{
			$("#cargo_name_err").css("display","none");
			$("#cargo_name").css("border","none");			
		}
		if($("#cargo_weight").val()=='' || $("#cargo_weight").val()<=0){
			err=7;
			$("#cargo_weight_err").css("display","block");
			$("#cargo_weight").css("border","1px solid red");
		}else{
			$("#cargo_weight_err").css("display","none");
			$("#cargo_weight").css("border","none");			
		}
		if(!$("#zakaz-checkbox-2").is(':checked')){
			if($("#startdatetask").val()==''){
				err=8;
				$("#startdate-err").css("display","block");
				$("#startdatetask").css("border","1px solid red");
			}else{
				$("#startdate-err").css("display","none");
				$("#startdatetask").css("border","none");	
				if(!BigDate($("#startdatetask").val(),$("#enddatetask").val())){
					err=11;
					// alert("yes")
					$("#startdate-err").css("display","block");
					$("#startdatetask").css("border","1px solid red");
				}else{
					// alert("no")
					$("#startdate-err").css("display","none");
					$("#startdatetask").css("border","none");			
				}		
			}
			if($("#enddatetask").val()==''){
				err=9;
				$("#enddate-err").css("display","block");
				$("#enddatetask").css("border","1px solid red");
			}else{
				$("#enddate-err").css("display","none");
				$("#enddatetask").css("border","none");			
			}
		}
		// alert(err);
		if(err>0){
			return false
		}else{
			return true
		}
	}

	//Добавление заказа
	$(document).on('click','#add_order', function(e){
		e.preventDefault();
		if(ValidationZakaz()){
			method=''
			if($("#cash_pay").is(':checked')){
				method='cash';
			}
			if($("#non_cash_pay").is(':checked')){
				method='non_cash';
			}
			if($("#cash_pay").is(':checked') && $("#non_cash_pay").is(':checked')){
				method='both';
			}
			if($("#money").attr("data")=='кг'){
				unit='kilo'
			}else{
				unit='ton'
			}
			arr=JSON.stringify($("#bt-list").val());
			$.ajax({
				url: '/api/v1/customer/add_order/',
				type: 'POST',
				data: {
					cargo_name: $("#cargo_name").val(),
					cargo_weight: $("#cargo_weight").val(),
					weight_unit: unit,
					price: $("#price").val(),
					//заглушка на валюту
					currency: "rub",
					body_types: arr,
					payment_method: method,
					start_city_id: $("#new-zakaz-ot").attr("city-id"),
					finish_city_id: $("#new-zakaz-to").attr("city-id"),
					start_date: $("#startdatetask").val(),
					finish_date: $("#enddatetask").val(),
					comment: $("#new-zakaz-comment").val(),
				}
			}).done(function(data){
				$("#new-zakaz-ot").val("");
				$("#new-zakaz-ot").prop('readonly', false);
				$("#new-zakaz-ot").attr("city-id","");
				$("#new-zakaz-to").val("");
				$("#new-zakaz-to").prop('readonly', false);
				$("#new-zakaz-to").attr("city-id","");
				$("#cargo_name").val('');
				$("#cargo_weight").val('');
				$("#money").attr("data","кг");
				$("#price").val("");
				$("#startdatetask").val("");
				$("#enddatetask").val("");
				$("#new-zakaz-comment").val("");
				$(".modal-body").find("p").html('Ваш заказ успешно создан. Статус можно отследить в личном кабинете в разделе "Мои заказы"');
				$("#create_oreder_done").modal('show');

			});	
		}else{
			
		}
	});

	//Обработка табов в редактировании заказа
	$(document).on('click','.info-tab', function(){
		$(".edit-tab").show();
		$(".delete-tab").show();
		$(".views-tab").hide();
		$(".response-tab").hide();
	});

	function getRandomArbitary(min, max)
	{
	  return (parseInt((Math.random() * (max - min) + min)*10))/10;
	}

	//Чистка таба подписок
	$(document).on('click','#subscription-tab-new_sub', function(){
		$("#subscription-subtab-new_sub").find("input[type=checkbox]").each(function(){
			if ($(this).prop("checked") && $(this).attr('id')!="subscription-checkbox-incity-n"){
				$(this).removeAttr("checked");
			}
		});
		$(".row-sub-to").hide("slow");
		$("#subscription-cargo_weight-ot-n").val("");
		$("#subscription-cargo_weight-to-n").val("");
		$("#subscription-startdatetask-n").val("");
		$("#subscription-enddatetask-n").val("");
		$("#subscription-city-ot-n").val("");
		$("#subscription-city-ot-n").attr("city-id","");
		$("#subscription-city-ot-n").attr("value","");
		$("#subscription-city-to-n").val("");
		$("#subscription-city-to-n").attr("city-id","");
		$("#subscription-city-to-n").attr("value","");
	});
	
	//Добавление подписки
	$(document).on('click','#add-subscription', function(e){
		e.preventDefault();
		if(ValidationSub("n")){
			cash='';
			if($("#subscription-checkbox-cash_pay-n").is(":checked")){
				cash='cash';
			}
			if($("#subscription-checkbox-non_cash_pay-n").is(":checked")){
				cash='non_cash';
			}
			if($("#subscription-checkbox-cash_pay-n").is(":checked") && $("#subscription-checkbox-non_cash_pay-n").is(":checked")){
				cash='both';
			}
			pay_type='';
			if($("#subscription-checkbox-fixprice-n").is(":checked")){
				pay_type='fixed';
			}
			if($("#subscription-checkbox-allprice-n").is(":checked")){
				pay_type='open';
			}
			if($("#subscription-checkbox-fixprice-n").is(":checked") && $("#subscription-checkbox-allprice-n").is(":checked")){
				pay_type='both';
			}
			order_type='';
			if($("#subscription-city-ot-n").val()==''){
				order_type='intercity';
			}
			if($("#subscription-city-to-n").val()==''){
				order_type='city';
			}
			if($("#subscription-city-to-n").val()!='' && $("#subscription-city-ot-n").val()!=''){
				order_type='both';
			}
			finish='';
			start='';
			if (!$("#subscription-checkbox-open_date-n").is(":checked")){
				finish=$("#subscription-enddatetask-n").val();
				start=$("#subscription-startdatetask-n").val();
			}
			$.ajax({
				url: '/api/v1/add_subscribe/',
				type: 'POST',
				data: {
					payment_method: cash,
					payment_type: pay_type,
					start_id: $("#subscription-city-ot-n").attr("city-id"),
					finish_id: $("#subscription-city-to-n").attr("city-id"),
					order_type: order_type,
					start_date: start,
					finish_date: finish,
					min_killo: $("#subscription-cargo_weight-ot-n").val(),
					max_killo: $("#subscription-cargo_weight-to-n").val(),
					active: true,
				}
			}).done(function(data){
				result=data;
				if (result.result){
					$("#add_subscribe_done_body").find("p").html('Ваша подписка успешно создана.<br/>Теперь все заказы будут фильтроваться с учетом этой подписки');
					$("#add_subscribe_done").modal('show');
				}
			});	
		}else{
			var id  = $(this).parent().attr('href');
            var top = $(id).offset().top;
        	$('body,html').animate({scrollTop: top}, 1500);
		}	
	});
	$(document).on('click','.sub_done', function(){
		// if(location.hash!='#subscribe'){
		// 	document.location.href=location.href+"#subscribe";
		// }
		location.reload();
		$('body,html').animate({scrollTop: 0}, 400);
		// $("#subscription-tab-new_sub").click();
		// pmc=false;
  //       pmn=false;
  //       if (result.subscribe.payment_method=='cash'){
  //       	pmc=true;
  //       };
  //       if (result.subscribe.payment_method=='non_cash'){
  //       	pmn=true;
  //       };
  //       if (result.subscribe.payment_method=='both'){
		// 	pmc=true;
		// 	pmn=true;
		// };
  //       pto=false;
  //       ptf=false;
  //       if (result.subscribe.payment_type=='open'){
  //       	pto=true;
  //       };

  //       if (result.subscribe.payment_type=='fixed'){
  //       	ptf=true;
  //       };
  //       if (result.subscribe.payment_type=='both'){
	 //        pto=true;
	 //        ptf=true;       	
  //       };
		// if(result.subscribe.finish_city === "defined"){
		// 	finish_city=result.subscribe.finish_city.name+" "+result.subscribe.finish_city.parent_name+" "+result.subscribe.finish_city.country_name;
		// 	finish_city_id=result.subscribe.finish_city.id;
		// }else{
		// 	finish_city='';
		// 	finish_city_id='';
		// };
		// loop=$('.sub-left').length+1;
		// console.log(result);
		// $("#accordion-js").append("<section id='sub_"+loop+"'><div id='sub-mega-div-"+loop+"' style='position: relative;'><div class='col-lg-4' style='margin-top: 39px;text-align-left;'><div class='int_search' id='subscription-div-active-"+loop+"' style='margin-left: 60px;' data-loop-id='"+loop+"' data-id='"+result.subscribe.id+"'><input type='checkbox' class='checkbox subscription-active-checkbox' id='subscription-active-checkbox-"+loop+"' name=''/><label for='subscription-checkbox-active-"+loop+"'>Подписка не активна</label></div></div><div class='col-lg-4 sub-right'><div class='btn btn-danger delete_subscribe' data-loop='"+loop+"' id='delete_subscribe-"+loop+"' data-id='"+result.subscribe.id+"'>Удалить подписку</div></div><div class='accordion-item' id='subscription-tab-"+loop+"'><h3 class='sub-left'>Подписка № "+loop+"</h3></div><div class='row flow-offset-1 product-info subscription-div' style='margin-top: 0px;' id='subscription-subtab-sub-"+loop+"'><div style='clear: both;'></div><div class='col-lg-12'><div class='product-info-body text-center text-md-left'><fieldset><div class='col-lg-12 martop'><div class='col-lg-12 martop'><div class='caption-subscription marbot'>Город</div><div class='col-lg-9'><div class='row'><div class='subcaption-subscription col-lg-3'>Отправления:<span class='compulsory_attr'>&#042;</span></div><div class='col-lg-9'><div class='col-lg-11'><label class='z-104 mfSelect' data-add-placeholder><input type='text' value='"+result.subscribe.start_city.name+" "+result.subscribe.start_city.parent_name+" "+result.subscribe.start_city.country_name+"' class='city' id='subscription-city-ot-"+loop+"' placeholder='Начните вводить название города' city-id='"+result.subscribe.start_city.id+"' autocomplete='off'/><ul class='dropdown' id='subscription-ot-"+loop+"'></ul><div class='loader' data-id-loader='subscription-city-ot-"+loop+"' style='top: 8px;right: 20px; display: none;'></div></label><div class='err' id='err_city_ot-subscription-"+loop+"' style='display: none'>Выберите город отправления из списка</div></div><div class='col-lg-1 col-sm-1 col-xs-2'><i class='fa fa-times delete-city' data-id='subscription-city-ot-"+loop+"' aria-hidden='true'></i></div></div></div><div class='row' id='subscription-to-div-"+loop+"' style='display: none;'><div class='subcaption-subscription col-lg-3'>Назначения:<span class='compulsory_attr'>&#042;</span></div><div class='col-lg-9'><div class='col-lg-11'><label class='z-103 mfSelect' data-add-placeholder><input type='text' value='"+finish_city+"' class='city' id='subscription-city-to-"+loop+"' placeholder='Начните вводить название города' city-id='"+finish_city_id+"' autocomplete='off'/><ul class='dropdown' id='subscription-to-"+loop+"'></ul><div class='loader' data-id-loader='subscription-city-to-"+loop+"' style='top: 8px;right: 20px; display: none;'></div></label><div class='err' id='err_city_to-subscription-"+loop+"' style='display: none'>Выберите город назначения из списка</div></div><div class='col-lg-1 col-sm-1 col-xs-2'><i class='fa fa-times delete-city' data-id='subscription-city-to-"+loop+"' aria-hidden='true'></i></div></div></div></div><div class='col-md-3 radio-item'><div class='int_search' style='margin-top: 15px;'><input type='checkbox' class='checkbox' id='subscription-checkbox-incity-"+loop+"' checked='checked' name=''/><label for='subscription-checkbox-incity-"+loop+"'>По городу</label></div><div class='int_search' style='margin-top: 40px;' data-loop='"+loop+"'><input type='checkbox' class='ch_new checkbox' id='subscription-checkbox-intercity-"+loop+"' checked='checked' name=''/><label for='subscription-checkbox-intercity-"+loop+"'>Межгород</label></div></div><div style='clear:both;'></div><hr><div class='caption-subscription marbot martop'>Оплата<span class='compulsory_attr'>&#042;</span></div><div class='err' id='err_pay_status-subscription-"+loop+"' style='display: none;'>Выберите цену и способ оплаты</div><div class='col-lg-12'><div class='int_search'><div class='col-lg-4' style='padding: 0px 0px 20px 0px;'><div class='pull-left' style='padding-top: 15px;'><input type='checkbox' class='checkbox ch_new' id='subscription-checkbox-fixprice-"+loop+"' checked='checked' name=''><label for='subscription-checkbox-fixprice-"+loop+"'>Фиксированная цена</label></div></div></div><div class='int_search'><div class='col-lg-4' style='padding-bottom: 20px;'><div class='pull-left'><input type='checkbox' class='checkbox ch_new' id='subscription-checkbox-allprice-"+loop+"' checked='checked' name=''/><label for='subscription-checkbox-allprice-"+loop+"'>По договоренности</label></div></div></div></div><div style='clear:both'></div><div class='col-lg-12'><div class='int_search'><div class='col-lg-4' style='padding: 0px 0px 20px 0px;'><div class='pull-left' style='padding-top: 15px;'><input type='checkbox' class='checkbox ch_new' id='subscription-checkbox-cash_pay-"+loop+"' checked='checked' name='radio'/><label for='subscription-checkbox-cash_pay-"+loop+"'>Оплата наличными</label></div></div></div><div class='int_search martop'><div class='col-lg-4' style='padding-bottom: 20px;'><div class='pull-left'><input type='checkbox' class='checkbox ch_new' id='subscription-checkbox-non_cash_pay-"+loop+"' checked='checked' name='radio'/><label for='subscription-checkbox-non_cash_pay-"+loop+"'>Безналичный расчет</label></div></div></div></div><div style='clear:both'></div><hr><div class='caption-subscription marbot martop'>Информация о грузе</div><div class='col-lg-9'><div class='row'><div class='subcaption-subscription col-lg-3'>Вес:<span class='compulsory_attr'>&#042;</span></div><div class='col-lg-9'><div class='col-lg-5 col-sm-6'><label class='z-104 mfSelect' data-add-placeholder><input type='text' class='' value='"+result.subscribe.min_killo+"' id='subscription-cargo_weight-ot-"+loop+"' placeholder='Минимальный вес' autocomplete='off'/></label><div class='err' id='err_weight_ot-subscription-"+loop+"' style='display: none'>Введите вес</div></div><div class='col-lg-2'><div class='little-hr'></div></div><div class='col-lg-5 col-sm-6'><label class='z-104 mfSelect' data-add-placeholder><input type='text' class='' value='"+result.subscribe.max_killo+"' id='subscription-cargo_weight-to-"+loop+"' placeholder='Максимальный вес' autocomplete='off'/></label><div class='err' id='err_weight_to-subscription-"+loop+"' style='display: none'>Введите вес</div></div></div></div></div><div class='subcaption-subscription col-lg-3'>кг</div><div style='clear:both'></div><hr><div class='caption-subscription marbot martop'>Даты<span class='compulsory_attr'>&#042;</span></div><div class='col-lg-12 martop marbot'><div class='int_search' data-loop='"+loop+"'><input type='checkbox' class='checkbox ch_new' id='subscription-checkbox-open_date-"+loop+"' checked='checked' name=''/><label for='subscription-checkbox-open_date-"+loop+"'>Открытая дата</label></div></div><div class='martop' id='subscription-dates-"+loop+"' style='display:none'><div class='pull-left col-lg-6 col-sm-6'><input class='datepicker cursor' readonly type='text' id='subscription-startdatetask-"+loop+"' value='"+result.subscribe.start_date+"' placeholder='Начало'/><div class='err' id='err_startdate-subscription-"+loop+"' style='display: none;'>Выберите дату начала</div></div><div class='col-lg-6 col-sm-6' style='margin-top: 0px;'><input class='datepicker cursor' readonly value='"+result.subscribe.finish_date+"' type='text' id='subscription-enddatetask-"+loop+"' placeholder='Конец'/><div class='err' id='err_enddate-subscription-"+loop+"' style='display: none;'>Выберите дату окончания</div></div></div><div style='clear:both'></div><div class='mfControls btn-group text-center col-lg-12 marbot martop'><a href='#sub_"+loop+"'><button class='btn btn-sm btn-sunglow marbot save_sub' type='submit' id='edit-subscription-"+loop+"' data-loop-id='"+loop+"' sub-id='"+result.subscribe.id+"' style='float:none; margin-top: 20px;'>Сохранить изменения в подписке №"+loop+"</button></a></div></div></div></fieldset></div></div></div><hr></div></section>");
		// $("#subscription-tab-"+loop).siblings('.subscription-div').slideToggle();
		// if($(".sub-left").length>=3){
		// 	$("#subscription-tab-new_sub").hide();
		// }
	});
	
	//Подтверждение телефона и мыла в профиле
	
	$(document).on('click','#send_code_phone_next', function(e){
		e.preventDefault();
		$("#send_again").html("");
		$.ajax({
			url: '/api/v1/send_code_phone/',
			type: 'POST',
			data: {
				item: $(".confirm_code").attr("data-item"),
			}
		}).done(function(data){
			$(".send_again").html("<div class='col-md-12'><div class='martop'><div class='inline-block code-repeat-text'>Повторно выслать код через:&nbsp</div><div class='inline-block' id='timer'>2:00</div></div></div>");
			var ts = 120;//600
			newstime(ts);
			setTimeout(function () {
				$("#timer").hide();
				$("#send_again").html("<div class='col-md-12' style='text-align: center;'><a class='btn btn-xl btn-sunglow' href='#' id='send_code_phone_next'>Выслать код</a></div>");
				$("#send_again").show();
				$(".code-repeat-text").hide();
			}, 120000); // время в мс
		});		
	});
		
	$(document).on('click','.confirm', function(e){
		e.preventDefault();
		this_elem=$(this).parent().siblings().find("input");
		this_btn=$(this);
		$(this).html("Отправить");
		$(this).addClass("confirm_code");
		$(this).removeClass("confirm");
		$.ajax({
			url: '/api/v1/'+$(this).attr("data")+'/',
			type: 'POST',
			data: {
				item: this_elem.val(),
			}
		}).done(function(data){
			this_btn.attr("data-item",this_elem.val());
			this_elem.attr('value','');
			this_elem.attr("placeholder","Введите код");
			this_elem.attr('id',this_btn.attr("data")+'_input');
			$("#"+this_btn.attr("data")+"_input").mask("99999");
			this_elem.focus();
			$(".send_again").html("<div class='col-md-12'><div class='martop'><div class='inline-block code-repeat-text'>Повторно выслать код через:&nbsp</div><div class='inline-block' id='timer'>2:00</div></div></div>");
			var ts = 120;//600
			newstime(ts);
			setTimeout(function () {
				$("#timer").hide();
				$("#send_again").html("<div class='col-md-12' style='text-align: center;'><a class='btn btn-xl btn-sunglow' href='#' id='send_code_phone_next'>Выслать код</a></div>");
				$("#send_again").show();
				$('.code-repeat-text').hide();
			}, 120000); // время в мс
		});		
	});

	$(document).on('click','.confirm_code', function(e){
		e.preventDefault();
		$.ajax({
			url: '/api/v1/check_'+$(this).attr("data").substr(5)+'_profile/',
			type: 'POST',
			data: {
				code: $(this).parent().siblings().find("input").val(),
				item: $(this).attr("data-item"),
			}
		}).done(function(data){
			alert("checkcode");
		});
	});		
	
	//Предложить заказ водителю

	$(document).on('click','.offer_order', function(e){
		e.preventDefault();
		$("#offer_order_ok").modal('show');
		$("#order_driver_name").html($(this).attr("data-name")+"?");
		$("#offer_order_ok_btn").attr("id-order-list",$(this).attr("id-order-list"));
		$("#offer_order_ok_btn").attr("id-driver-list",$(this).attr("id-driver-list"));	
	});
	$(document).on('click','#offer_order_ok_btn', function(e){
		e.preventDefault();
		$.ajax({
			url: '/api/v1/offer_order/',
			type: 'POST',
			data: {
				order_id: $(this).attr("id-order-list"),
				driver_id:  $(this).attr("id-driver-list"),
			}
		}).done(function(data){
			// location.reload();
			// result=data;
			//добавить резалт труе который убирает кнопку предложить	
		});		
	});

	
	//Запрос на получение водителей, откликнувшихся на заказ
	$(document).on('click','.driver-tab', function(){
		$(".edit-tab").hide();
		$(".delete-tab").hide();
		$(".views-tab").show();
		$(".response-tab").show();
		$.ajax({
			url: '/api/v1/get_list_response_drivers/',
			type: 'POST',
			data: {
				id: $(this).find("a").attr("data"),
			}
		}).done(function(data){
			$("#list_drivers").html("");
			result=data;
			console.log(data);
			$(".response-tab").html("Всего: "+result.list_drivers.length+" Новых: "+result.new_list_drivers.length);
			for (var i = 0; i < result.new_list_drivers.length; i++){
				$("#list_drivers").append("<tr class='success'><td class=''><a href='/get_one_driver/"+result.new_list_drivers[i].id+"/'>"+result.new_list_drivers[i].name+"</a></td><td class=''>"+result.new_list_drivers[i].price+"</td><td class=''>"+result.new_list_drivers[i].car_name+"</td><td class=''><div class'mid'><div class='col-lg-2 col-sm-2 col-xs-2 rating' style='margin-left: -10px;'>"+result.new_list_drivers[i].rating+"</div><div id='rating_n"+i+"'><input type='hidden' class='val' value='"+result.new_list_drivers[i].rating+"'/></div></div></td><td class=''>"+result.new_list_drivers[i].phone+"</td><td class=''><a href='' data-name='"+result.new_list_drivers[i].name+"' id-driver-list='"+result.new_list_drivers[i].id+"' id-order-list='"+$(".idorder").attr("id-order")+"' class='btn btn-sunglow offer_order' style='visibility: visible; animation-name: fadeInRight;'>Предложить<br/>заказ</a></td>");
				$('#rating_n'+i).rating({
			      fx: 'float',
			          image: '/app/views/images/stars.png',
			          width: 20,
			          minimal: 0.6,
			            readOnly: true,
			      url: 'rating.php'
			    });
			}
			
			for (var i = 0; i < result.list_drivers.length; i++){
				if (i%2==0){
					grade="gradeA odd"
				}else{
					grade="gradeA even"
				}
				$("#list_drivers").append("<tr class="+grade+"><td class=''><a href='/get_one_driver/"+result.list_drivers[i].id+"/'>"+result.list_drivers[i].name+"</a></td><td class=''>"+result.list_drivers[i].price+"</td><td class=''>"+result.list_drivers[i].car_name+"</td><td class=''><div class'mid'><div class='col-lg-2 col-sm-2 col-xs-2 rating' style='margin-left: -10px;'>"+result.list_drivers[i].rating+"</div><div id='rating_"+i+"'><input type='hidden' class='val' value='"+result.list_drivers[i].rating+"'/></div></div></td><td class=''>"+result.list_drivers[i].phone+"</td><td class=''><a href='' data-name='"+result.list_drivers[i].name+"' id-driver-list='"+result.list_drivers[i].id+"' id-order-list='"+$(".idorder").attr("id-order")+"' class='btn btn-sunglow offer_order' style='visibility: visible; animation-name: fadeInRight;'>Предложить<br/>заказ</a></td>");
				$('#rating_'+i).rating({
			      fx: 'float',
			          image: '/app/views/images/stars.png',
			          width: 20,
			          minimal: 0.6,
			            readOnly: true,
			      url: 'rating.php'
			    });
			}
		});			
	});

	//Удаление заказа
	$(document).on('click','.delete_icon', function(){
		$("#ask1").modal('show');
		$("#btn-delete-order").attr("data",$(this).attr("id-order"));
		$(".order-num").html($(this).attr("id-order"));	
	});
	$(document).on('click','#delete_order', function(){
		$("#ask").modal('show');
		$("#btn-delete-order").attr("data",$(".idorder").attr("id-order"));	
	});

	$(document).on('click','#btn-delete-order', function(e){
		e.preventDefault();
		$.ajax({
			url: '/api/v1/delete_order/',
			type: 'POST',
			data: {
				id: $(this).attr("data"),
			},
		}).done(function(data){
			// if(location.hash!='#orders'){
			// 	document.location.href=location.href+"#orders";
			// }
			location.reload();
		});			
	});
	
	//Получение телефона в заказе
	$(document).on('click','#ask-phone-customer', function(e){
		e.preventDefault();
		$.ajax({
			url: '/api/v1/get_phone/',
			type: 'POST',
			data: {
				id: $(this).attr("data"),
			}
		}).done(function(data){
			result=data;
			$(".ask-phone-div").html("<div class='alert alert-info'>"+result.phone+"</div>");
		});			
	});

	//селекты
	//страница создания заказа
	$(document).on('click','#money', function(){
		ToggleSelect($(this).attr("id"));	
	});
	$(document).on('click','.money-item', function(){
		SaveItemsSelect($(this).attr("id"),$(this).parent().siblings().attr("id"),$(this).attr("data-name"),$(this).attr("data"),$(this).attr("data-value"));

	});

	//страница редактирования профиля водителя
	$(document).on('click','#type', function(){
		ToggleSelect($(this).attr("id"));	
	});
	$(document).on('click','.type-item', function(){
		SaveItemsSelect($(this).attr("id"),$(this).parent().siblings().attr("id"),$(this).attr("data-name"),$(this).attr("data"),'')
	});

	$(document).on('click','#typeavto', function(){
		ToggleSelect($(this).attr("id"));	
	});
	$(document).on('click','.typeavto-item', function(){
		SaveItemsSelect($(this).attr("id"),$(this).parent().siblings().attr("id"),$(this).attr("data-name"),$(this).attr("data"),'')
	});

	//кнопка очистки города
	$(document).on('click','.delete-city', function(){
		$('#'+$(this).attr("data-id")).val('');
		$('#'+$(this).attr("data-id")).attr('value','');
		$('#'+$(this).attr("data-id")).prop('readonly', false);
		$('#'+$(this).attr("data-id")).focus();
		$('#'+$(this).attr("data-id")).attr("city-id","");

	});

	//Отклик водителя на заказ
	$(document).on('click','#ask-response', function(){
		$("#send-response").attr("data",$(this).attr("data"));
		if($("#price").val()=='' || $("#price").val()=='0'){
			$(".price-modal-div").show();
		}else{
			$(".price-modal-div").hide();
		}
	});

	$(document).on('click','#ask-response-2nd', function(){
		$("#send-response").attr("data",$(this).attr("data"));
		$(".price-modal-div").show();
	});

	$(document).on('click','#send-response', function(){
		$.ajax({
			url: '/api/v1/response_driver/',
			type: 'POST',
			data: {
				id: $(this).attr("data"),
				price: $("#modal-price").val(),
				currency: "rub",
			}
		}).done(function(data){
			$("#response_done_ok").modal('show');
			$("#response_done").modal('hide');
		});			
	});
	$(document).on('click','.close_response_ok', function(){
		location.reload();
	});
	

	//отказать от услуг/заказа
	$(document).on('click','.delete_offer', function(){
		url=''
		if($(this).find("a").attr("data-page")=='customer'){
			url='/api/v1/refuse_driver/';
		}else{
			url='/api/v1/refuse_customer/';
		}
		$("#delete-cd-tab").attr("data",url);
		$("#delete-cd-tab").attr("data-id",$(this).attr("data"));
	});
	
	$(document).on('click','#delete-cd-tab', function(){
		$.ajax({
			url: $(this).attr("data"),
			type: 'POST',
			data: {
				id: $(this).attr("data-id")
			}
		}).done(function(data){
			location.reload();
		});			
	});
	
	$(document).on('click keypress mouseover mouseout','#ageInputId_driver', function(){
		if ($("#ageOutputId_driver").val()>1){
			$("#lower1").hide()	
		}else{
			$("#lower1").show()	
		}
	});
	$(document).on('click','#anchor-driver-a, #anchor-customer-a', function(e){
		e.preventDefault();
		var id  = $(this).attr('href'),top = $(id).offset().top;
        $('body,html').animate({scrollTop: top-100}, 2500);
    });
	// $(document).on('hover','#ageOutputId_driver', function(){
	// 	if ($("#ageInputId_driver").val()>1){
	// 		$("#lower1").hide()
	// 	}
	// });
	$(document).on('click','#search_driver_mainpage', function(e){
		e.preventDefault();
		var id  = $(this).attr('href'),top = $(id).offset().top;
        $('body,html').animate({scrollTop: top-100}, 2500);
		result=new Object();
		if($('#voditel-ot').attr('city-id')!=''){
			result.city_id=$('#voditel-ot').attr('city-id');
		}

		if($("#radio-1").is(':checked') && $("#radio-2").is(':checked')){
			result.driver_specialization='both';
		}else if($("#radio-1").is(':checked')){
			result.driver_specialization='in_city';
		}else if($("#radio-2").is(':checked')){
			result.driver_specialization='intercity';
		}

		if($("#radio-10").is(':checked')){
			result.load_type_top=true;
		}
		if($("#radio-11").is(':checked')){
			result.load_type_rear=true;
		}
		if($("#radio-9").is(':checked')){
			result.load_type_side=true;
		}

		result.max_ton=$("#ageOutputId_driver").html();
		result.offset=0;
		result.limit= 3;
		json_str=JSON.stringify(result);

		$.ajax({
			url: '/api/v1/get_more_drivers/',
			type: 'POST',
			data: {
				json_str
			},
		}).done(function(data){
			result=data;
			if(result.result){
				$("#main-driver-div").html("<h2 class='wow fadeIn'>Каталог Водителей</h2>");
				for (var i = 0; i < result.list_driver.length; i++){
					if(result.list_driver[i].loaders){
						loaders='есть';
					}else{
						loaders='нет';
					}
					if(result.list_driver[i].car_type=='single'){
						car_type='Одиночка';
					}else if(result.list_driver[i].car_type=='semitrailer'){
						car_type='Полуприцеп';
					}else{
						car_type='Одиночка+прицеп'
					}
					temp=''
					if(result.list_driver[i].load_type_top){
						temp=temp+("<i title='Верхняя' class='fa fa-arrow-up' aria-hidden='true'></i>");
					}else if(result.list_driver[i].load_type_rear){
						temp=temp+("<i title='Задняя' class='fa fa-arrow-down' aria-hidden='true'></i>");
					}else if(result.list_driver[i].load_type_side){
						temp=temp+("<i title='Боковая' class='fa fa-arrow-right' aria-hidden='true'></i>");
					}
					if(result.list_driver[i].avatar==''){
						avatar='/app/views/images/noavatar.png'
					}else{
						avatar=result.list_driver[i].avatar
					}
					$("#main-driver-div").append("<div class='row flow-offset-1 product-info driver-div'><div class='col-lg-4 text-center text-md-left wow fadeInLeft'><img src='"+avatar+"' width='370' height='274' alt=''></div><div class='col-lg-8'><div class='product-info-body text-center text-md-left'><h3><a href='/get_one_driver/"+result.list_driver[i].id+"/'>"+result.list_driver[i].name+"</a></h3><p class='heading-4 product-info-price'><a href='#'>Автомобиль: "+result.list_driver[i].car_name+"</a></p><p>Рейтинг: "+result.list_driver[i].rating+"</p><div class='row row-sm-justify'><div class='col-sm-4 col-lg-3 text-sm-left'><h6 class='text-capitalize'>Стоимость</h6><ul class='product-info-list text-sm-left '><li>По городу: "+result.list_driver[i].rate+" руб/ч</li></ul></div><div class='col-sm-4 col-lg-5 text-sm-left'><h6 class='text-capitalize preffix-1'>Параметры автомобиля</h6><ul class='product-info-list text-sm-left preffix-1'><li>Грузоподъемность: "+result.list_driver[i].capacity+" тонн</li><li>Способ загрузки: "+temp+"</li><li>Тип: "+car_type+"</li><li>Грузчики: "+loaders+"</li></ul></div><div class='col-sm-4 col-md-3'><div class='inline-block'><ul class='product-info-list-1'><li><div class='box'><div class='box__left box__middle'><span class='icon icon-xs icon-default fa-thumbs-o-up'></span></div><div class='box__body box__middle'>Всего заказов: 20</div></div></li></ul><a href='/get_one_driver/"+result.list_driver[i].id+"/' class='btn btn-md btn-sunglow wow fadeInRight' id='"+result.list_driver[i].id+"'>Подробнее</a></div></div></div></div></div></div><hr>");
				}	
			}else{

			}
		});
	});

	$(document).on('click','#search_customer_mainpage', function(e){
		e.preventDefault();
		var id  = $(this).attr('href'),top = $(id).offset().top;
        $('body,html').animate({scrollTop: top-100}, 2500);
		result=new Object();
		if($('#customer-ot').attr('city-id')!=''){
			result.start_id=$('#customer-ot').attr('city-id');
		}
		if($("#radio-3").is(':checked') && $("#radio-4").is(':checked')){
			result.order_type ='both';
		}else if($("#radio-3").is(':checked')){
			result.order_type ='in_city';
		}else if($("#radio-4").is(':checked')){
			result.order_type ='intercity';
		}

		result.start_date=$("#startdatetask").val();
		if($("#enddatetask").val()!=''){
			result.finish_date=$("#enddatetask").val();
		}

		if($("#radio-5").is(':checked') && $("#radio-6").is(':checked')){
			result.payment_type="both";
		}else if($("#radio-5").is(':checked')){
			result.payment_type="fixed";
		}else if($("#radio-6").is(':checked')){
			result.payment_type="open";
		}

		if($("#radio-7").is(':checked') && $("#radio-8").is(':checked')){
			result.payment_method="both";
		}else if($("#radio-7").is(':checked')){
			result.payment_method="cash";
		}else if($("#radio-8").is(':checked')){
			result.payment_method="non_cash";
		}
		 

		result.max_ton=$("#ageOutputId_customer").html();
		result.offset=0;
		result.limit= 3;
		json_str=JSON.stringify(result);

		$.ajax({
			url: '/api/v1/get_more_orders/',
			type: 'POST',
			data: {
				json_str
			},
		}).done(function(data){
			result=data;
			if(result.result || result.result==true || result.result=='true' || result.return){
				$("#main-customer-div").html("")
				for (var i = 0; i < result.list_order.length; i++){
					temp=''
					if(result.list_order[i].comment.length>75){
						temp="...";	
					}else{
						temp=result.list_order[i].comment
					}
					if(result.list_order[i].finish_city.id == '' || result.list_order[i].finish_city.id == ''){
						city='По городу';
					}else{
						city=result.list_order[i].finish_city.name;
					}
					if(result.list_order[i].price==0 || result.list_order[i].price==''){
						price='По договоренности';
					}else{
						price=result.list_order[i].price;
					}

					$("#main-customer-div").append("<div class='col-sm-6 col-lg-4 wow fadeIn customer-div'><div class='order-1'><a href='#' class='order-price-1 heading-5'>"+price+"</a><div style='clear:both;'></div><a href='/get_one_order"+result.list_order[i].id+"/' class='order-title-1 heading-4'>"+result.list_order[i].start_city.name+"<br/>"+city+"</a><p class='order-caption-1'><span>"+result.list_order[i].cargo_name+"</span><br/>"+temp+"</p><div class='incenter'><a href='#' class='btn btn-md btn-sunglow wow fadeInRight marbot' id='"+result.list_order[i].id+"'>Подбробнее</a></div></div></div>");
				}	
			}else{

			}	
		});
	});

	//табы заказов
	$(document).on('click','.tabs_table', function(){
		this_item=$(this);
		$(".tabs_table").each(function(){
			$(this).removeClass("active")
		});
		this_item.addClass("active");
		$(".tables").each(function(){
			$(this).hide();
		});
		$("[name="+this_item.attr("data-id")+"]").show()
	});
});