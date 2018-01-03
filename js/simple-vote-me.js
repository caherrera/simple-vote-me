function simplevotemeaddvote(postId, tipo, userID) {
    return simplevotemeaddvoteajax(postId, {
        action: 'simplevoteme_addvote',
        tipo: tipo,
        postid: postId,
        userid: userID
    });
}

function simplevotemeaddvotecompliment(complimentid, tipo, userID) {
    return simplevotemeaddvoteajax(complimentid, {
        action: 'simplevoteme_compliments_addvote',
        tipo: tipo,
        complimentid: complimentid,
        userid: userID
    });
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

