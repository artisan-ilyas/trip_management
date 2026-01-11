import './bootstrap';
import { Calendar } from '@fullcalendar/core'
import resourceTimelinePlugin from '@fullcalendar/resource-timeline'
import multiMonthPlugin from '@fullcalendar/multimonth'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'


import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// window.initBoatCalendar = function (el, options) {
//     const calendar = new Calendar(el, {
//         plugins: [
//             resourceTimelinePlugin,
//             multiMonthPlugin,
//             listPlugin,
//             interactionPlugin
//         ],
//         ...options
//     })

//     calendar.render()
//     return calendar
// }


window.initFleetCalendar = function (el, options) {
    const calendar = new Calendar(el, {
        plugins: [
            resourceTimelinePlugin,
            interactionPlugin,
            listPlugin
        ],
        initialView: 'resourceTimelineMonth',
        resourceAreaWidth: '260px',
        height: 'auto',
        editable: true,
        selectable: true,
        nowIndicator: true,
        ...options
    })

    calendar.render()
    return calendar
}
