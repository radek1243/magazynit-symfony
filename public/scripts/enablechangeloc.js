$(document).ready(
	function(){
		$('#form_enable_change_loc').on('click', handler2);
	}
);

var handler2 = function enableLocChange(event){
	$('#form_destination_loc').prop('disabled', false);
}