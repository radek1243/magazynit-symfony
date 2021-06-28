$(document).ready(
	function(){
		$('button').on("click", btnClick);
	}
);
var btnClick = function(){
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
		//$('#form_current_loc').val($('#form_location').val());
		//$('#form_current_type').val($('#form_typ').val());
		return true;
	}
}