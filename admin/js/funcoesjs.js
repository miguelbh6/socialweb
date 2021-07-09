function loadingPageHide() {
	var element = document.getElementById('idloadingpage');
	if (typeof(element) != undefined && typeof(element) != null && typeof(element) != 'undefined') {
		element.style.display = "none";
	}
}

function inputMaiusculo(objinput, e) {
	if ((e.keyCode == 16) || (e.keyCode == 35) || (e.keyCode == 37) || (e.keyCode == 39)) {
		return;
	}

	if (e.target.type != 'text') {
		return;
	}

	var target = e.target,
	position = target.selectionStart;
	objinput.val(objinput.val().toUpperCase());
	target.selectionEnd = position;
}

function inputMinusculo(objinput, e) {
	if ((e.keyCode == 16) || (e.keyCode == 35) || (e.keyCode == 37) || (e.keyCode == 39)) {
		return;
	}

	if (e.target.type != 'text') {
		return;
	}

	var target = e.target,
	position = target.selectionStart;
	objinput.val(objinput.val().toLowerCase());
	target.selectionEnd = position;
}

function Mascara(o) {
	if (o.value.length > 14)
		mascara(o, cnpj);
	else
		mascara(o, cpf);
	}

function mascara(o, f) {
	v_obj = o;
	v_fun = f;
	setTimeout("execmascara()", 1);
}

function execmascara() {
	v_obj.value = v_fun(v_obj.value);
}

function telefone(v) {
	v = v.replace( /\D/g , "");
	v = v.replace( /^(\d\d)(\d)/g , "($1) $2");
	v = v.replace( /(\d{4})(\d)/ , "$1-$2");
	return v;
}

function cpf(v) {
	v = v.replace( /\D/g , "");
	v = v.replace( /(\d{3})(\d)/ , "$1.$2");
	v = v.replace( /(\d{3})(\d)/ , "$1.$2");
	v = v.replace( /(\d{3})(\d{1,2})$/ , "$1-$2");
	return v;
}

function cep(v) {
	v = v.replace( /D/g , "");
	v = v.replace( /^(\d{2})(\d)/ , "$1.$2");
	v = v.replace( /(\d{3})(\d{1,2})$/ , "$1-$2");
	return v;
}

function cnpj(v) {
	v = v.replace( /\D/g , "");
	v = v.replace( /^(\d{2})(\d)/ , "$1.$2");
	v = v.replace( /^(\d{2})\.(\d{3})(\d)/ , "$1.$2.$3");
	v = v.replace( /\.(\d{3})(\d)/ , ".$1/$2");
	v = v.replace( /(\d{4})(\d)/ , "$1-$2");
	return v;
}

function formatReal(mixed) {
	var num = parseInt(parseFloat(mixed).toFixed(2).toString().replace(/[^\d]+/g, ''));
	var tmp = num + '';
	tmp = tmp.replace(/([0-9]{2})$/g, ",$1");
	if (tmp.length > 6)
		tmp = tmp.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");

	if (tmp.length == 1)
		tmp = '0,0' + tmp;

	if (tmp.length == 2)
		tmp = '0,' + tmp;

	if (tmp.length == 3)
		tmp = '0' + tmp;

	return tmp;
}

function formatFloat(num, casasDec, sepDecimal, sepMilhar) {
	//Ademir Pinto - 28/07/2017
	var strnum = num.toString();
	if (strnum.length == 0) {
		return '0,00';
	}
	if ((strnum.length == 1) && (strnum == '0')) {
		return '0,00';
	}
	if ((strnum.length == 4) && (strnum == '0,00')) {
		return '0,00';
	}
	if ((strnum.length >= 3) && (strnum.substr(-3, 1) == ',')) {
		return strnum;
	}

    if (num < 0) {
        num = -num;
        sinal = -1;
    } else
        sinal = 1;

    var resposta = "";
    var part = 0;

	if (casasDec == 2) {
		if (num == 0) {
			resposta = "0,00";
		} else {
			/*$.ajax({
			url: 'processajax.php?acao=formatfloat&num=' + num + '&time=' + $.now(),
			type: "get",
			async: false,
			dataType: "json",
			success: function(response) {
				if (response['success'] == true) {
					resposta = response['resultado'];
				} else {
					alert('problema na função de formatFloat - 1');
				}
			},
			error: function(response) {
				alert('problema na função de formatFloat - 2');
			}
			});*/

			//Ademir Pinto - 17/04/2017
			resposta = formatReal(num);
		}
	} else {
		if (num != Math.floor(num)) {
			part = Math.round((num - Math.floor(num)) * Math.pow(10, casasDec)).toString();

			while (part.length < casasDec)
				part = '0' + part;

			if (casasDec > 0) {
				resposta = sepDecimal + part;
				num = Math.floor(num);
			} else
				num = Math.round(num);
		}

		while (num > 0) {
			part = (num - Math.floor(num/1000) * 1000).toString();
			num = Math.floor(num/1000);
			if (num > 0)
				while (part.length < 3)
					part = '0' + part;
			resposta = part + resposta;
			if (num > 0)
				resposta = sepMilhar + resposta;
		}

		if (sinal < 0)
			resposta = '-' + resposta;

		var n = resposta.indexOf(",");

		if (n == -1)
		  resposta = resposta + ',00';

		if (n == 0)
		  resposta = '0' + resposta;

		if (resposta == ',00')
		  resposta = '0,00';
	}
	return resposta;
}