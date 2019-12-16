import {$calendarDetail} from "../calendar";

export default function setErrorNotAllowed()
{
    $calendarDetail.html('<div class="alert alert-success" role="alert">\n' +
        '  <h4 class="alert-heading">Zugriff verboten!</h4>\n' +
        '  <p>Der Zugriff auf diese Funktion ist Ihnen nicht erlaubt!</p>\n' +
        '  <hr>\n' +
        '  <p class="mb-0">Tut mir leid...</p>\n' +
        '</div>');
}
