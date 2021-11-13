$(document).ready(
	function(){
		$('button').on("click", btnClick);
	}
);
var btnClick = function(){
	if(hasChecked()){
		if($(this).attr('id')==='device_operation_change_desc'){
			if(length>1){
				alert('Zaznaczono więcej niż jedno urządzenie!');
				return false;
			}
			else{
				var desc = prompt('Podaj nowy opis urządzenia');
				if(desc===null) return false;
				$('#device_operation_newdesc').val(desc);
			}
			
		}
		//$('#form_current_loc').val($('#form_location').val());
		//$('#form_current_type').val($('#form_typ').val());
		return true;
	}
	else{
		alert('Nie zaznaczono żadnego urządzenia!');
	}
}
function hasChecked(){
	var length = $('input:checkbox:checked').length;				
	if(length==0) {
		return false;
	}	
	return true;
}