import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configuration de Pusher pour les notifications en temps réel avec Laravel Echo
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

console.log('Configuration Pusher - Clé API:', import.meta.env.VITE_PUSHER_APP_KEY);
console.log('Configuration Pusher - Cluster:', import.meta.env.VITE_PUSHER_APP_CLUSTER);

// Initialisation de Laravel Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: false,
  
  
});

console.log('Instance Laravel Echo créée:', window.Echo);

// Canal pour les mises à jour de tracemap
try {
    console.log('Tentative de souscription au canal tracemap-updates...');
    // Nous utilisons maintenant directement window.Echo.channel() dans les vues
    console.log('Souscription au canal tracemap-updates sera effectuée directement dans les vues');
} catch (error) {
    console.error('Erreur lors de la configuration de Laravel Echo:', error);
}

// Ajouter des écouteurs d'événements pour les connexions et déconnexions
try {
    window.Echo.connector.pusher.connection.bind('connecting', () => {
        console.log('Pusher - Tentative de connexion en cours...');
    });
    
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('Pusher - Connexion établie avec succès');
        console.log('État de la connexion:', window.Echo.connector.pusher.connection.state);
        console.log('Socket ID:', window.Echo.socketId());
        
        // La connexion est établie, les canaux seront configurés directement dans les vues
        console.log('Connexion établie, les canaux peuvent maintenant être utilisés dans les vues');
    });
    
    window.Echo.connector.pusher.connection.bind('disconnected', () => {
        console.log('Pusher - Déconnecté du serveur');
        console.log('État actuel de la connexion:', window.Echo.connector.pusher.connection.state);
    });
    
    window.Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('Pusher - Erreur de connexion:', err);
        console.error('Détails de l\'erreur:', JSON.stringify(err));
    });
    
    window.Echo.connector.pusher.connection.bind('failed', () => {
        console.error('Pusher - La connexion a échoué définitivement');
    });
} catch (error) {
    console.error('Erreur lors de la configuration des écouteurs d\'événements Pusher:', error);
}
