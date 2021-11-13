$(document).ready(function(){
	$("button").on("click", confirmProt);	
});

var confirmProt = function(){
	if($(this).attr('name')==='confirm'){
		$.ajax({
			url: "/confirm_protocol",
			type:	"POST",
			dataType:	'json',
			async:	true,
			data:	{ id: $(this).val(), name: $(this).attr('name')},
			
			success: function(data, status) {   
			  //alert(data['id']);
			  $(data['id']).html(data['response']);      
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