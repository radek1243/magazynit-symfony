  $(document).ready(function(){   
     $("#form_submit").on("click", function(event){  
     	if($('#form_sn').val()!=''){
	        $.ajax({  
	           url:        '/finddevice',  
	           type:       'POST',   
	           dataType:   'html',  
	           async:      true,  
	           data:	   { sn: $('#form_sn').val()},
	           
	           success: function(data, status) {   
	              $(devices).html(data);      
	           },  
	           error : function(xhr, textStatus, errorThrown) {  
	              alert('Ajax request failed.');  
	           }  
	        });  
        }
     });  
  });   
  $(document).ready(function(){
  	if($('#form_sn').val()!=''){
  		$.ajax({  
           url:        '/finddevice',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { sn: $('#form_sn').val()},
           
           success: function(data, status) {   
              $(devices).html(data);      
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
           }  
        });  
	}
  });