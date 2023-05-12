jQuery(document).ready(function($) {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultDate: new Date(),
        navLinks: true, // can click day/week names to navigate views
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        events: [
            // aquí puedes añadir eventos al calendario, por ejemplo:
            {
                title: 'Evento de ejemplo',
                start: '2023-05-01'
            }
            // etc...
        ]
    });
});
