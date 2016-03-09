// Valida la Fecha
function toDate()
{
	var sep = '/';
	var strDate = this.toString().split(sep);
	return new Date( strDate[2] , strDate[1]-1 , strDate[0] );
}

function ISO2Date()
{
	var sep = '-';
	var strDate = this.toString().split(sep);
	strDate = new Date( strDate[0] , strDate[1]-1 , strDate[2] );
	return strDate;
}

function validarDateSimple()
{
	var sep1 = '-';
	var sep2 = '/';
	
	var testObj=this.toString();
	
	if( !eval('testObj.regexp(/^\\d+['+sep1+sep2+']\\d+['+sep1+sep2+']\\d{2}|\\d{4}$/)') ){
		return false;
	}
	
	var testStr=testObj.split(sep1);
	if( testStr.length != 3 )
	{
		testStr=testObj.split(sep2);
		if( testStr.length != 3 )
		{
			return false;
		}
	}
	testStr[0] = Number( testStr[0] , 10 );
	testStr[1] = Number( testStr[1] , 10 ) - 1;
	testStr[2] = Number( testStr[2] , 10 );
	var daysArr = new Array;
	for (var i=0; i<12; i++)
	{
		if(i!=1){
			if( (i/2) == (Math.round(i/2)) ){
				if(i<=6){
					daysArr[i]="31";
				}else{
					daysArr[i]="30";
				}
			}else{
				if(i<=6){
	 				daysArr[i]="30";
				}else{
					daysArr[i]="31";
				}
			}
		}else{
			if((testStr[2]/4)==(Math.round(testStr[2]/4))){
				daysArr[i]="29";
			}else{
				daysArr[i]="28";
			}
		}
	}
	if( testStr[2].length == 2 ){
		if( testStr[2] > 69 ){
			testStr[2] += 1000;
		}else{
			testStr[2] += 2000;
		}
		//return false;
	}
	for(var i=0; i<12; i++)
	{
		if(testStr[1]==i){
			var setMonth=i;
			break;
		}
	}
	if( setMonth==undefined ){
		return false;
	}
	if( testStr[0] < 1 || testStr[0]>daysArr[setMonth] ){
		return false;
	}
	return true;
}

function validarISODateSimple()
{
	var sep = '-';
	var testObj=this.toString();
	var testStrISO=testObj.split(sep);
	var testStr = new Array;
	testStr[0] = testStrISO[2];
	testStr[1] = testStrISO[1];
	testStr[2] = testStrISO[0];
	
	if(testStr.length>3 || testStr.length<3)
	{
		return false;
	}
	testStr[0] = Number( testStr[0] , 10 );
	testStr[1] = Number( testStr[1] , 10 ) - 1;
	testStr[2] = Number( testStr[2] , 10 );
	var daysArr = new Array;
	for (var i=0; i<12; i++)
	{
		if(i!=1){
			if( (i/2) == (Math.round(i/2)) ){
				if(i<=6){
					daysArr[i]="31";
				}else{
					daysArr[i]="30";
				}
			}else{
				if(i<=6){
	 				daysArr[i]="30";
				}else{
					daysArr[i]="31";
				}
			}
		}else{
			if((testStr[2]/4)==(Math.round(testStr[2]/4))){
				daysArr[i]="29";
			}else{
				daysArr[i]="28";
			}
		}
	} 
	if(testStr[2]<1000){
		return false;
	}
	for(var i=0; i<12; i++)
	{
		if(testStr[1]==i){
			var setMonth=i;
			break;
		}
	}
	if( setMonth==undefined ){
		return false;
	}
	if( testStr[0] < 1 || testStr[0]>daysArr[setMonth] ){
		return false;
	}
	return true;
}

function validarDateMax( fecha_max )
{
	if( this.toString().dateSimple() )
	{
		var fecha = this.toString().split('/');
		fechaa = new Date( Number(fecha[2],10), Number(fecha[1],10)-1, Number(fecha[0],10) );
		
		fecha = fecha_max.toString().split('-');
		fecha_maxa = new Date( Number(fecha[0],10), Number(fecha[1],10)-1, Number(fecha[2],10) );
		
		if( fechaa.getTime() <= fecha_maxa.getTime() ){
			return true;
		}
	}
	return false;
}

function validarDateMin( fecha_min )
{
	if (this.toString().dateSimple())
	{
		var fecha = this.toString().split('/');
		fechaa = new Date( Number(fecha[2],10), Number(fecha[1],10)-1, Number(fecha[0],10) );
		
		fecha = fecha_min.toString().split('-');
		fecha_mina = new Date( Number(fecha[0],10), Number(fecha[1],10)-1, Number(fecha[2],10) );
		
		if( fechaa.getTime() >= fecha_mina.getTime() ){
			return true;
		}
	}
	return false;
}

