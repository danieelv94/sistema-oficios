// public/sw.js
self.addEventListener('push', function (event) {
    if (!event.data) return;

    let data;
    try {
        // Intentamos leerlo como JSON
        data = event.data.json();
    } catch (e) {
        // Si no es JSON (como el error que te da), lo leemos como texto
        data = {
            title: 'Aviso CEAA',
            body: event.data.text()
        };
    }

    const options = {
        body: data.body,
        icon: data.icon || '/img/logo-ceaa.png',
        badge: '/img/badge.png',
        data: {
            url: data.action_url || '/'
        }
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});