$(document).ready(
	function(){
		$('#form_type').on('change', function(EVENT){
			$.ajax({  
	           url:        '/adddevice',  
	           type:       'POST',   
	           dataType:   'text',  
	           async:      true,  
	           data:	   { type: $('#form_type').val()},
	           
	           success: function(data, status) {   
	              if(data=="true"){
	              	$('#form_invoicing').prop('checked', true);
	              }
	              else{
	              	$('#form_invoicing').prop('checked', false);
	              }      
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
		$('#form_type').change();
	}
);