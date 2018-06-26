jQuery( document ).on( 'click', '.like-wrapper > a', function(e) {

	e.preventDefault();

	//wordpress post id 
	const post_id = jQuery(this).data('id');//get data-id attribute value

	//console.log('ElementThatClicked ' + jQuery(this).attr('data-id'));//data-id value 

	//use post_id value as class value of <span> 
	const spanElem = jQuery(this).find('.' + post_id);
 	//console.log('spanElemtext ' + spanElem.text());
 	const spanElemVal = spanElem.text();

 	//parseInt(spanElemVal);
 	//var textLike = jQuery(this).text();	
 	//console.log('textLike ' + textLike);
 	//console.log('spanElemVal ' + spanElemVal);

 	//run value is 0
 	if ( parseInt(spanElemVal) === 0 ) {

 		//console.log('Will run only when like value is 0');

 		//change the text 'Like' to 'Liked'
	 	jQuery(this).contents().filter(function(){ 
		  return this.nodeType !== 0; 
		})[0].nodeValue = "Liked" 			
	 } 
	 //console.log('security ' + security);
	 //console.log('ajax_public_handle.ajax_nonce ' + ajax_public_handle.ajax_nonce);
	jQuery.ajax({
		url : ajax_public_handle.ajaxurl,//wp_localize_script
		type : 'post',  
		data : {
			action : 'tga_ajax_public_handler', 
			post_id : post_id,
			security: ajax_public_handle.ajax_nonce 
		}, 
		success : function( response ) { 
			jQuery(spanElem).html( response );
			console.log('response ' + response);// 7			
		}


	});//jQuery.ajax

	//return false; 

})
