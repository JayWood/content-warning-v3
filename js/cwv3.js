// JavaScript Document
jQuery(document).ready(function($) {
	var wpdata = cwv3_params;
	//console.log(wpdata);
	var enter = $('#cw_enter_link');
	var exit = $('#cw_exit_link');
	
	if(wpdata.sd == true){
		var cboxObj = $.colorbox(
			{
				scrolling:	false,
				overlayClose:	false,
				escKey:	false,
				inline:	true,
				href: '#cwv3_auth',
				initialWidth: '50%',
				maxWidth: '600px',
				loop: false,
				onLoad: function(){
					$('#cboxClose').remove();
				},
				className: 'cwv3_box',
				opacity:	cwv3_params.opacity
			}
		);
		
		enter.click(function(e){
			//console.log(e);
			$.post(wpdata.admin_url, {action: wpdata.action, nonce: wpdata.nonce, id: wpdata.id, method: 'enter'}, function(e){
				if(cwv3_params.enter == "#"){
					$.colorbox.close();
				}else{
					window.location = wpdata.enter;
				}
			});
		});	
			
		exit.click(function(e){
			$.post(wpdata.admin_url, {action: wpdata.action, nonce: wpdata.nonce, id: wpdata.id, method: 'exit'}, function(e){
				window.location = wpdata.exit;
			});
		});
	}
	
	
});