$(document).ready(function(){
	$('button').on("click", buttonClick);
	$('#form_sn').on('keypress', snKeypress);
});

var buttonClick = function(){
	if($(this).attr('id')!=='form_submit'){
		var length = $('input:checkbox:checked').length;				
		if(length==0) {
			alert('Nie zaznaczono żadnego urządzenia!');
			return false;
		}				
		else{
			if($(this).attr('id')==='form_change_desc'){
				if(length>1){
					alert('Zaznaczono więcej niż jedno urządzenie!');
					return false;
				}
				else{
					var desc = prompt('Podaj nowy opis urządzenia');
					if(desc===null) return false;
					$('#form_newdesc').val(desc);
				}				
			}
			$('#form_current_sn').val($('#form_sn').val());
			return true;
		}
	}
}


var snKeypress = function(event){
	if(event.keyCode===13){
		$('#form_submit').click();
		return false;
	}
}