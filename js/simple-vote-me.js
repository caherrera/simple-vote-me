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

