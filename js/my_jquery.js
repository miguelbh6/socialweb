function setrelogio(){
	var momentoAtual = new Date();

    var hora = momentoAtual.getHours();
    var minuto = momentoAtual.getMinutes();
    var segundo = momentoAtual.getSeconds();

	if (segundo <= 9){
	   segundo = "0" + segundo;
	};

	if (minuto <= 9){
	   minuto = "0" + minuto;
	};

	if (hora <= 9){
	   hora = "0" + hora;
	};

    horaImprimivel = hora + ":" + minuto + ":" + segundo;
    $("#relogio").html(horaImprimivel);
	setTimeout("setrelogio()", 1000);
}

//Remap jQuery to $
(function($){})(window.jQuery);

$(document).ready(function() {
	$.ajaxSetup({
		cache: false
	});

	$('body').append('<div id="ajaxBusy"><p><img src="imagens/loading2.gif"></p></div>');

	$.extend($.ui.dialog.prototype.options.position, { my: "center top", at: "center top+150" });

	initMoneyMask();

	//Desabilita Mouse Direito
	//$(document).bind('contextmenu', function (e) {
	//	e.preventDefault();
	//	//alert('Opção não autorizada.');
	//});

	//Desabilita CTRL + P - Impressão
	/*$(document).keydown(function(event) {
		if (event.ctrlKey || event.metaKey) {
			var tecla = String.fromCharCode(event.which).toLowerCase();
			if (tecla == 'p') {
				event.preventDefault();
				save(event);
				return false;
			}
		}
	});*/

	//Desabilita a Opção de PrintScreen
	/*$(window).keyup(function(event){
		if (event.keyCode == 44) {
			event.preventDefault();
			save(event);
			return false;
		}
	});*/

	$('#ajaxBusy').css({
		display:"none",
		margin:"0px",
		paddingLeft:"0px",
		paddingRight:"0px",
		paddingTop:"0px",
		paddingBottom:"0px",
		position:"absolute",
		left:"50%",
		top:"60%",
		width:"auto"
	});

	$('#ajaxBusy').css('zIndex',9999);

	$('#idloadingpage').css('zIndex',9999);

	$(document).ajaxStart(function() {
	  $('#ajaxBusy').show();
	}).ajaxStop(function(){
	  $('#ajaxBusy').hide();
	});

	$('#idnewform').submit(function() {
		//...
	});

	$('#btnsubmitwait').on('click',function() {
		//$(this).val('Aguarde...').attr('disabled','disabled').css("background-color","#9b9b9b");
		$(this).attr('disabled','disabled').css("background-color","#9b9b9b");
		$(this.form).submit();
	});

	//http://select2.org
	if ($.fn.select2) { $.fn.select2.defaults.set("theme", "bootstrap"); }
	$('select').each(function(){
		if ($.fn.select2) {

			var render = true;
			if ($(this).attr('boolselect2')) {
				if (($(this).attr('boolselect2') == '0') || ($(this).attr('boolselect2') == 'false')) {
					render = false;
				}
			}

			if ($(this).hasClass("select2-hidden-accessible")) {
				render = false;
			}

			if (render) {
				var length = $(this).children('option').length;
				if (length > 5) {
					$(this).select2();
				}
			}
		}
	});

	$('#btnsalvarnovowait').on('click',function() {
		var strbtnsalvar = $('#btnsalvarnovowait').val();

		var action = $('#idnewform').attr('action') + '&btnsalvar=' + strbtnsalvar;
		$('#idnewform').attr('action', action);

		//$('#btnsalvarnovowait').val('Aguarde...').css("background-color","#9b9b9b");
		$('#btnsalvarnovowait').css("background-color","#9b9b9b");

		$('#btnsalvarnovowait').attr('disabled','disabled').css("background-color","#9b9b9b");
		$('#btnsalvarsairwait').attr('disabled','disabled').css("background-color","#9b9b9b");
		$('#btnsalvarwait').attr('disabled','disabled').css("background-color","#9b9b9b");
		$(this.form).submit();
	});

	$('#btnsalvarsairwait').on('click',function() {
		var strbtnsalvar = $('#btnsalvarsairwait').val();

		var action = $('#idnewform').attr('action') + '&btnsalvar=' + strbtnsalvar;
		$('#idnewform').attr('action', action);

		//$('#btnsalvarsairwait').val('Aguarde...').css("background-color","#9b9b9b");
		$('#btnsalvarsairwait').css("background-color","#9b9b9b");

		$('#btnsalvarnovowait').attr('disabled','disabled').css("background-color","#9b9b9b");
		$('#btnsalvarsairwait').attr('disabled','disabled').css("background-color","#9b9b9b");
		$('#btnsalvarwait').attr('disabled','disabled').css("background-color","#9b9b9b");
		$(this.form).submit();
	});

	$('#btnsalvarwait').on('click',function() {
		var strbtnsalvar = $('#btnsalvarwait').val();

		var action = $('#idnewform').attr('action') + '&btnsalvar=' + strbtnsalvar;
		$('#idnewform').attr('action', action);

		//$('#btnsalvarwait').val('Aguarde...').css("background-color","#9b9b9b");
		$('#btnsalvarwait').css("background-color","#9b9b9b");

		$('#btnsalvarnovowait').attr('disabled','disabled').css("background-color","#9b9b9b");
		$('#btnsalvarsairwait').attr('disabled','disabled').css("background-color","#9b9b9b");
		$('#btnsalvarwait').attr('disabled','disabled').css("background-color","#9b9b9b");
		$(this.form).submit();
	});

	//$(document).ajaxError(function(e, xhr, settings, exception) {
	//	alert('error in: ' + settings.url + ' \n'+'error:\n' + exception );
	//});

	//Mask
	$.getScript("js/my_jquerymask.js")
		.done(function() {
			//alert('yay, all good, do something');
		})
		.fail(function() {
			if (arguments[0].readyState == 0) {
				alert('Erro ao carregar "js/my_jquerymask.js"');
			}
			else {
				alert('Erro ao carregar "js/my_jquerymask.js"' + ' \n' + ' \n' + arguments[2]['message']);
			}
	});

	/*var maskBehavior = function (val) {
		return val.replace(/\D/g, '').length === 11 ? '(00)00000-0000' : '(00)0000-00009';
	},
	options = {
		onKeyPress: function(val, e, field, options) {
			field.mask(maskBehavior.apply({}, arguments), options);
		}
	};

	$('.competenciamask').mask('00/0000');
	$('.datemask').mask('00/00/0000');
	$('.cepmask').mask('99.999-999');
	$('.cpfmask').mask('000.000.000-00', {reverse: true});
	$('.cnpjmask').mask('00.000.000/0000-00', {reverse: true});
	$('.telefonemask').mask('(00)0000-0000');
	//$('.floatmask').mask("#.##0,00", {reverse: true, maxlength: false});
	$('.integermask').mask('0#');
	$('.celularmask').mask(maskBehavior, options);
	$('.ceimask').mask('00.000.00000/00');

	//19/04/2017
	$('.floatmask').mask('#' + '.' + '##0' + ',' + (new Array(parseInt(2 || 2, 10) + 1).join('0')), {
		'reverse': true,
		'maxlength': false
	}).on('keyup', function () {
		var val = this.value;
		if (val) {
			if (val.length <= parseInt(2 || 2, 10)) {
				while (val.length < parseInt(2 || 2, 10)) {
					val = '0' + val;
				}
				val = '0' + ',' + val;
			} else {
				var parts = val.split(',');
				parts[0] = parts[0].replace(/^0+/, '');
				if (parts[0].length === 0) {
					parts[0] = '0';
				}
				val = parts.join(',');
			}
			this.value = val;
		} else {
			//24/04/2017
			//this.value = '0,00';
		}
	});*/

	if (document.getElementById('relogio') != null){
		setrelogio();
	}
});

