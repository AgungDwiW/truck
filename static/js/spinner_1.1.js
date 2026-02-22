function showSpinner(cancel= false){
	$(".spiner_bg").show();
	$(".spiner_container").show();
	$("#spinner_cancel").hide()
	if (cancel){
		$("#spinner_cancel").show()
	}
}
function hideSpinner(){
	$(".spiner_bg").hide();
	$(".spiner_container").hide();	
}

spinner_run = 1;
function spinnerCancel(){
	spinner_run = 0;
	hideSpinner();
}

window.onload = function () {
    const centeredDiv = document.getElementById('spiner_container');

    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY +(window.innerHeight/2);
        centeredDiv.style.top = `${scrollY}px`;
    });
};

