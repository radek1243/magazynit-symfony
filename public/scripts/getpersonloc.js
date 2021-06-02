var handler = function getPersonLoc(event){
	$.ajax({  
       url:        '/get_person_loc',  
       type:       'POST',   
       dataType:   'text',  
       async:      true,  
       data:	   { receiver: $('#form_receiver').val()},
       
       success: function(data, status) {   
       		$('#form_destination_loc').children('option').each( function(index){
       			if($(this).val()==data){
       				$(this).prop('selected',true);
       				return false;
       			}
       		})
       },  
       error : function(xhr, textStatus, errorThrown) {  
          alert('Ajax request failed.');  
   		}  
	});  
}
$(document).ready(
	function(){
		$('#form_receiver').on('change', handler);	
		$('#form_new_dev_type').on('change', handler3);
		$('#form_receiver').change();
		$('#form_new_dev_type').change();
	}
);

var handler3 = function getTypeModels(event){
		$.ajax({  
           url:        '/get_type_models',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { type: $('#form_new_dev_type').val(), label: "Model urządzenia: ", 
           				selectId: "form_new_dev_model", selectName: "form[new_dev_model]", indexValue: "id", indexName: "name"},
           
           success: function(data, status) {
           	  $('#div_model').html(data);  
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
       		}  
    	});
	};