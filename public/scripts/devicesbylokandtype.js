  $(document).ready(function(){   
     $("#form_current_loc").on("change", devByTypeFromLoc);  
     $("#form_current_type").on("change", devByTypeFromLoc);
     $('#form_current_loc').change();
  });  

  var devByTypeFromLoc = function(event){
      $.ajax({  
         url:        '/devices_by_type_from_loc',  
         type:       'POST',   
         dataType:   'html',  
         async:      true,  
         data:	   { typ: $('#form_current_type').val(), loc: $('#form_current_loc').val()},
         
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