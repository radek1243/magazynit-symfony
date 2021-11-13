  $(document).ready(function(){   
     $("#form_type").on("change", devOnService);
     $('#form_type').change();  
  }); 
  var devOnService = function(){
  		$.ajax({  
           url:        '/devices_on_service',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { type: $('#form_type').val()},
           
           success: function(data, status) {   
              $('#devices').html(data);      
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