function validarDateRange( fecha_min, fecha_max )
{
	if( this.toString().dateMin( fecha_min ) ){
		if(this.toString().dateMax( fecha_max ) ){
			return true;
		}
	}
	return false;
}

function validarDate( invalid_days )
{
	if( !this.toString().dateSimple() ) return false;
	
	var strDate = this.toString().toDate();
	
	if( arguments.length > 1 ){
		init_date = arguments[1];
		if( !init_date.dateSimple() ) return false;
		init_date = init_date.toDate();
		if( strDate.getTime() < init_date.getTime() ) return false;
	}
	if( arguments.length > 2 ){
		end_date = arguments[2];
		if( !init_date.dateSimple() ) return false;
		end_date = end_date.toDate();
		if( strDate.getTime() > end_date.getTime() ) return false;
	}
	strDay = strDate.getDay();
	strInvalidDays = invalid_days.split(',');
	for( i=0 ; i < strInvalidDays.length ; i++ )
	{
		if( strDay == strInvalidDays[i] ){
			return false;
		}
	}
	return true;
}

function validarISODate( strInvalidDays )
{
	if( !this.toString().dateisoSimple() ) return false;
	
	var strDate = this.toString().ISO2Date();
	
	if( validarISODate.arguments.length > 1 ){
		init_date = validarISODate.arguments[1];
		if( !init_date.dateisoSimple() ) return false;
		init_date = init_date.ISO2Date();
		if( strDate.getTime() < init_date.getTime() ) return false;
	}
	
	if( validarISODate.arguments.length > 2 ){
		end_date = validarISODate.arguments[2];
		if( !init_date.dateisoSimple() ) return false;
		end_date = end_date.ISO2Date();
		if( strDate.getTime() > end_date.getTime() ) return false;
	}
	
	var strDay = strDate.getDay();
	strInvalidDays = strInvalidDays.split(',');
	
	for( var i=0 ; i < strInvalidDays.length ; i++ )
	{
		if( strDay == strInvalidDays[i] ){
			return false;
		}
	}
	
	return true;
}

// Verifican Strings
function validarString( len_min, len_max )
{
	return ( this.toString().length >= len_min && this.toString().length <= len_max );
}
function validarStringSimple()
{
	return this.toString().length > 0;
}

// Verifican Numeros
function validarInt( int_min, int_max )
{
	if( /^\d+$/.test( this.toString() ) ){
		return ( parseInt( this.toString(), 10 ) >= int_min && parseInt( this.toString(), 10 ) <= int_max );
	}else{
		return false;
	}
}

function validarIntSimple()
{
	return /^\d+$/.test( this.toString() );
}
function validarNumberSimple()
{
	return /^\d+\.?\d*$/.test( this.toString() );
}

function validarNumber( int_min, int_max )
{
	if( /^\d+\.?\d*$/.test( this.toString() ) ){
		return ( parseFloat( this.toString() ) >= int_min && parseFloat( this.toString() ) <= int_max );
	}else{
		return false;
	}
}

// Verifican con Regexp
function validarRegexp( regexp )
{
	return regexp.test( this.toString() );
}

function validarDecimal( int, dec )
{
	return eval( '/^\d{1,'+int+'}\.\d{1,'+dec+'}$/.test( this.toString() )' );
}

function validarEmail( )
{
	return /^[-._a-z0-9]+\@[-_a-z0-9]+\.[-._a-z0-9]+$/i.test( this.toString() );
}

function validarEqual( val )
{
	return this.toString() == eval('document.forms[0].'+val+'.value') ? true : false;
}

function validarRadio()
{
	radios = box.getElementsByName( campo.name );
	for( j=0; radios.length; j++){
		if( radios[j].selected ){
			return true;
		}
	}
	return false;
}

String.prototype.toDate = toDate;
String.prototype.ISO2Date = ISO2Date;
String.prototype.strSimple = validarStringSimple;
String.prototype.str = validarString;
String.prototype.equal = validarEqual;
String.prototype.intSimple = validarIntSimple;
String.prototype.int = validarInt;
String.prototype.numberSimple = validarNumberSimple;
String.prototype.number = validarNumber;
String.prototype.decimal = validarDecimal;
String.prototype.regexp = validarRegexp;
String.prototype.dateSimple = validarDateSimple;
String.prototype.date = validarDate;
String.prototype.dateMin = validarDateMin;
String.prototype.dateMax = validarDateMax;
String.prototype.dateRange = validarDateRange;
String.prototype.dateisoSimple = validarISODateSimple;
String.prototype.dateiso = validarISODate;
String.prototype.emailSimple = validarEmail;
String.prototype.radioSimple = validarRadio;

