$(document).ready(
	function(){
		$('#form_sender').on('change', function(event){
			$.ajax({  
	           url:        '/gen_prot_ret',  
	           type:       'POST',   
	           dataType:   'html',  
	           async:      true,  
	           data:	   { sender: $('#form_sender').val()},
	           
	           success: function(data, status) {   
	              $('#devices').html(data);      
	           },  
	           error : function(xhr, textStatus, errorThrown) {  
	              alert('Ajax request failed.');  
           		}  
        	});  
		})
	}
);
$(document).ready(
	function(){
		$.ajax({  
           url:        '/gen_prot_ret',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { sender: $('#form_sender').val()},
           
           success: function(data, status) {   
              $('#devices').html(data);      
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
       		}  
    	});  
	}
);
$(document).ready(
	function(){
		$('#form_submit').on('click', handler3);
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