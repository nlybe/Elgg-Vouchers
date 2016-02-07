$( document ).ready(function() {
	t_show();
});

function t_show(msingle, mseries, mqr) {
	var code = $('input:radio[name=code_type]:checked').val();
	
	var code_single = document.getElementById('code');
	var code_series = document.getElementById('code_end');
	var code_end_note = document.getElementById('more_info_code_end');
	var code_qr = document.getElementById('code_qr_image');
	var howmany = document.getElementById('howmany');
	var code_message = document.getElementById('code_message');
	
    if (code=='code_single') {
        code_single.style.display = "inline-block";
        code_series.style.display = "none";
        code_end_note.style.display = "none";
        code_qr.style.display = "none";
        howmany.style.display = "inline-block";
        $("#code_message").text(msingle);
        return true;
    }
    if (code=='code_series') {
        code_single.style.display = "inline-block";
        code_series.style.display = "inline-block";
        code_end_note.style.display = "inline-block";
        code_qr.style.display = "none";
        howmany.style.display = "none";
        $("#code_message").text(mseries);        
        return true;
    }
    if (code=='code_qr') {
        code_single.style.display = "none";
        code_series.style.display = "none";
        code_end_note.style.display = "none";
        code_qr.style.display = "inline-block";
        howmany.style.display = "inline-block";
        $("#code_message").text(mqr);
        return true;
    }    
    
    return false;
}

function enable_code_end(status)	// obsolette
{
	document.voucherpost.howmany.disabled = status;
	status=!status;	
	document.voucherpost.code_end.disabled = status;
}
