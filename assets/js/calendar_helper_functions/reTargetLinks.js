import prepare_event_form from "../prepare_event_form";
import $ from "jquery";
import { $calendarDetail } from "../calendar";

export default function reTargetLinks()
{
    console.log('DEBUG: reTargetLinks called');
    prepare_event_form();
    $calendarDetail.find('a:not(.js-no-retarget)').bind('click', function() {
        const url = $(this).attr('href');
        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $calendarDetail.html(data);
                reTargetLinks();
            }
        });
        return false;
    });

    $calendarDetail.find('form').on('submit', function(e){
        e.preventDefault();
        let $form = $(e.currentTarget);
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(data){
                $calendarDetail.html(data);
                reTargetLinks();
                fullcalendar.refetchEvents();
            }
        });
    });
}
