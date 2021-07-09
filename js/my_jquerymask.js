$(document).ready(function() {
	//Mask
	var maskBehavior = function (val) {
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

	//Ademir Pinto - 26/05/2017
	$('.horariomask').mask('99:99');

	//22/07/2017
	$('.floatmask').mask('#' + '.' + '##0' + ',' + (new Array(parseInt(2 || 2, 10) + 1).join('0')), {
		'reverse': true,
		'maxlength': false
	}).on('keyup', function(e) {
		//var target = e.target,
        //position = target.selectionStart;

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
			//target.selectionEnd = position;
		} else {
			//24/04/2017
			//this.value = '0,00';
		}
	});
});