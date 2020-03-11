import $ from "jquery";

export default function setEventRoomId(info)
{
    $(info.el).find('div.fc-content').append('<span class="js-event-room-id" style="float: right; font-weight: bold;">' + info.event.getResources()[0]['title'][0] + '</span>');
}
