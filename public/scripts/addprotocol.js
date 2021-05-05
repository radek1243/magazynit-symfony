$(document).ready(
	function(){
		$('#form_type').on('change', function(event){
			$.ajax({  
	           url:        '/addprotocol',  
	           type:       'POST',   
	           dataType:   'html',  
	           async:      true,  
	           data:	   { type: $('#form_type').val()},
	           
	           success: function(data, status) {   
	              $('#devices').html(data);      
	           },  
	           error : function(xhr, textStatus, errorThrown) {  
	              alert('Ajax request failed.');  
           		}  
        	});  
		})
	}
);
$(document).ready(
	function(){
		$.ajax({  
           url:        '/addprotocol',  
           type:       'POST',   
           dataType:   'json',  
           async:      true,  
           data:	   { type: $('#form_type').val(), load: true},
           
           success: function(data, status) {   
           	//alert(data);
              $('#devices').html(data['devices']);
              $('#reserved').html(data['reserved']);      
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
       		}  
    	});  
	}
);
$(document).ready(
	function(){
		$('#form_add').on('click', function(event){
			var arrayObjects = $("input[name*='res_checkbox']:checked");
			if(arrayObjects.length>0){
				var array = [];
				var counter=0;
				arrayObjects.each(function(){
					array[counter] = $(this).val();
					counter++;
				});
				$.ajax({  
		           url:        '/addprotocol',  
		           type:       'POST',   
		           dataType:   'html',  
		           async:      true,  
		           data:	   { checkboxes: array },
		           
		           success: function(data, status) {   
	           			$('#form_type').change();
		                $('#reserved').html(data);      
		           },  
		           error : function(xhr, textStatus, errorThrown) {  
		              alert('Ajax request failed.');  
	           		}  
	        	});
        	}
        	else{
        		alert('Nie zaznaczono żadnego urządzenia!');
        	}
		})
	}	
);
$(document).ready(
	function(){
		$('#form_submit').on('click', function(event){
			var checks = $("input[name*='rem_checkbox']");
			checks.each(function(){
				$(this).prop('checked', true);
				//alert($(this).val());
			})
			$('#form_destination_loc').prop('disabled',false);
			return true;
		})
	}
);
function unreserveClick(){
	var arrayObjects = $("input[name*='rem_checkbox']:checked");
	if(arrayObjects.length>0){
		var array = [];
		var counter=0;
		arrayObjects.each(function(){
			array[counter] = $(this).val();
			counter++;
		});
		$.ajax({  
           url:        '/addprotocol',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { unreserve: array },
           
           success: function(data, status) {   
       			$('#form_type').change();
                $('#reserved').html(data);     
           },  
           error : function(xhr, textStatus, errorThrown) {  
              alert('Ajax request failed.');  
       		}  
    	});
	}
	else{
		alert('Nie zaznaczono żadnego urządzenia!');
	}
}

