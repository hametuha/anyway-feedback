jQuery(document).ready(function($){
	$('.afb_container').find('a').click(function(e){
		var endpoint = this.href;
		var data = {
			action: "anyway_feedback",
			object_id: $(this).nextAll("input[name=object_id]").val(),
			post_type: $(this).nextAll('input[name=post_type]').val(),
			nonce: $(this).nextAll("input[name=nonce]").val(),
			class_name: $(this).attr('class') 
		};
		$.post(
			endpoint,
			data,
			function(response){
				if(response.success){
					$('.afb_container a, .afb_container input').remove();
					$('.afb_container .message').addClass('success').text(response.message);
				}else{
					$('.afb_container a, .afb_container input').remove();
					$('.afb_container .message').addClass('error').text(response.message);
				}
			}
		);
		return false;
	});
});
