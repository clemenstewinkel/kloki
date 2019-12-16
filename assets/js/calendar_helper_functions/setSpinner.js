import {$calendarDetail} from "../calendar";

export default function setSpinner()
{
    $calendarDetail.html('<div class="d-flex justify-content-center">\n' +
        '  <div class="spinner-border" role="status">\n' +
        '    <span class="sr-only">Loading...</span>\n' +
        '  </div>\n' +
        '</div>');
}
