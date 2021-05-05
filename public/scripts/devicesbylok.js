  $(document).ready(function(){   
     $("#form_typ").on("change", function(event){  
        $.ajax({  
           url:        '/',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { typ: $('#form_typ').val()},
           
           success: function(data, status) {   
              $(devices).html(data);      
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
           }  
        });  
     });  
  });  
  $(document).ready(function(){
  		$.ajax({  
           url:        '/',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { typ: $('#form_typ').val()},
           
           success: function(data, status) {   
              $(devices).html(data);      
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
           }  
        });  

  });