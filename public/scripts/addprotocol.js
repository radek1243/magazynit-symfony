$(document).ready(
	function(){
		$('#form_type').on('change', load);
		$('#form_add').on('click', reserve);
		$('#form_submit').on('click', enableLocation);
		$('#form_type').change();
	}
);

var load = function getDevices(event){
	$.ajax({  
		url:        '/get_efficient_devices',  
		type:       'POST',   
		dataType:   'html',  
		async:      true,  
		data:	   { type: $('#form_type').val()},
		
		success: function(data, status) {   
			var json = JSON.parse(data);
		   $('#devices').html(json['devices']);
		   $('#reserved').html(json['reserved']);      
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

var reserve = function(event){
	var arrayObjects = $("input[name*='res_checkbox']:checked");
	if(arrayObjects.length>0){
		var array = [];
		var counter=0;
		arrayObjects.each(function(){
			array[counter] = $(this).val();
			counter++;
		});
		$.ajax({  
		   url:        '/reserve_devices',  
		   type:       'POST',   
		   dataType:   'html',  
		   async:      true,  
		   data:	   { checkboxes: array },
		   
		   success: function(data, status) {   
			  if(data=="true") $('#form_type').change();     
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
	else{
		alert('Nie zaznaczono żadnego urządzenia!');
	}
}

var enableLocation = function(event){
	var checks = $("input[name*='rem_checkbox']");
	checks.each(function(){
		$(this).prop('checked', true);
		//alert($(this).val());
	})
	$('#form_destination_loc').prop('disabled',false);
	return true;
}

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
           url:        '/unreserve_devices',  
           type:       'POST',   
           dataType:   'html',  
           async:      true,  
           data:	   { unreserve: array },
           
           success: function(data, status) {  
			    if(data=="true") $('#form_type').change();  
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
	else{
		alert('Nie zaznaczono żadnego urządzenia!');
	}
}

