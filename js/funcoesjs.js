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

function getQueryVariable(variable) {
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function getIdade(dataNascimento) {
	var hoje = new Date();
	var arrayData = dataNascimento.split("/");
	var retorno = "";

	if (arrayData.length == 3) {
		var ano = parseInt(arrayData[2]);
		var mes = parseInt(arrayData[1]);
		var dia = parseInt(arrayData[0]);

		if (arrayData[0] > 31 || arrayData[1] > 12) {
			return retorno;
		}
		ano = (ano.length == 2) ? ano += 1900 : ano;
		var idade = (hoje.getYear()+1900) - ano;
		var meses = (hoje.getMonth() + 1) - mes;
		idade = (meses < 0 ) ? idade - 1 : idade;
		meses = (meses < 0 ) ? meses + 12 : meses;

		if (idade > 1) {
			var sidade  = idade + " anos";
		} else {
			var sidade  = idade + " ano";
		}

		if (meses > 1) {
			var smeses  = " e " + meses + " meses.";
		} else {
			var smeses  = " e " + meses + " mes.";
		}

		if (meses == 0) {
			var smeses = ".";
		}

		retorno = sidade + smeses;
	}
	return retorno;
}

function validaData(stringData) {
    /******** VALIDA DATA NO FORMATO DD/MM/AAAA *******/

    var regExpCaracter = /[^\d]/;     //Expressão regular para procurar caracter não-numérico.
    var regExpEspaco = /^\s+|\s+$/g;  //Expressão regular para retirar espaços em branco.

    if(stringData.length != 10) {
        alert('Data fora do padro DD/MM/AAAA');
        return false;
    }

    splitData = stringData.split('/');

    if(splitData.length != 3) {
        alert('Data fora do padrão DD/MM/AAAA');
        return false;
    }

    /* Retira os espaços em branco do início e fim de cada string. */
    splitData[0] = splitData[0].replace(regExpEspaco, '');
    splitData[1] = splitData[1].replace(regExpEspaco, '');
    splitData[2] = splitData[2].replace(regExpEspaco, '');

    if ((splitData[0].length != 2) || (splitData[1].length != 2) || (splitData[2].length != 4)) {
        alert('Data fora do padrao DD/MM/AAAA');
        return false;
    }

    /* Procura por caracter não-numérico. EX.: o "x" em "28/09/2x11" */
    if (regExpCaracter.test(splitData[0]) || regExpCaracter.test(splitData[1]) || regExpCaracter.test(splitData[2])) {
        alert('Caracter invalido encontrado!');
        return false;
    }

    dia = parseInt(splitData[0],10);
    mes = parseInt(splitData[1],10)-1; //O JavaScript representa o mês de 0 a 11 (0->janeiro, 1->fevereiro... 11->dezembro)
    ano = parseInt(splitData[2],10);

    var novaData = new Date(ano, mes, dia);

    /* O JavaScript aceita criar datas com, por exemplo, mês=14, porém a cada 12 meses mais um ano é acrescentado à data
         final e o restante representa o mês. O mesmo ocorre para os dias, sendo maior que o número de dias do mês em
         questão o JavaScript o converterá para meses/anos.
         Por exemplo, a data 28/14/2011 (que seria o comando "new Date(2011,13,28)", pois o mês é representado de 0 a 11)
         o JavaScript converterá para 28/02/2012.
         Dessa forma, se o dia, mês ou ano da data resultante do comando "new Date()" for diferente do dia, mês e ano da
         data que está sendo testada esta data é inválida. */
    if ((novaData.getDate() != dia) || (novaData.getMonth() != mes) || (novaData.getFullYear() != ano)) {
        //alert('Data Inválida!');
        return false;
    } else {
        //alert('Data OK!');
        return true;
    }
}

function processaElementosForm(dataArray, elements) {
	for (var i = 1; i < elements.length; i++) {
		var achou = false;
		for (t = 0; t < dataArray.length; t += 1)
			if (dataArray[t].name === elements[i].name)
				achou = true;

		if ((!achou) && (elements[i].type == 'radio') && (elements[i].checked)) {
			dataArray.push({name: elements[i].name, value: elements[i].value});
		}

		if ((!achou) && (elements[i].type == 'select-one')) {
			dataArray.push({name: elements[i].name, value: elements[i].value});
		}
	}
	return $.param(dataArray);
}

function dataAtualFormatada() {
    var data = new Date();
    var dia = data.getDate();
    if (dia.toString().length == 1)
      dia = "0"+dia;
    var mes = data.getMonth()+1;
    if (mes.toString().length == 1)
      mes = "0"+mes;
    var ano = data.getFullYear();
    return dia+"/"+mes+"/"+ano;
}