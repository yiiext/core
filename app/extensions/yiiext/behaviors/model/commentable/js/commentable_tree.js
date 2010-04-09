$(function() {
    var addCommentForm = $('#addCommentForm');
    $('a.reply, .postNewComment').live('click', function(){
        $(this).after(addCommentForm);
        $('#comment_parent_id').val($(this).attr('data-id'));
        $("#addCommentForm textarea").val("");
    });

    $('#addCommentForm input[type=button], #addCommentForm input[type=submit]').click(function(){
        if(!$('#addCommentForm textarea').val()){
            alert("Text can't be empty.");
            return false;
        }
    });
});