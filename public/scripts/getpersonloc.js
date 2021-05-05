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
          //alert('Ajax request failed.');  
   		}  
	});  
}
$(document).ready(
	function(){
		$('#form_receiver').on('change', handler);	
		$('#form_receiver').change();
	}
);