function setSymbol(v) {
	if (false) {
		if (v.substr(0, 2) != 'R$') return 'R$'+v;
	}
	return v;
}

function formatReal_(v) {
	v = v.replace('R$','');

	var strCheck = '0123456789';
	var len = v.length;
	var a = '', t = '', neg='';

	if(len!=0 && v.charAt(0)=='-'){
		v = v.replace('-','');
		if(false){
			neg = '-';
		}
	}

	if (len==0) {
		if (!true) return t;
		t = '0.00';
	}

	for (var i = 0; i<len; i++) {
		if ((v.charAt(i)!='0') && (v.charAt(i)!=',')) break;
	}

	for (; i<len; i++) {
		if (strCheck.indexOf(v.charAt(i))!=-1) a+= v.charAt(i);
	}

	var n = parseFloat(a);
	n = isNaN(n) ? 0 : n/Math.pow(10,2);
	t = n.toFixed(2);

	i = 2 == 0 ? 0 : 1;
	var p, d = (t=t.split('.'))[i].substr(0,2);
	for (p = (t=t[0]).length; (p-=3)>=1;) {
		t = t.substr(0,p)+'.'+t.substr(p);
	}

	return (2>0)
		? setSymbol(neg+t+','+d+Array((2+1)-d.length).join(0))
		: setSymbol(neg+t);
}

function initMoneyMask() {
	$(".newfloatmask").maskMoney({thousands:'.', decimal:',', allowZero:true, defaultZero:true});

	var moneyFields = $("input.newfloatmask");

	$.each(moneyFields, function() {
		$(this).val(formatReal_($(this).val()));
    });

    $('input.newfloatmask').bind('blur', function () {
    	$(this).val(formatReal_($(this).val()));
    });
}