var formSubmited = false;
var box;
var campo;
					
function validate( box1 ) {
	var allow_multiple_submition = arguments[1] ? true : false;
	box = box1;
	if( !allow_multiple_submition && formSubmited ) {
		alert('El formulario ya ha sido enviado, aguarde por favor.');
		return false;
	}
	if( box.tagName == 'FORM' )
	{
		var inputs = box.elements;
	} else {
		var inputs = box.all;
	}
	
	
	var strValidar;
	var validar_tmp;
	var validar_tipo;
	var validar_msg;
	
	for( var i = 0; i < inputs.length; i++ )
	{
		campo = inputs[i];
		
		strValidar = ( campo.validar ? campo.validar : campo.validate );
		if( strValidar ){
			// Conprueba la sentencio IF()
			validar_tmp = strValidar.split( ':', 2);
			if( validar_tmp[0].substr(0,3) == 'if(' ){
				condicion = /^if\((.*)\)$/i.exec( validar_tmp[0] );
				if( !eval(condicion[1]) ){
					continue;
				}
				strValidar = /^if\(.*\):(.*)$/i.exec( strValidar );
				strValidar = strValidar[1];
			}
			if( campo.type == 'radio' )
			{
				radio = false;
				radios = document.getElementsByName(campo.name);
				for(cont=0; cont < radios.length; cont++){
					if(radios[cont].checked==true)
					{
						radio = true;
					}
				}
				if( !radio ){
					if( strValidar == 'auto' ) strValidar = 'Debe seleccionar alguna de las opciones.';
					alert( strValidar );
					return false;
				}
			}
			else
			{
				// Conprueba la sentencia EqualThan()
				validar_tmp = strValidar.split( ':', 2);
				if( validar_tmp[0].substr(0,3) == 'equalthan(' ){
					condicion = /^equalthan\((.*)\)$/i.exec( validar_tmp[0] );
					campo2 = document.getElementById( condicion[1] );
					alert( campo2.value );
					if( campo.value == campo2.value ){
						continue;
					}
					strValidar = /^equalthan\(.*\):(.*)$/i.exec( strValidar );
					strValidar = strValidar[1];
				}
				
				// Conprueba la sentencio LessThan()
				validar_tmp = strValidar.split( ':', 2);
				if( validar_tmp[0].substr(0,3) == 'lessthan(' ){
					condicion = /^lessthan\((.*)\)$/i.exec( validar_tmp[0] );
					campo2 = document.getElementById( condicion[1] );
					if( campo.value < campo2.value ){
						continue;
					}
					strValidar = /^lessthan\(.*\):(.*)$/i.exec( strValidar );
					strValidar = strValidar[1];
				}
				
				// Conprueba la sentencio GreatTo()
				validar_tmp = strValidar.split( ':', 2);
				if( validar_tmp[0].substr(0,3) == 'greatthan(' ){
					condicion = /^greatthan\((.*)\)$/i.exec( validar_tmp[0] );
					campo2 = document.getElementById( condicion[1] );
					if( campo.value > campo2.value ){
						continue;
					}
					strValidar = /^greatthan\(.*\):(.*)$/i.exec( strValidar );
					strValidar = strValidar[1];
				}
				
				// Comprueba la sentencia NOREQUIRED
				validar_tmp = strValidar.split( ':' , 2 );
				if( validar_tmp[0] == 'norequired' || validar_tmp[0] == 'notrequired' ){
					if( campo.value == '' ){
						continue;
					}else{
						validar_tmp = strValidar.split( ':', 3 );
						validar_tipo = validar_tmp[1];
						validar_msg = validar_tmp[2];
					}
				}else{
					validar_tipo = validar_tmp[0];
					validar_msg = validar_tmp[1];
				}
								
				if( validar_tipo.substr( validar_tipo.length - 1 ) != ')' ){
					validar_tipo = validar_tipo+'Simple()';
				}
				
				if( !eval( 'campo.value.'+validar_tipo ) ){
					if( validar_msg == 'auto' ) validar_msg = 'El dato ingresado es inválido.';
					alert( validar_msg );
					try
					{
						campo.focus();
						campo.select();
					}catch(all){}
					return false;
				}
			}
		}
	}
	formSubmited = true;
	try{document.form.action.value=document.form.submitAction.value;}catch(all){};
	return true;
}