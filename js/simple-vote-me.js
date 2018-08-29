var simplevotemeaddvoteQueue = {};

function simplevotemeShowLoading(o) {
    o = jQuery(o);
    o.find('img').attr('src', simplevotemeLoading);


}

function simplevotemeaddvote(postId, tipo, userID, o) {
    if (!simplevotemeaddvoteQueue.hasOwnProperty('p' + postId)) {
        simplevotemeaddvoteQueue['p' + postId] = true;
        simplevotemeShowLoading(o);

        return simplevotemeaddvoteajax(postId, {
            action: 'simplevoteme_addvote',
            tipo: tipo,
            postid: postId,
            userid: userID
        }, o);
    }
}

function simplevotemeaddvotecompliment(complimentid, tipo, userID, o) {
    if (!simplevotemeaddvoteQueue.hasOwnProperty('c' + complimentid)) {
        simplevotemeaddvoteQueue['c' + complimentid] = true;
        simplevotemeShowLoading(o);
        return simplevotemeaddvoteajax(complimentid, {
            action: 'simplevoteme_compliments_addvote',
            tipo: tipo,
            complimentid: complimentid,
            userid: userID
        }, o);
    }
}

function simplevotemeaddvoteajax(id, data, o) {
    jQuery.ajax({
        type: 'POST',
        url: gtsimplevotemeajax.ajaxurl,
        data: data,
        success: function (result, textStatus, XMLHttpRequest) {

            var linkid = '#simplevoteme-' + id;
            var c = jQuery("<div id=result9999>" + result + '</div>');

            jQuery(linkid).find('#gt_simplevoteme_votes_positives').html(c.find('#gt_simplevoteme_votes_positives').html());
            jQuery(linkid).find('#gt_simplevoteme_votes_neutrals').html(c.find('#gt_simplevoteme_votes_neutrals').html());
            jQuery(linkid).find('#gt_simplevoteme_votes_negatives').html(c.find('#gt_simplevoteme_votes_negatives').html());

            jQuery(linkid).find('.good span.result').html(c.find('.good span.result').html());
            jQuery(linkid).find('.neutro span.result').html(c.find('.neutro span.result').html());
            jQuery(linkid).find('.bad span.result').html(c.find('.bad span.result').html());

            jQuery(linkid).find('.good img').attr('src', c.find('.good img').attr('src'));
            jQuery(linkid).find('.neutro img').attr('src', c.find('.neutro img').attr('src'));
            jQuery(linkid).find('.bad img').attr('src', c.find('.bad img').attr('src'));


            if (data.hasOwnProperty('postid')) {
                delete simplevotemeaddvoteQueue['p' + data.postid];
            } else {
                delete simplevotemeaddvoteQueue['c' + data.complimentid];
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
                wrapperList: null,
                buttons: jQuery(this).find('>span')
            }, params);
            if (options.wrap && !$(this).find('#gt_simplevoteme_votes').length) {

                var div = jQuery(document.createElement('div'));
                div.attr('id', 'gt_simplevoteme_votes');
                jQuery(this).append(div);
                div.append(jQuery(this).parent().find('ul.gt_simplevoteme_votes_list'));
            }
            options.wrapperList = $(this).find('#gt_simplevoteme_votes');

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
                    options.buttonActive = jQuery(this);

                    if (options.listActive.children().length) {
                        if (jQuery('.simplevotemeWrapper').length == 1) {
                            options.wrapperList.css('margin-left', (options.wrapperList.parent().width() - options.wrapperList.width()) / 2);
                        }
                        options.wrapperList.show();
                    } else {
                        wrapper.trigger('mouseleave');
                        return;
                    }
                    options.listActive.slideDown().addClass('active');
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
                    options.wrapperList.fadeOut();


                }
            );

        });


    };

}(jQuery));

