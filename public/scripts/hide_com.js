$(document).ready(function(){
    var errorDiv = $('#error');
    var comDiv = $('#communicate');
    if(errorDiv.length){
        errorDiv.delay(7000).slideUp(500);
    }
    else if(comDiv.length){
        comDiv.delay(7000).slideUp(500);
    }
});