
(function ($) {

    $(document).ready(function () {

        var trackChange = function (element) {
            setInterval(function () {
                var precVal = element.precVal;
                var actualVal = element.val();
                precVal = precVal ? precVal : '';
                actualVal = actualVal ? actualVal : '';
                if (actualVal != precVal) {
                    element.trigger("change");
                    element.precVal = actualVal;
                }
            }, 100);
        };

        // if (_wpcf7.jqueryUi && !_wpcf7.supportHtml5.date) {
        $('.wpcf7-kdate').each(function () {

            var originalName = $(this)[0].id;
            var originalElement = $(this);
            //originalElement[0].name = "";
            //if (originalElement.val() == 'null') originalElement.val('');

            var hElement = $('*[name="' + originalName +'"]');// $('<input type="text"/>');
            //hElement.attr("name", originalName);

            //originalElement.after(hElement);

            var options = {
                dateFormat: originalElement.attr('dateFormat') ? originalElement.attr('dateFormat') : "dd/mm/yy",
                altFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true,
                altField: '[name="' + originalName + '"]',
                defaultDate: originalElement.attr('defaultDate'),
                yearRange: originalElement.attr('yearRange') ? originalElement.attr('yearRange')  : "c-10:c+10"
            }

            originalElement.datepicker(options);


            /*$(this).datepicker("option", "dateFormat", "dd/mm/yy");
            $(this).datepicker("option", "altField", '[name="' + $(this)[0].name.substring(1) + '"]');
            $(this).datepicker("option", "altFormat", "yy-mm-dd");
            $(this).datepicker("option", "defaultDate", "-18y");
            $(this).datepicker("option", "changeMonth", "true");
            $(this).datepicker("option", "changeYear", "true");*/

            $(this).change(function () {
                var x = null;
                try {
                    x = $.datepicker.parseDate(options.dateFormat, originalElement.val());
                }
                catch (e) {
                    x = 0;
                }
                if (x == 0) {
                    hElement.val('null');
                    //hElement.val(originalElement.val());
                }
                else {
                    if (x == null) {
                        hElement.val(null);
                    }
                }
            });
            hElement.change(function () {
                var x = null;
                try {
                    x = $.datepicker.parseDate(options.altFormat, hElement.val());
                }
                catch (e) { x = 0; }
                if (x != 0) {
                    originalElement.datepicker("setDate", x);
                }
                else {
                    if (!originalElement.val()) {
                        originalElement.val('----------');
                    }
                }
            });


            trackChange(hElement);
            $('#test').click(function () {
                hElement.val('2012-05-14');
                return false;
            });

        });
        //  }
    });
}
)(jQuery);