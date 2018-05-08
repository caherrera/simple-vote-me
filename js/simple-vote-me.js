var simplevotemeaddvoteQueue = {};
function simplevotemeShowLoading(o){
    o = jQuery(o);
    o.find('img').attr('src',simplevotemeLoading);


}

function simplevotemeaddvote(postId, tipo, userID, o) {
    if (!simplevotemeaddvoteQueue.hasOwnProperty('p'+postId)) {
        simplevotemeaddvoteQueue['p'+postId] = true;
        simplevotemeShowLoading(o);

        return simplevotemeaddvoteajax(postId, {
            action: 'simplevoteme_addvote',
            tipo: tipo,
            postid: postId,
            userid: userID
        },o);
    }
}

function simplevotemeaddvotecompliment(complimentid, tipo, userID, o) {
    if (!simplevotemeaddvoteQueue.hasOwnProperty('c'+complimentid)) {
        simplevotemeaddvoteQueue['c'+complimentid] = true;
        simplevotemeShowLoading(o);
        return simplevotemeaddvoteajax(complimentid, {
            action: 'simplevoteme_compliments_addvote',
            tipo: tipo,
            complimentid: complimentid,
            userid: userID
        },o);
    }
}

function simplevotemeaddvoteajax(id, data,o) {
    jQuery.ajax({
        type: 'POST',
        url: gtsimplevotemeajax.ajaxurl,
        data: data,
        success: function (result, textStatus, XMLHttpRequest) {

            var linkid = '#simplevoteme-' + id;
            var c=jQuery("<div id=result9999>"+result+'</div>');

            jQuery(linkid).find('#gt_simplevoteme_votes_positives').html(c.find('#gt_simplevoteme_votes_positives').html());
            jQuery(linkid).find('#gt_simplevoteme_votes_neutrals').html(c.find('#gt_simplevoteme_votes_neutrals').html());
            jQuery(linkid).find('#gt_simplevoteme_votes_negatives').html(c.find('#gt_simplevoteme_votes_negatives').html());

            jQuery(linkid).find('.good span.result').html(c.find('.good span.result').html());
            jQuery(linkid).find('.neutro span.result').html(c.find('.neutro span.result').html());
            jQuery(linkid).find('.bad span.result').html(c.find('.bad span.result').html());

            jQuery(linkid).find('.good img').attr('src',c.find('.good img').attr('src'));
            jQuery(linkid).find('.neutro img').attr('src',c.find('.neutro img').attr('src'));
            jQuery(linkid).find('.bad img').attr('src',c.find('.bad img').attr('src'));


            if (data.hasOwnProperty('postid')) {
                delete simplevotemeaddvoteQueue['p'+data.postid];
            }else{
                delete simplevotemeaddvoteQueue['c'+data.complimentid];
            }
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
