  $(document).ready(function(){   
     $("#form_invoice_typ").on("change", function(event){  
        $.ajax({  
           url:        '/devices_to_fv',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { type: $('#form_invoice_typ').val()},
           
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
           url:        '/devices_to_fv',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { type: $('#form_invoice_typ').val()},
           
           success: function(data, status) {   
              $('#devices').html(data);      
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
           }  
        });  
  });