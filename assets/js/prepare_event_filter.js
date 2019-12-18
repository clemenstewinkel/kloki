import $ from "jquery";
import 'jquery-datetimepicker';

export default function prepare_event_filter()
{
    $.datetimepicker.setLocale('de');

    const $endDate   = $('#event_filter_endDate');
    const $startDate = $('#event_filter_startDate');

    $startDate.datetimepicker({
        inline: false,
        timepicker: false,
        format: 'Y-m-d',
        onShow: function( ct ) {
            console.dir(ct);
            this.setOptions({
                maxDate: $endDate.val() ? $endDate.val() : false
            })
        },
    });

    $endDate.datetimepicker({
        inline: false,
        timepicker: false,
        format: 'Y-m-d',
        onShow: function() {
            this.setOptions({
                minDate: $startDate.val() ? $startDate.val() : false
            })
        },
    });

}

