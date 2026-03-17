<h2>Hola, {{ $ticket->user->name }}</h2>
<p>Te informamos que tu ticket de soporte ha sido **CONCLUIDO**.</p>
<p><strong>Notas de resolución:</strong> {{ $ticket->resolution_notes }}</p>
<p>Gracias por tu paciencia.</p>
<p>Si tienes alguna otra solicitud, no dudes en contactarnos.</p>