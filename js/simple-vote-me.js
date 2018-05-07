var simplevotemeaddvoteQueue = {posts: {}, compliment: {}};

function simplevotemeaddvote(postId, tipo, userID, o) {
    if (!simplevotemeaddvoteQueue.posts.hasOwnProperty(postId)) {
        simplevotemeaddvoteQueue.posts[postId] = true;
        o = jQuery(o);
        o.html('<img src="' + simplevotemeLoading + '">');

        return simplevotemeaddvoteajax(postId, {
            action: 'simplevoteme_addvote',
            tipo: tipo,
            postid: postId,
            userid: userID
        });
    }
}

function simplevotemeaddvotecompliment(complimentid, tipo, userID, o) {
    if (!simplevotemeaddvoteQueue.compliment.hasOwnProperty(complimentid)) {
        simplevotemeaddvoteQueue.compliment[complimentid] = true;
        o = jQuery(o);
        o.html('<img src="' + simplevotemeLoading + '">');
        return simplevotemeaddvoteajax(complimentid, {
            action: 'simplevoteme_compliments_addvote',
            tipo: tipo,
            complimentid: complimentid,
            userid: userID
        });
    }
}

function simplevotemeaddvoteajax(id, data) {
    jQuery.ajax({
        type: 'POST',
        url: gtsimplevotemeajax.ajaxurl,
        data: data,
        success: function (data, textStatus, XMLHttpRequest) {

            var linkid = '#simplevoteme-' + id;
            jQuery(linkid).html('');
            jQuery(linkid).append(data);
        },
        error: function (MLHttpRequest, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}


(function ($) {

    $.fn.simplevotemeShowVotes = function (params) {

        this.each(function () {
            var options = $.extend({}, {
                wrap: false,
                listActive: null,
                buttonActive: null,
                buttons: jQuery(this).find('>span')
            }, params);
            if (options.wrap && !$(this).find('#gt_simplevoteme_votes').length) {

                var div = jQuery(document.createElement('div'));
                div.attr('id', 'gt_simplevoteme_votes');
                jQuery(this).append(div);
                div.append(jQuery(this).parent().find('ul.gt_simplevoteme_votes_list'));
            }
            var wrapper = jQuery(this);
            wrapper.find(options.buttons).mouseover(function () {
                var key = jQuery(this).data('key');
                if (!key) {
                    if (jQuery(this).hasClass('good')) {
                        key = 'positives';
                    } else if (jQuery(this).hasClass('bad')) {
                        key = 'negatives'
                    } else if (jQuery(this).hasClass('neutro')) {
                        key = 'neutrals';
                    }
                }
                if (!jQuery(this).hasClass('active')) {

                    if (options.buttonActive) {
                        options.buttonActive.removeClass('active');
                    }
                    if (options.listActive) {
                        options.listActive.removeClass('active').hide();
                    }
                    options.listActive = jQuery(wrapper).find('#gt_simplevoteme_votes_' + key);
                    options.listActive.slideDown().addClass('active');
                    options.buttonActive = jQuery(this);
                    options.buttonActive.addClass('active');

                }

            });
            jQuery(this).mouseleave(function () {

                    if (options.listActive) {
                        options.listActive.slideUp().removeClass('active');
                    }
                    options.listActive = null;
                    if (options.buttonActive) {
                        options.buttonActive.removeClass('active');
                    }
                    options.buttonActive = null;



                }
            );

        });


    };

}(jQuery));

jQuery(document).ready(function () {

    if (jQuery('.simplevotemeWrapper').length) {
        jQuery('.gt_simplevoteme.categorychecklist').remove();
        jQuery('.simplevotemeWrapper').simplevotemeShowVotes({wrap: true});

    } else {

        jQuery('.gt_simplevoteme.categorychecklist').show();
        jQuery('#gt_simplevoteme_votes').simplevotemeShowVotes({buttons: '.gt_simplevoteme_resume_div'});
    }


});
