<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FullCalendar Test</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- FullCalendar CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <!-- SweetAlert2 (optional, for alerts) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <h2 style="text-align:center;">Laravel FullCalendar Test</h2>
    <div id="calendar"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                editable: true,
                selectable: true,
                eventDurationEditable: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    {
                        id: 1,
                        title: 'Booked Trip',
                        start: '2025-09-12',
                        end: '2025-09-14',
                        backgroundColor: '#dc3545',
                        borderColor: '#000',
                        extendedProps: {
                            region: 'North',
                            price: 500,
                            boat: 'Boat A',
                            status: 'Booked'
                        }
                    },
                    {
                        id: 2,
                        title: 'On Hold Trip',
                        start: '2025-09-16',
                        backgroundColor: '#ffc107',
                        borderColor: '#000',
                        extendedProps: {
                            region: 'South',
                            price: 300,
                            boat: 'Boat B',
                            status: 'On Hold'
                        }
                    }
                ],
                eventClick: function (info) {
                    Swal.fire({
                        title: info.event.title,
                        html: `
                            <b>Region:</b> ${info.event.extendedProps.region}<br>
                            <b>Boat:</b> ${info.event.extendedProps.boat}<br>
                            <b>Price:</b> $${info.event.extendedProps.price}<br>
                            <b>Status:</b> ${info.event.extendedProps.status}
                        `,
                        confirmButtonText: 'Close'
                    });
                }
            });

            calendar.render();
        });
    </script>
</body>
</html>
