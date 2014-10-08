jQuery(document).ready(function($){
	$('.afb_container').find('a').click(function(e){
		e.preventDefault();
		if(!$(this).parents('.afb_container').hasClass('afb_posted')){
			var endpoint = this.href;
			var data = {
				action: "anyway_feedback",
				object_id: $(this).nextAll("input[name=object_id]").val(),
				post_type: $(this).nextAll('input[name=post_type]').val(),
				nonce: $(this).nextAll("input[name=nonce]").val(),
				class_name: $(this).attr('class') 
			};
			var target = $(this).parent(".afb_container");
			$.post(
				endpoint,
				data,
				function(response){
					if(response.success){
						target.find('a, .input').remove();
						target.find('.message').addClass('success').text(response.message);
					}else{
						target.find('a, .input').remove();
						target.find('.message').addClass('error').text(response.message);
					}
				}
			);
		}
	});
});
