  $(document).ready(function(){   
     $("#form_dev_type").on("change", function(event){  
        $.ajax({  
           url:        '/devices_on_service',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { type: $('#form_dev_type').val()},
           
           success: function(data, status) {   
              $('#devices').html(data);      
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
           }  
        });  
     });  
  }); 
  $(document).ready(function(){
  		$.ajax({  
           url:        '/devices_on_service',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { type: $('#form_dev_type').val()},
           
           success: function(data, status) {   
              $('#devices').html(data);      
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
           }  
        });  
  });