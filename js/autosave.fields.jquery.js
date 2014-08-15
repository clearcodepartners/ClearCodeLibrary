/**
 * Created by mike on 8/2/14.
 */
function callback1(){ jQuery(this).removeClass('yellow').removeClass('yellow2').addClass('green'); jQuery(this).parents('.field').removeClass('yellow').removeClass('yellow2').addClass('green'); setTimeout(callback2.bind(this), 1000);}
function callback2(){ jQuery(this).removeClass('green'); jQuery(this).parents('.field').removeClass('green'); }
function callback3(){ jQuery(this).removeClass('yellow').removeClass('yellow2').addClass('red'); jQuery(this).parents('.field').removeClass('yellow').removeClass('yellow2').addClass('red'); }
jQuery(function($){
    $(window).load(function(){
        $('.login btn').click(function(){
            $(this).parents('form').submit();
        });
        $('.picker select').focusin(function(e){    $(this).parents('.picker').addClass('active');      });
        $('.picker select').focusout(function(e){   $(this).parents('.picker').removeClass('active');   });
        $('input, textarea').each(function(){ $(this).data('oldText', $(this).val()); });
        function listen_for_autosave($this){
            $this.find('select').change(function(e){
                var $this2 = $(this);
                $this2.parents('.picker').addClass('yellow');
                var seturl = "ajax/set/type/" + encodeURI($this.attr('edit')) + "/id/" + encodeURI($this.attr('edit_id')) + "/var/" + encodeURI($this2.attr('name'));
                $.ajax({
                    'url'       : seturl,
                    'type'      : "POST",
                    'data'      : { 'val' : $this2.val() },
                    'success'   : function(data){
                        setTimeout(callback1.bind($this2.parents('.picker')[0]), 1000);
                    },
                    'dataType': 'json',
                    'async': false,
                });
            });
            $this.find('input, textarea').focus(function(e){
                var $this2 = $(this);
                $this2.focusin(function(e){     $this2.data('oldText', $this2.val());   });
                $this.keyup(function(e){ if($this2.val() !== $this2.data('oldText')) $this2.parents('.field').addClass('yellow2'); else $this2.parents('.field').removeClass('yellow2'); });
                $this2.focusout(function(e){
                    if($this2.val() !== $this2.data('oldText')){
                        if($this2.attr('name') == '_title'){
                            $this2.parents('.field').removeClass('yellow2').addClass('yellow');
                            var seturl = "ajax/move/catid/"+$this2.parents('.category').attr('cat_id')+'/current/'+$this2.data('oldText')+"/new/"+$this2.val();
                            $.ajax({
                                'async'     : false,
                                'type'      : "POST",
                                'url'       : seturl,
                                'data'      : {},
                                'success'   : function(data){
                                    if(data.success){
                                        $this2.data('oldText', data.result.new_id).val(data.result.new_id);
                                        setTimeout(callback1.bind($this2[0]), 1000);
                                    }
                                    else setTimeout(callback3.bind($this2[0]), 1000);
                                },
                                'dataType': 'json',
                            });
                        }
                        else{
                            $this2.parents('.field').removeClass('yellow2').addClass('yellow');
                            var seturl = "ajax/set/type/" + encodeURI($this.attr('edit')) + "/id/" + encodeURI($this.attr('edit_id')) + "/var/" + encodeURI($this2.attr('name'));
                            $.ajax({
                                'url' : seturl,
                                'type'      : "POST",
                                'data' : { 'val' : $this2.val() },
                                'success' : function(data){
                                    if(data.success){
                                        $this2.data('oldText', data.result.val).val(data.result.val);
                                        setTimeout(callback1.bind($this2[0], $this2), 1000);
                                    }
                                    else setTimeout(callback3.bind($this2[0], $this2), 1000);
                                },
                                'dataType': 'json'
                            });
                        }
                    }
                    else $this2.parents('.field').removeClass('yellow2');
                });
            });
            $this.find('.btn.delete').click(function(){
                $catid = $(this).parents('.category').attr('cat_id');
                $obj = $(this).parents('.ajax_auto_save').find('[name="_title"]').val();
                var seturl = 'ajax/remove/catid/' + $catid + '/current/' + $obj;
                var cb = $(this).parents('.ajax_auto_save').attr('edit') + $(this).parents('.ajax_auto_save').attr('edit_id');
                $this2 = $(this);
                $.ajax({
                    'type'      : "POST",
                    'url' : seturl,
                    'data' : {},
                    'async' : false,
                    'success' : function(data){
                        if(data.success) $this2.parents('.ajax_auto_save').remove();
                    },
                    'dataType': 'json'
                });
            });
        }
        $('.ajax_auto_save').each(function(){ listen_for_autosave($(this)); });

        $('.category_add').click(function(e){
            var $category = $(this).parents('.category');
            var $template = $category.find('.template').clone(false, false);
            $template.removeClass('template');
            $template.show();
            var seturl = 'ajax/add/id/' + $category.attr('cat_id') + '/title/' + $category.attr('def_title');
            $.ajax({
                'async'     : false,
                'type'      : "POST",
                'url'       : seturl,
                'success'   : function(d){
                    $template.attr('edit_id', d.result.val );
                    $template.find('[name="_title"]').val(d.result.title)
                },
                'dataType'  : 'json',

            });
            $category.append($template);
            listen_for_autosave($template);
            $template.find('[name="_title"]').data('oldText', $template.find('[name="_title"]').val() )
        });
    });
});