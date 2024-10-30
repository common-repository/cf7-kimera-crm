jQuery(document).ready(function ($) {

    var serializeObject = function (form) {
        var obj = {},
            names = {};

        $.each(form.serializeArray(), function (i, o) {
            var n = o.name,
              v = o.value;

            if (n.includes('[]')) {
                names.n = !names.n ? 1 : names.n + 1;
                var indx = names.n - 1;
                n = n.replace('[]', '[' + indx + ']');
            }

            obj[n] = obj[n] === undefined ? v
              : $.isArray(obj[n]) ? obj[n].concat(v)
              : [obj[n], v];
        });

        return obj;
    };

    var init = function (element) {
        $(element).find("input.cf7k-refresh-change").change(kcf7_refresh);
        $(element).find("input.cf7k-refresh-click").click(kcf7_refresh);
        $(element).find(".cf7k-refresh-change input:not(.cf7k-refresh-change)").change(kcf7_refresh);
        $(element).find(".cf7k-refresh-click input:not(.cf7k-refresh-click)").click(kcf7_refresh);
        $(element).find("select.cf7k-refresh-change").change(kcf7_refresh);
        $(element).find("select.cf7k-refresh-click").click(kcf7_refresh);
        $(element).find(".cf7k-refresh-change select:not(.cf7k-refresh-change)").change(kcf7_refresh);
        $(element).find(".cf7k-refresh-click select:not(.cf7k-refresh-click)").click(kcf7_refresh);
        $(element).find("textarea.cf7k-refresh-change").change(kcf7_refresh);
        $(element).find("textarea.cf7k-refresh-click").click(kcf7_refresh);
        $(element).find(".cf7k-refresh-change textarea:not(.cf7k-refresh-change)").change(kcf7_refresh);
        $(element).find(".cf7k-refresh-click textarea:not(.cf7k-refresh-click)").click(kcf7_refresh);
        $(element).find("button.cf7k-refresh-click").click(kcf7_refresh);
        $(element).find(".cf7k-refresh-click button:not(.cf7k-refresh-click)").click(kcf7_refresh);
    }

    var kcf7_refresh = function () {

        var formElement = $(this).closest('form');
        var id = -1;
        var idFormElement = null;
        var formData = serializeObject(formElement);
        id = formData._wpcf7;
        idFormElement = formData._wpcf7_unit_tag;
        formData._kcfid = id;
        formData._changed_element = $(this).attr('name');
        delete formData._wpcf7;
        delete formData._wpcf7_version;
        delete formData._wpcf7_locale;
        delete formData._wpcf7_unit_tag;
        delete formData._wpnonce;

        var refreshElements = $(formElement).find('.cf7k-dependence-' + $(this).attr('name'));
        formData._refreshElements = [];
        refreshElements.each(function () {
            formData._refreshElements.push($(this).attr('name'));
            $(this).addClass('cf7k-refresh-loading');
        });

        var loader = $('<div class="cf7k-overlay"></div>');
        $('#' + idFormElement).find('form').append(loader);
        $.post(cf7_kimera_crm_ajax.ajaxurl + location.search , formData, function (data) {
            var objData = $.parseJSON(data);
            $.each(objData, function (key, value) {
                var x = $($.parseHTML(value, true));
                var originalElement = $('#' + idFormElement).find('.' + key);
                if (originalElement.length == 0) originalElement = $('#' + idFormElement).find('[name="' + key + '"]');
                originalElement.replaceWith(x);
                init(x);
            });
            loader.remove();
        });
    };

    init($('body'));
});