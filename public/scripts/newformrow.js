$(document).ready(
	function(){
		$('#form_add_new_dev').on('click', function(event){
			var type = $('#form_new_dev_type').val();
			var model = $('#form_new_dev_model').val();
			var sn = $('#form_new_dev_sn').val();
			var sn2 = $('#form_new_dev_sn2').val();
			if(sn==''){
				alert('Proszę podać numer seryjny!');
			}
			var counter = $('#table_new_devs').children('tbody').children('tr').length;
			var html="<tr><td>"+$('#form_new_dev_type option:selected').text()+"<input type='hidden' name='type["+counter+"]' value='"+type+"'></td>";
			html+="<td>"+$('#form_new_dev_model option:selected').text()+"<input type='hidden' name='model["+counter+"]' value='"+model+"'></td>";
			html+="<td>"+sn.toUpperCase()+"<input type='hidden' name='sn["+counter+"]' value='"+sn.toUpperCase()+"'></td>";
			html+="<td>"+sn2.toUpperCase()+"<input type='hidden' name='second_sn["+counter+"]' value='"+sn2.toUpperCase()+"'></td></tr>";
			$('#table_new_devs').append(html);
			$('#form_new_dev_sn').val(''); 
			$('#form_new_dev_sn2').val('');
		})
	}
);
$(document).ready(
	function(){
		$('#form_submit').on('click', handler2);
	}
)