$(document).ready(
	function(){
		$('#form_type').on('change', sortedModelsByType);
		$('#form_type').change();
	}
);

var sortedModelsByType = function(event){
		$.ajax({  
		   url:        '/sorted_models_by_type',  
		   type:       'POST',   
		   dataType:   'json',  
		   async:      true,  
		   data:	   { type: $('#form_type').val()},
		   
		   success: function(data, status) {
				$('#div_model').html(data['html']);
				if(data['inv']=="true"){
					$('#form_invoicing').prop('checked', true);
				}
				else{
					$('#form_invoicing').prop('checked', false);
				}      
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