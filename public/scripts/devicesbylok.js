  $(document).ready(function(){
      $("#form_typ").on("change", devFromLoc);
      $('#form_typ').change();
  });

  var devFromLoc = function(){
   $.ajax({  
      url:        '/devices_from_loc',  
      type:       'POST',   
      dataType:   'html',  
      async:      true,  
      data:	   { typ: $('#form_typ').val()},
      
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