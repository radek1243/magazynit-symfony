$(document).ready(
	function(){
		$('#form_person').on('change', handler);
		$('#form_person').change();
	}
);
var handler = function(event){
	$.ajax({  
       url:        '/get_person_history',  
       type:       'POST',   
       dataType:   'html',  
       async:      true,  
       data:	   { person: $('#form_person').val()},
       
       success: function(data, status) {   
           $('#devices').html(data);    
       },  
       error : function(xhr, textStatus, errorThrown) {  
          alert('Ajax request failed.');  
   		}  
	});
}