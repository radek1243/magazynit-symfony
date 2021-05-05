$(document).ready(function(){
	$("button").on("click", function(event){
		if($(this).attr('name')==='confirm'){
			$.ajax({
				url: "/protocollist",
				type:	"POST",
				dataType:	'json',
				async:	true,
				data:	{ id: $(this).val(), name: $(this).attr('name')},
				
				success: function(data, status) {   
	              //alert(data['id']);
	              $(data['id']).html(data['response']);      
	           },  
	           error : function(xhr, textStatus, errorThrown) {  
	              alert('Błąd zatwierdzania protokołu');  
	           } 
			});
		}
	});	
});