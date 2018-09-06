var simplevotemeaddvoteQueue = {};

function simplevotemeShowLoading(o) {
    var img =o.find('img');
    img.data('src', img.attr('src'));
    img.attr('src', simplevotemeLoading);

}

function simplevotemeaddvote(post_id, vote_selected, user_id, o) {
    o = jQuery(o);

    if (!simplevotemeaddvoteQueue.hasOwnProperty('p' + post_id)) {
        simplevotemeaddvoteQueue['p' + post_id] = true;
        simplevotemeShowLoading(o);

        return simplevotemeaddvoteajax(post_id, {
            action: 'simplevoteme_addvote',
            vote_selected: vote_selected,
            post_id: post_id,
            user_id: user_id
        }, o);
    }
}

function simplevotemeaddvotecompliment(compliment_id, vote_selected, user_id, o) {
    o = jQuery(o);

    if (!simplevotemeaddvoteQueue.hasOwnProperty('c' + compliment_id)) {
        simplevotemeaddvoteQueue['c' + compliment_id] = true;
        simplevotemeShowLoading(o);
        return simplevotemeaddvoteajax(compliment_id, {
            action: 'simplevoteme_compliments_addvote',
            vote_selected: vote_selected,
            compliment_id: compliment_id,
            user_id: user_id
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
            var simplevoteme = jQuery(linkid);

            if (result.success) {
                for(var option in result.data.votes) {
                    if(result.data.votes.hasOwnProperty(option)) {
                        var votes=result.data.votes[option];
                        var ul=simplevoteme.find('#gt_simplevoteme_votes_'+option);
                        ul.empty();
                        var u=0;
                        for(var user in votes) {
                            if (votes.hasOwnProperty(user)) {
                                var li=jQuery(document.createElement('li'));
                                li.append(votes[user]);
                                ul.append(li);
                                u++;
                            }

                        }
                        simplevoteme.find('#SimpleVoteMeVoteOption'+option).find('span.result').text(u);
                    }
                }
            }

            if (data.hasOwnProperty('post_id')) {
                delete simplevotemeaddvoteQueue['p' + data.post_id];
            } else {
                delete simplevotemeaddvoteQueue['c' + data.compliment_id];
            }
            var img=o.find('img');
            img.attr('src',img.data('src'));

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

            var options = $.extend({}, {
                form: jQuery('#simplevoteme')
            }, params);
            var countVotes = 0;
            var form;

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
                    var tr = jQuery('#' + $(this).data('vote'));
                    confirmAction('Eliminar una opción de voto, borrará los votos realizados por los usuarios. Esta acción no puede deshacerse.','Eliminar Opción de Voto',[
                        {
                            'text':'Confirmar y eliminar opción de voto',
                            'class':'button button-primary',
                            click:function() {

                                remove(tr);
                                $( this ).dialog( "close" );
                            }
                        },
                        {
                            'text':'Cancel',
                            click: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    ]);

                });

                $this.find('.gt_simplevoteme_vote_undo_remove').click(function (e) {
                    e.preventDefault();
                    var tr = jQuery('#' + $(this).data('vote'));
                    cancelRemove(tr);
                });


                $this.find('.gt_simplevoteme_custom_img_uploader').each(function () {

                    wpMediaUploader({
                        selector: $(this),
                        target: $(this),
                        image: $(this).parent().find('img')
                    });
                });

                form = jQuery(options.form);
                form.submit(function (e) {
                    wrapper.find('tr').each(function () {

                        $(this).find('input:not([type=hidden])').each(function() {
                           if ($(this).val()=='') {
                               $(this).addClass('error');
                           }else{
                               $(this).removeClass('error');
                           }
                        });

                    });
                    if ($(this).find('input.error').length) {
                        confirmAction('Debes completar todos los cambios','Opción de Votos',[
                            {
                                'text':'OK, Volver y reparar',
                                'class':'button button-primary',
                                click:function() {
                                    $( this ).dialog( "close" );
                                }
                            },
                        ]);
                        e.preventDefault();
                    }else{
                        return true;
                    }
                });


            }

            function confirmAction(message,title,buttons) {
                var $d=jQuery('#dialog-confirm');
                if (!$d.length) {
                    $d=jQuery(document.createElement('div'));
                }
                $d.html(message);
                $d.attr('title',title || 'Confirmar');
                $d.dialog({
                    resizable: false,
                    height: "auto",
                    width: 400,
                    modal: true,
                    buttons: buttons || {
                        "Delete all items": function() {
                            $( this ).dialog( "close" );
                        },
                        Cancel: function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
            }


            this.addNew = function ($args = []) {

                var tr = jQuery(document.createElement('tr'));
                wrapper.prepend(tr);

                var uuid=generateUUID();
                var imgTagName = "gt_simplevoteme_custom_thumb_$name";
                var $inputId = jQuery('<input name="gt_simplevoteme_options_votes[' + uuid + '][id]" id="GtSimplevotemeVote' + uuid + 'Id" value="Nuevo">');
                var $inputName = jQuery('<input name="gt_simplevoteme_options_votes[' + uuid + '][name]" id="GtSimplevotemeVote' + uuid + 'Name" value="">');
                var $inputLabel = jQuery('<input name="gt_simplevoteme_options_votes[' + uuid + '][label]" id="GtSimplevotemeVote' + uuid + 'Label" value="">');
                var removeButton = jQuery('<a href="#remove" id="GtSimplevotemeVoteRemove" class="gt_simplevoteme_vote_remove button button-link-delete"><span class="dashicons dashicons-trash"></span></a>');

                removeButton.click(function (e) {
                    e.preventDefault();
                    tr.remove();
                });

                var undoRemoveButton = jQuery('<a href="#undo-remove" id="GtSimplevotemeVoteUndoRemove" class="gt_simplevoteme_vote_undo_remove button button-link-delete"><span class="dashicons dashicons-image-rotate"></span></a>');

                undoRemoveButton.click(function (e) {
                    e.preventDefault();
                    var tr = $(this).parent().parent();
                    cancelRemove(tr);
                });



                tr.append(jQuery(document.createElement('td')).append($inputId));
                tr.append(jQuery(document.createElement('td')).append($inputName));
                tr.append(jQuery(document.createElement('td')).append($inputLabel));

                var td = jQuery(document.createElement('td'));
                var image = jQuery(document.createElement('img'));
                image.css({width: '48px'});

                td.addClass("gt_simplevoteme_custom_img_uploader").attr('id', 'GtSimpleVoteMeCustomImgUploader-' + uuid);
                td.append(image);
                td.append('<input style="width: 70%" name="gt_simplevoteme_options_votes[' + uuid + '][custom_img]" class="gt_simplevoteme_custom_img_input"/>');
                tr.append(td);

                tr.append(jQuery(document.createElement('td')).append(removeButton).append(undoRemoveButton));

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
                vote.find('input').attr('disabled', 'disabled');


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
