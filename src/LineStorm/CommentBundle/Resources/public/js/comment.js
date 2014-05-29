
var contentCounts = contentCounts || {};

define(['jquery', 'cms_api'], function ($, api) {

    $(document).ready(function(){

        $commentBlock = $('.comment-block');

        // get the new comment form
        var loadCommentForm = function(){
            window.lineStorm.api.call($commentBlock.data('url-new'), {
                dataType: 'json',
                success: function(ob){
                    if(ob.form){
                        $newForm = $(ob.form);
                        $commentBlock.find('.comment-block-new').html($newForm);

                        $newForm.on('submit', function(e){
                            e.preventDefault();
                            e.stopPropagation();

                            window.lineStorm.api.saveForm($newForm, function(ob, status, xhr){
                                if(xhr.status === 200){
                                } else if(xhr.status === 201) {
                                    $commentBlock.find('.comment-block-comments').append(ob.html);
                                } else {
                                }
                            });

                            return false;
                        });
                    }
                }
            });
        };


        var loadComments = function(){
            // load current comments
            window.lineStorm.api.call($commentBlock.data('url-get'), {
                dataType: 'html',
                success: function(html){
                    $commentBlock.find('.comment-block-comments').html(html);
                    $commentBlock.find('.comment-block-new').find('textarea,input').val('');
                }
            });
            return false;
        };

        // init load comments
        loadCommentForm();
        loadComments();

        // bind comment refresh
        $('.comments-refresh').on('click', loadComments)
        $('.comments-refresh-form').on('click', loadCommentForm);

        // comment delete button
        $commentBlock.on('click', '.comment-delete', function(){
            if(confirm("Are you sure you want to delete this comment?")){
                var $this = $(this);
                window.lineStorm.api.call($this.data('url'), {
                    method: 'DELETE',
                    dataType: 'html',
                    success: function(html){
                        $this.closest('.comment-row').slideUp(1000, function(){
                            $(this).remove();
                        });
                    }
                });

            }
            return false;
        });


        $('.comment-block form').on('submit', function(e){
            e.preventDefault();
            e.stopPropagation();
            $('#FormErrors').slideUp(function(){ $(this).html(''); });
            window.lineStorm.api.saveForm($(this), function(on, status, xhr){
                if(xhr.status === 200){
                } else if(xhr.status === 201) {
                    window.location = on.location;
                } else {
                }
            }, function(e, status){
                if(e.status === 400){
                    if(e.responseJSON){
                        var errors = window.lineStorm.api.parseError(e.responseJSON.errors);
                        var str = '';
                        for(var i in errors){
                            if(errors[i].length)
                                str += "<p class=''><strong style='text-transform:capitalize;'>"+i+":</strong> "+errors[i].join(', ')+"</p>";
                        }
                        $('#FormErrors').html(str).slideDown();
                    } else {
                        alert(status);
                    }
                }
            });

            return false;
        });

        $('.page-form-delete').on('click', function(){
            if(confirm("Are you sure you want to permanently delete this page?")){
                window.lineStorm.api.call($(this).data('url'), {
                    method: 'DELETE',
                    success: function(o){
                        alert(o.message);
                        window.location = o.location;
                    }
                });
            }
        });

    });

});
