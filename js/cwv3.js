/* global cwv3_params */
jQuery(document).ready(function($) {
	var wpdata = cwv3_params;
	//console.log(wpdata);
	var enter = $('#cw_enter_link');
	var exit = $('#cw_exit_link');
	
	if(wpdata.sd === true){
		$.colorbox(
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
			if( typeof(e) !== "undefined" ){
				e.preventDefault();
			}
			//console.log(e);
			$.post(wpdata.admin_url, {action: wpdata.action, nonce: wpdata.nonce, id: wpdata.id, method: 'enter'}, function(){
				if(cwv3_params.enter === "#"){
					$.colorbox.close();
				}else{
					window.location = wpdata.enter;
				}
			});
		});	
			
		exit.click(function(e){
			if( typeof(e) !== "undefined" ){
				e.preventDefault();
			}
			$.post(wpdata.admin_url, {action: wpdata.action, nonce: wpdata.nonce, id: wpdata.id, method: 'exit'}, function(){
				window.location = wpdata.exit;
			});
		});
	}
	
	
});