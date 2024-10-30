(function($) {
    $(function () {

        var toggle_ktable= function (_this) {
            return _this.each(function () {
                var formtable = $(this).closest('.contact-form-editor-box-kimeracrm').find('.toggle-k-target');

                if ($(this).is(':checked')) {
                    formtable.removeClass('hidden');
                } else {
                    formtable.addClass('hidden');
                }
            });
        };


        $('input:checkbox.toggle-k-table').click(function(event) {
            toggle_ktable($(this));
        });
        toggle_ktable($('input:checkbox.toggle-k-table'));
  });

  $.fn.cf7_kimeracrm_toggle_form_table = function () {
    return this.each(function() {
        var formtable = $(this).closest('.contact-form-editor-box-kimeracrm').find('fieldset');

      if ($(this).is(':checked')) {
        formtable.removeClass('hidden');
      } else {
        formtable.addClass('hidden');
      }
    });
  };

  _wpcf7.taggen.update['kpanel'] = function ($form) {
      $form.find('textarea.tag').each(function () {
          var tag_type = $(this).attr('name');

          if ($form.find(':input[name="tagtype"]').length) {
              tag_type = $form.find(':input[name="tagtype"]').val();
          }

          if ($form.find(':input[name="required"]').is(':checked')) {
              tag_type += '*';
          }

          var components = _wpcf7.taggen.compose(tag_type, $form);
          $(this).val(components + '\n\tPut your code/tags here...\n[kpanel_end]');
      });

  };

  $('input.insert-tag-textarea').click(function () {
      var $form = $(this).closest('form.tag-generator-panel');
      var tag = $form.find('textarea.tag').val();
      _wpcf7.taggen.insert(tag);
      tb_remove(); // close thickbox
      return false;
  });

})(jQuery);
