/**
 * Utilitaires pour l'application PWA TraceMap
 */

// Détecter si l'application est lancée en mode standalone (PWA)
const isPWA = () => {
    return window.matchMedia('(display-mode: standalone)').matches || 
           window.navigator.standalone || 
           document.referrer.includes('android-app://') ||
           window.location.search.includes('source=pwa');
};

// Appliquer des styles spécifiques pour le mode PWA
const applyPWAStyles = () => {
    if (isPWA()) {
        document.documentElement.classList.add('standalone-mode');
        
        // Masquer la barre d'adresse sur iOS
        window.scrollTo(0, 1);
        
        // Ajuster le viewport pour les appareils avec encoche
        const metaViewport = document.querySelector('meta[name="viewport"]');
        if (metaViewport) {
            metaViewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover');
        }
        
        // Ajuster les éléments d'interface pour le mode plein écran
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            mapContainer.style.height = '100%';
            mapContainer.style.position = 'fixed';
            mapContainer.style.top = '0';
            mapContainer.style.left = '0';
            mapContainer.style.right = '0';
            mapContainer.style.bottom = '0';
            mapContainer.style.zIndex = '10';
        }
        
        // Ajuster les contrôles pour les appareils avec encoche
        const controls = document.querySelectorAll('.leaflet-control-container .leaflet-top');
        controls.forEach(control => {
            control.style.paddingTop = 'env(safe-area-inset-top)';
        });
        
        const bottomControls = document.querySelectorAll('.leaflet-control-container .leaflet-bottom');
        bottomControls.forEach(control => {
            control.style.paddingBottom = 'env(safe-area-inset-bottom)';
        });
    }
};

// Initialiser les fonctionnalités PWA
const initPWA = () => {
    // Appliquer les styles PWA
    applyPWAStyles();
    
    // Écouter les changements de mode d'affichage
    window.matchMedia('(display-mode: standalone)').addEventListener('change', (evt) => {
        if (evt.matches) {
            console.log('L\'application est maintenant en mode standalone');
            document.documentElement.classList.add('standalone-mode');
            applyPWAStyles();
        } else {
            console.log('L\'application n\'est plus en mode standalone');
            document.documentElement.classList.remove('standalone-mode');
        }
    });
    
    // Gérer les événements d'orientation
    window.addEventListener('orientationchange', () => {
        // Réappliquer les styles après un changement d'orientation
        setTimeout(applyPWAStyles, 300);
    });
    
    // Gérer le redimensionnement de la fenêtre
    window.addEventListener('resize', () => {
        if (isPWA()) {
            // Réappliquer les styles après un redimensionnement
            applyPWAStyles();
        }
    });
};

// Exécuter l'initialisation lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', initPWA);

// Exporter les fonctions pour une utilisation externe
window.pwaUtils = {
    isPWA,
    applyPWAStyles,
    initPWA
};