  $(document).ready(function(){   
     $("#form_typ").on("change", function(event){  
        $.ajax({  
           url:        '/homepage',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { typ: $('#form_typ').val(), loc: $('#form_location').val()},
           
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
     $("#form_location").on("change", function(event){  
        $.ajax({  
           url:        '/homepage',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { typ: $('#form_typ').val(), loc: $('#form_location').val()},
           
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
           url:        '/homepage',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { typ: $('#form_typ').val(), loc: $('#form_location').val()},
           
           success: function(data, status) {   
              $(devices).html(data);      
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
           }  
        });  

  });