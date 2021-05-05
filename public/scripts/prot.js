  $(document).ready(function(){   
     $("#form_prot_id").on("change", function(event){  
        $.ajax({  
           url:        '/addprotperson',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { protid: $('#form_prot_id').val()},
           
           success: function(data, status) {   
              $(persons).html(data);      
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
           }  
        });  
     });  
  });  
  $(document).ready(function(){
  		$.ajax({  
           url:        '/addprotperson',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { protid: $('#form_prot_id').val()},
           
           success: function(data, status) {   
              $(persons).html(data);      
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
           }  
        });  

  });
  $(document).ready(function(){   
     $("#form_submit").on("click", function(event){  
     	//alert('a');
        $('#form_protid').val($('#form_prot_id').val());
     });  
  }); 