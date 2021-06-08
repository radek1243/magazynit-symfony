  $(document).ready(function(){   
     $("#form_location").on("change", devByTypeFromLoc);  
     $("#form_typ").on("change", devByTypeFromLoc);
     $('#form_location').change();
  });  

  var devByTypeFromLoc = function(event){
      $.ajax({  
         url:        '/devices_by_type_from_loc',  
         type:       'POST',   
         dataType:   'html',  
         async:      true,  
         data:	   { typ: $('#form_typ').val(), loc: $('#form_location').val()},
         
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