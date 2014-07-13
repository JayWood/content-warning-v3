/* global cwv3_params */
jQuery(document).ready(function($) {
	var enter = $('#cw_enter_link');
	var exit = $('#cw_exit_link');
	
	if(cwv3_params.sd === "1"){
		$.colorbox(
			{
				scrolling:	false,
				overlayClose:	false,
				escKey:	false,
				inline:	true,
				href: '#cwv3_auth',
				maxWidth: '80%',
				loop: false,
				onLoad: function(){
					$('#cboxClose').remove();
				},
				className: 'cwv3_box',
				opacity:	cwv3_params.opacity
			}
		);
		
		enter.click(function(e){
			if( typeof(e) !== "undefined" ){
				e.preventDefault();
			}
			//console.log(e);
			$.post(cwv3_params.admin_url, {action: cwv3_params.action, nonce: cwv3_params.nonce, id: cwv3_params.id, method: 'enter'}, function(){
				if(cwv3_params.enter === "#"){
					$.colorbox.close();
				}else{
					window.location = cwv3_params.enter;
				}
			});
		});	
			
		exit.click(function(e){
			if( typeof(e) !== "undefined" ){
				e.preventDefault();
			}
			$.post(cwv3_params.admin_url, {action: cwv3_params.action, nonce: cwv3_params.nonce, id: cwv3_params.id, method: 'exit'}, function(){
				window.location = cwv3_params.exit;
			});
		});
	}
	
	
});