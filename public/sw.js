"use strict";

const CACHE_NAME = "offline-cache-v1";
const OFFLINE_URL = 'public/offline.html';

const filesToCache = [
    OFFLINE_URL,
    '/',
    '/logo.png',
    '/build/assets/app.css',
    '/build/assets/app.js',
    '/js/pwa-utils.js',
    '/manifest.json',
    // Icônes SVG
    '/icons/android-icon-192x192.svg',
    '/icons/apple-icon-180x180.svg',
    '/icons/apple-icon-152x152.svg',
    '/icons/apple-icon-144x144.svg',
    '/icons/apple-icon-120x120.svg',
    '/icons/android-icon-96x96.svg',
    // Écrans de démarrage
    '/icons/apple-splash-2048-2732.svg',
    '/icons/apple-splash-1125-2436.svg',
    '/icons/apple-splash-750-1334.svg',
    // Ressources externes
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
    'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    'https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css'
];

// Fonction pour gérer les erreurs de fetch
const handleFetchError = (error) => {
    console.error('Erreur lors du fetch:', error);
    
    // Envoyer l'erreur à la page principale si possible
    if (self.clients) {
        self.clients.matchAll().then(clients => {
            clients.forEach(client => {
                client.postMessage({
                    error: error.message || 'Erreur de fetch non spécifiée',
                    url: error.url || 'URL inconnue',
                    timestamp: new Date().toISOString()
                });
            });
        });
    }
    
    return caches.match(OFFLINE_URL);
};

// Fonction pour vérifier si une ressource est accessible avant de la mettre en cache
const checkResource = async (url) => {
    try {
        const response = await fetch(url, { method: 'HEAD' });
        return response.ok;
    } catch (error) {
        console.warn(`Ressource inaccessible: ${url}`, error);
        return false;
    }
};

self.addEventListener("install", (event) => {
    event.waitUntil(
        (async () => {
            try {
                const cache = await caches.open(CACHE_NAME);
                
                // Filtrer les ressources inaccessibles
                const resourcesToCache = [];
                
                for (const url of filesToCache) {
                    try {
                        // Toujours mettre en cache la page hors ligne et la page d'accueil
                        if (url === OFFLINE_URL || url === '/') {
                            resourcesToCache.push(url);
                            continue;
                        }
                        
                        // Vérifier les autres ressources
                        const isAvailable = await checkResource(url);
                        if (isAvailable) {
                            resourcesToCache.push(url);
                        } else {
                            console.warn(`Ressource ignorée car inaccessible: ${url}`);
                        }
                    } catch (error) {
                        console.error(`Erreur lors de la vérification de ${url}:`, error);
                    }
                }
                
                // Mettre en cache les ressources accessibles
                await cache.addAll(resourcesToCache);
                console.log('Mise en cache réussie pour:', resourcesToCache);
            } catch (error) {
                console.error('Erreur lors de l\'installation du service worker:', error);
            }
        })()
    );
});

self.addEventListener("fetch", (event) => {
    // Stratégie pour les requêtes de navigation (HTML)
    if (event.request.mode === 'navigate') {
        event.respondWith(
            (async () => {
                try {
                    // Essayer d'abord le réseau pour les pages
                    const networkResponse = await fetch(event.request);
                    
                    // Mettre en cache la réponse fraîche
                    const cache = await caches.open(CACHE_NAME);
                    cache.put(event.request, networkResponse.clone());
                    
                    return networkResponse;
                } catch (error) {
                    console.log('Mode hors ligne - navigation vers:', event.request.url);
                    
                    // Essayer de récupérer depuis le cache
                    const cachedResponse = await caches.match(event.request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    
                    // Si pas dans le cache, retourner la page hors ligne
                    return caches.match(OFFLINE_URL);
                }
            })()
        );
    } 
    // Stratégie pour les ressources statiques (images, CSS, JS)
    else if (event.request.destination === 'image' || 
             event.request.destination === 'style' || 
             event.request.destination === 'script' ||
             event.request.url.includes('.svg')) {
        
        event.respondWith(
            (async () => {
                // Vérifier d'abord dans le cache
                const cachedResponse = await caches.match(event.request);
                if (cachedResponse) {
                    // Retourner depuis le cache et mettre à jour en arrière-plan
                    // pour les ressources locales uniquement
                    if (event.request.url.startsWith(self.location.origin)) {
                        fetch(event.request).then(networkResponse => {
                            caches.open(CACHE_NAME).then(cache => {
                                cache.put(event.request, networkResponse);
                            });
                        }).catch(error => console.log('Erreur de mise à jour en arrière-plan:', error));
                    }
                    return cachedResponse;
                }
                
                // Si pas dans le cache, essayer le réseau
                try {
                    const networkResponse = await fetch(event.request);
                    
                    // Mettre en cache la nouvelle ressource
                    const cache = await caches.open(CACHE_NAME);
                    cache.put(event.request, networkResponse.clone());
                    
                    return networkResponse;
                } catch (error) {
                    // Pour les images, retourner une image par défaut si disponible
                    if (event.request.destination === 'image') {
                        return caches.match('/logo.png');
                    }
                    
                    // Sinon, propager l'erreur
                    throw error;
                }
            })()
        );
    }
    // Stratégie pour les autres requêtes (API, etc.)
    else {
        event.respondWith(
            fetch(event.request)
                .catch(handleFetchError)
        );
    }
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
