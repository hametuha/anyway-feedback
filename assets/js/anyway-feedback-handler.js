/*!
 * Helper script for Anyway Feedback
 *
 * @package AnywayFeedback
 */

/*global AFBP:true*/
/*global ga:true*/

jQuery(document).ready(function($){

    var cookieExists = function(postType, objectId){
            var cookie = $.cookie(cookieName(postType));
            if( cookie ){
                if( typeof cookie !== 'String' ){
                    cookie = cookie.toString();
                }
                cookie = cookie.replace(/[^0-9,]/g, '');
                cookie = cookie.split(',');
                return !(cookie.indexOf(objectId.toString()) < 0);
            }else{
                return false;
            }
        },
        cookieName = function(postType){
            return 'afb_' + ( 'comment' === postType ? 'comment' : 'post' );
        },
        saveCookie = function(postType, objectId){
            if( !cookieExists(postType, objectId) ){
                var cookie = $.cookie(cookieName(postType));
                if( cookie ){
                    if( typeof cookie !== 'String'){
                        cookie = cookie.toString();
                    }
                    cookie = cookie.split(',');
                }else{
                    cookie = [];
                }
                cookie.push(parseInt(objectId));
                $.cookie(cookieName(postType), cookie.join(','), {
                    expires: 365 * 2,
                    path: '/'
                });
            }
        },
        containers = $('.afb_container');
    // Check if use is already posted
    containers.each(function(index, container){
        var objectId = parseInt($(container).find('input[name=object_id]').val()),
            postType = $(container).find('input[name=post_type]').val();
        if( cookieExists(postType, objectId) ){
            $(container).find('a, input').remove();
            $(container).find('.message').text(AFBP.already);
            $(container).addClass('afb_posted');
        }
    });

    containers.on('click', 'a', function(e){
		e.preventDefault();
        // Check posted?
		if( !$(this).parents('.afb_container').hasClass('afb_posted') ){
			var endpoint = this.href,
			    data = {
                    action: "anyway_feedback",
                    object_id: parseInt($(this).nextAll("input[name=object_id]").val()),
                    post_type: $(this).nextAll('input[name=post_type]').val(),
                    class_name: $(this).attr('class')
                };
			var target = $(this).parent(".afb_container");
			$.post(
				endpoint,
				data,
				function( response ){
					if( response.success ){
						target.find('a, .input').remove();
						target.find('.message').addClass('success').text(response.message);
                        target.find('.status').text(response.status);
                        // Save cookie
                        saveCookie(data.post_type, data.object_id);
                        // Record Google Analytics
                        if( '1' === AFBP.ga ){
                            try{
                                ga('send', {
                                    hitType: 'event',
                                    eventCategory: 'anyway-feedback/' + ( data.post_type === 'comment' ? 'comment' : 'post' ),
                                    eventAction: data.class_name === 'good' ? 'positive' : 'negative',
                                    eventLabel: data.object_id,
                                    eventValue: 1
                                });
                            }catch(err){}
                        }
                        // Trigger event
                        target.trigger('feedback.afb', [( data.post_type === 'comment' ? 'comment' : 'post' ), data.object_id, data.class_name === 'good']);
					}else{
						target.find('a, .input').remove();
						target.find('.message').addClass('error').text(response.message);
					}
				}
			);
		}
	});
});
