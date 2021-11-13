var getPersonDevs = function(){
	$.ajax({  
		url:        '/get_person_devices',  
		type:       'POST',   
		dataType:   'html',  
		async:      true,  
		data:	   { sender: $('#form_sender').val()},
		
		success: function(data, status) {   
			$('#devices').html(data);      
		},  
		error : function(xhr, textStatus, errorThrown) {  
			if(xhr.status==404 && xhr.responseText=="unauthorized"){
				location.reload();
			}
			else{
			alert('Ajax request failed.');  
			}  
		}  
	});  
}

$(document).ready(
	function(){
		$('#form_submit').on('click', handler3);
		$('#form_sender').on('change', getPersonDevs);
		$('#form_sender').change();
	}
);

var handler3 = function checkSelectedDevices(event){
	if($("input[name*='dev_checkbox']:checked").length>0) {
		$('#form_destination_loc').prop('disabled', false); 
		return true;
	}
	else{
		alert('Nie zaznaczono żadnego urządzenia');
		return false;
	}
}