import {$calendarDetail} from "../calendar";

export default function setErrorInternalServer()
{
    $calendarDetail.html('<div class="alert alert-danger" role="alert">\n' +
        '  <h4 class="alert-heading">Interner Fehler!</h4>\n' +
        '  <p>Da ist etwas schiefgegangen!</p>\n' +
        '  <hr>\n' +
        '  <p class="mb-0">Die Aktion konnte wahrscheinlich nicht durchgeführt werden!</p>\n' +
        '</div>');
}
