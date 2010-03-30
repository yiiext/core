$(function() {
    var addCommentForm = $('#addCommentForm');
    $('a.reply, .postNewComment').click(function(){
        $(this).after(addCommentForm);
        $('#comment_parent_id').val($(this).attr('data-id'));
    });
});