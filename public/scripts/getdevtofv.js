  $(document).ready(function(){   
     $("#form_type").on("change", devToFV);
     $('#form_type').change();  
  }); 
var devToFV = function(){
  		$.ajax({  
           url:        '/devices_to_fv',  
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