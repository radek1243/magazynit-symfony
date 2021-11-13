  $(document).ready(function(){   
     $("#form_submit").on("click", devBySN);  
     $("#form_submit").click();
  });   
  
   var devBySN = function(){
      if($('#form_sn').val()!=''){
         $.ajax({  
            url:        '/devices_by_sn',  
            type:       'POST',   
            dataType:   'html',  
            async:      true,  
            data:	   { sn: $('#form_sn').val()},
            
            success: function(data, status) {   
               $(devices).html(data);      
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
   }