(function ($) {

    $.fn.GtSimpleVotemeAdmin = function (params) {
        this.each(function () {
            var _this = this;
            var $this = $(this);
            var wrapper;

            var options = $.extend({}, {}, params);
            var countVotes = 0;

            function init() {
                if ($this.hasClass('gt_simplevoteme_admin_structure'))
                    return;

                wrapper = $this.find('.gt_simplevoteme_table > tbody');
                $this.addClass('gt_simplevoteme_admin_structure');
                $this.find('.gt_simplevoteme_add_new').click(function (e) {
                    e.preventDefault();
                    _this.addNew();
                });

                $this.find('.gt_simplevoteme_vote_remove').click(function (e) {
                    e.preventDefault();
                    var tr = jQuery('#'+$(this).data('vote'));
                    remove(tr);
                });

                $this.find('.gt_simplevoteme_vote_undo_remove').click(function (e) {
                    e.preventDefault();
                    var tr = jQuery('#'+$(this).data('vote'));
                    cancelRemove(tr);
                });


                $this.find('.gt_simplevoteme_custom_img_uploader').each(function () {

                    wpMediaUploader({
                        selector: $(this),
                        target: $(this),
                        image: $(this).parent().find('img')
                    });
                });
            }


            this.addNew = function ($args = []) {


                var imgTagName = "gt_simplevoteme_custom_thumb_$name";
                var $inputName = jQuery('<input name="gt_simplevoteme_vote[' + _this.countVotes + '][name]" id="GtSimplevotemeVote' + _this.countVotes + 'Name" value="">');
                var $inputLabel = jQuery('<input name="gt_simplevoteme_vote[' + _this.countVotes + '][label]" id="GtSimplevotemeVote' + _this.countVotes + 'Label" value="">');
                var removeButton = jQuery('<a href="#remove" id="GtSimplevotemeVoteRemove" class="gt_simplevoteme_vote_remove button button-link-delete"><span class="dashicons dashicons-trash"></span></a>');

                removeButton.click(function(e){
                    e.preventDefault();
                    var tr = $(this).parent().parent();
                    remove(tr);
                });

                var undoRemoveButton = jQuery('<a href="#undo-remove" id="GtSimplevotemeVoteUndoRemove" class="gt_simplevoteme_vote_undo_remove button button-link-delete"><span class="dashicons dashicons-image-rotate"></span></a>');

                undoRemoveButton.click(function(e){
                    e.preventDefault();
                    var tr = $(this).parent().parent();
                    cancelRemove(tr);
                });
                var tr = jQuery(document.createElement('tr'));

                tr.append('<td>Nuevo</td>');
                tr.append(jQuery(document.createElement('td')).append($inputName));
                tr.append(jQuery(document.createElement('td')).append($inputLabel));

                var td = jQuery(document.createElement('td'));
                var image = jQuery(document.createElement('img'));
                image.css({width: '48px'});

                td.addClass("gt_simplevoteme_custom_img_uploader").attr('id', 'GtSimpleVoteMeCustomImgUploader-' + generateUUID());
                td.append(image);
                td.append('<input style="width: 70%" name="custom_img" class="gt_simplevoteme_custom_img_input"/>');
                tr.append(td);

                tr.append(jQuery(document.createElement('td')).append(removeButton).append(undoRemoveButton));

                wrapper.prepend(tr);
                wpMediaUploader({selector: td, target: td, image: image});


            };

            function generateUUID() { // Public Domain/MIT
                var d = new Date().getTime();
                if (typeof performance !== 'undefined' && typeof performance.now === 'function') {
                    d += performance.now(); //use high-precision timer if available
                }
                return 'xxxxxxxx'.replace(/[xy]/g, function (c) {
                    var r = (d + Math.random() * 16) % 16 | 0;
                    d = Math.floor(d / 16);
                    return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                });
            }

            function wpMediaUploader(params) {
                jQuery.wpMediaUploader($.extend({}, {
                    selector: $this,
                    target: wrapper, // The class wrapping the textbox
                    uploaderTitle: 'Select or upload image', // The title of the media upload popup
                    uploaderButton: 'Set image', // the text of the button in the media upload popup
                    multiple: false, // Allow the user to select multiple images
                    buttonText: 'Upload image', // The text of the upload button
                    buttonClass: '.gt_simplevoteme_custom_img_link button button-primary', // the class of the upload button
                    previewSize: '150px', // The preview image size
                    modal: false, // is the upload button within a bootstrap modal ?
                    buttonStyle: {},

                }, params));
            }


            function remove(vote) {
                vote.addClass('to_remove');
                vote.find('input[type=hidden].to_remove').val('1');
                vote.find('.gt_simplevoteme_custom_img_link').hide();
                vote.find('input').attr('disabled','disabled');


            }

            function cancelRemove(vote) {
                vote.removeClass('to_remove');
                vote.find('input[type=hidden].to_remove').val('');
                vote.find('.gt_simplevoteme_custom_img_link').show();
                vote.find('input').removeAttr('disabled');
            }

            init();

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
