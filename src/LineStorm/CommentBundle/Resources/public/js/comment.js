define(['jquery', 'bootstrap', 'cms_api'], function($, bs, api){

    // get the new comment form
    var _load = function($commentBlock){
            var
                $commentBlockNew = $commentBlock.find('.comment-block-new'),
                $commentBlockComments = $commentBlock.find('.comment-block-comments'),

                _loadCommentForm = function(){
                    api.call($commentBlock.data('url-new'), {
                        dataType: 'json',
                        success: function(ob){
                            if(ob.form){
                                var $newForm = $(ob.form);
                                $commentBlockNew.html($newForm);

                                $newForm.on('submit', function(e){
                                    e.preventDefault();
                                    e.stopPropagation();

                                    api.saveForm($newForm, function(ob, status, xhr){
                                        if(xhr.status === 200){
                                        } else if(xhr.status === 201) {
                                            $commentBlockComments.append(ob.html);
                                            $commentBlockNew.empty();
                                            _loadCommentForm();
                                        } else {
                                        }
                                    });

                                    return false;
                                });
                            }
                        }
                    });
                },

                _loadComments = function(){
                    // load current comments
                    api.call($commentBlock.data('url-get'), {
                        dataType: 'html',
                        success: function(html){
                            $commentBlockComments.html(html);
                        }
                    });
                    return false;
                }
            ;

            _loadCommentForm();
            _loadComments();

            // bind comment refresh
            $('.comments-refresh').on('click', _loadComments);
            $('.comments-refresh-form').on('click', _loadCommentForm);



            $commentBlock.on('click', '.comment-reply', function(){
                var $this = $(this);
                var $comment = $this.closest('.comment-row');
                api.call($this.data('url'), {
                    dataType: 'json',
                    success: function(ob){
                        if(ob.form){
                            var $newForm = $(ob.form);
                            $comment.after($newForm);

                            $newForm.on('submit', function(e){
                                e.preventDefault();
                                e.stopPropagation();

                                api.saveForm($newForm, function(ob, status, xhr){
                                    if(xhr.status === 200){
                                    } else if(xhr.status === 201) {
                                        $comment.find('.media-body').append(ob.html);
                                        $newForm.remove();
                                        $commentBlockNew.empty();
                                        _loadCommentForm();
                                    } else {
                                    }
                                });

                                return false;
                            });
                        }
                    }
                });
            });

            // comment delete button
            $commentBlock.on('click', '.comment-delete', function(){
                if(confirm("Are you sure you want to delete this comment?")){
                    var $this = $(this);
                    api.call($this.data('url'), {
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


            $commentBlock.find('form').on('submit', function(e){
                e.preventDefault();
                e.stopPropagation();
                $('#FormErrors').slideUp(function(){ $(this).html(''); });
                api.saveForm($(this), function(on, status, xhr){
                    if(xhr.status === 200){
                    } else if(xhr.status === 201) {
                        window.location = on.location;
                    } else {
                    }
                }, function(e, status){
                    if(e.status === 400){
                        if(e.responseJSON){
                            var errors = api.parseError(e.responseJSON.errors);
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

            $commentBlock.find('.page-form-delete').on('click', function(){
                if(confirm("Are you sure you want to permanently delete this page?")){
                    api.call($(this).data('url'), {
                        method: 'DELETE',
                        success: function(o){
                            alert(o.message);
                            window.location = o.location;
                        }
                    });
                }
            });


            return {
                comments: _loadComments,
                form: _loadCommentForm
            };
        }
    ;



    return {
        load: _load
    };
});
