$(document).ready(
	function(){
		$('button').on("click",
			function(event){
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
								$('#form_newdesc').val(desc);
							}
							
						}
						$('#form_current_sn').val($('#form_sn').val());
						return true;
					}
				}
			}
		)
	}
);
$(document).ready(
	function(){
		$('#form_sn').on('keypress', function(event){
			if(event.keyCode===13){
				$('#form_submit').click();
				return false;
			}
		})
	}
);