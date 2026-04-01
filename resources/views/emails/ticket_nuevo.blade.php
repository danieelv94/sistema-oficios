<h2>¡Hola Daniel!</h2>
<p>Se ha recibido una nueva solicitud de soporte técnico:</p>
<ul>
    <li><strong>Usuario:</strong> {{ $usuario }}</li>
    <li><strong>Asunto:</strong> {{ $ticket->subject }}</li>
    <li><strong>Descripción:</strong> {{ $ticket->description }}</li>
</ul>
<p>Puedes revisarlo en el panel de administración.</p>