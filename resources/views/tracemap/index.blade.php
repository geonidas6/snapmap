@extends('layouts.app')

@section('content')
    <style>
        /* Styles pour Intro.js */
        .introjs-tooltip {
            border-radius: 12px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
            min-width: 300px !important;
        }
        
        .introjs-tooltipbuttons {
            display: flex !important;
            justify-content: space-between !important;
            flex-wrap: nowrap !important;
        }
        
        .introjs-tooltiptext {
            font-size: 15px !important;
            line-height: 1.5 !important;
        }
        
        .introjs-button {
            border-radius: 6px !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
            font-size: 14px !important;
        }
        
        .introjs-skipbutton {
            color: #3b82f6 !important;
            padding: 6px 10px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            min-width: 60px !important;
        }
        
        .introjs-prevbutton, .introjs-nextbutton, .introjs-donebutton {
            background-color: #3b82f6 !important;
            color: white !important;
            border: none !important;
            padding: 6px 10px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            min-width: 60px !important;
        }
        
        .introjs-prevbutton:hover, .introjs-nextbutton:hover {
            background-color: #2563eb !important;
        }
        
        /* Styles pour la carte en plein écran */
        #map {
            height: 100vh;
            width: 100%;
            z-index: 10;
            position: absolute;
            top: 0;
            left: 0;
        }

        /* Style pour le marqueur de position de l'utilisateur */
        .user-location-marker {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(66, 133, 244, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(66, 133, 244, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(66, 133, 244, 0);
            }
        }

        /* Style pour les marqueurs personnalisés */
        .custom-marker {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid white;
            box-shadow: 0 3px 14px rgba(0,0,0,0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .custom-marker:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 20px rgba(0,0,0,0.6);
        }

        /* Animation de pulsation pour les nouveaux marqueurs */
        .marker-pulse {
            animation: markerPulse 2s infinite;
        }

        @keyframes markerPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        /* Style pour l'affichage plein écran */
        .fullscreen-media {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .fullscreen-media img,
        .fullscreen-media video {
            max-width: 90%;
            max-height: 80vh;
            object-fit: contain;
        }

        /* Styles pour les boutons de navigation */
        .nav-button {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10000;
        }

        .nav-button:hover {
            background-color: rgba(0, 0, 0, 0.8);
            transform: translateY(-50%) scale(1.1);
        }

        .prev-button {
            left: 20px;
        }

        .next-button {
            right: 20px;
        }

        .tracemap-bubble {
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            margin: 10px 0;
            font-size: 16px;
            max-width: 80%;
        }

        .close-fullscreen {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            font-size: 30px;
            cursor: pointer;
        }

        .media-controls {
            position: absolute;
            bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .media-controls button {
            background-color: rgba(255, 255, 255, 0.3);
            border: none;
            color: white;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Animations pour les transitions */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes scaleUp {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        .scale-up {
            animation: scaleUp 0.3s ease-out;
        }

        .slide-up {
            animation: slideUp 0.3s ease-out;
        }

        /* Style pour l'interface utilisateur sur la carte */
        .map-ui {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background-color: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Styles pour les contrôles de la carte */
        .leaflet-control-layers {
            border-radius: 8px !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
        }

        .leaflet-control-layers-toggle {
            width: 36px !important;
            height: 36px !important;
            background-size: 20px 20px !important;
        }

        .leaflet-control-layers-expanded {
            padding: 10px !important;
            background-color: white !important;
            border-radius: 8px !important;
            border: none !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
        }

        .leaflet-control-zoom {
            border: none !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
        }

        .leaflet-control-zoom a {
            width: 36px !important;
            height: 36px !important;
            line-height: 36px !important;
            border-radius: 8px !important;
            background-color: white !important;
            color: #555 !important;
            transition: all 0.2s ease !important;
        }

        .leaflet-control-zoom a:hover {
            background-color: #f5f5f5 !important;
            color: #333 !important;
        }

        .leaflet-bar {
            border-radius: 8px !important;
            overflow: hidden !important;
        }

        .leaflet-control-locate a {
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
        }

        .leaflet-control-locate a:hover {
            background-color: #f5f5f5 !important;
        }
    </style>
    <!-- Carte en plein écran -->
    <div id="map" data-intro="Bienvenue sur TraceMap ! Voici la carte interactive où vous pouvez voir et partager des moments géolocalisés." data-step="1"></div>

    <!-- Interface utilisateur sur la carte -->
    <div class="map-ui" data-intro="TraceMap vous permet de partager des photos et vidéos liées à des lieux spécifiques sur la carte." data-step="2">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-800">TraceMap</h1>
            <button id="restart-tutorial" class="text-sm text-blue-600 hover:text-blue-800 flex items-center ml-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Aide
            </button>
        </div>
    </div>

    <!-- Bouton flottant pour ouvrir le modal -->
    <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 pointer-events-auto z-20">
        <button id="open-upload-modal" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all hover:scale-110" data-intro="Cliquez sur ce bouton pour publier une nouvelle photo ou vidéo à l'emplacement de votre choix sur la carte." data-step="4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </button>
    </div>

    <!-- Modal pour téléverser des médias après un clic sur la carte (style Tracemapchat) -->
    <div id="upload-modal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 ease-in-out">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-blue-500 to-purple-600 rounded-t-2xl">
                <h2 class="text-xl font-bold text-white">Publier un nouveau Tracemap</h2>
                <button id="close-modal" class="text-white hover:text-gray-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="tracemap-form" enctype="multipart/form-data" class="p-6">
                @csrf

                <!-- Champs cachés pour les coordonnées -->
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <!-- Message de confirmation -->
                <div id="upload-success" class="hidden mb-4 p-3 bg-green-100 text-green-800 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span id="success-message">Tracemap publié avec succès!</span>
                </div>

                <!-- Message d'erreur -->
                <div id="upload-error" class="hidden mb-4 p-3 bg-red-100 text-red-800 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span id="error-message">Une erreur est survenue.</span>
                </div>

                <div id="upload-form-content">
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label for="content" class="text-lg font-medium text-gray-800">Ajouter des photos/vidéos</label>
                            <div class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Emplacement capturé ✓</div>
                        </div>

                        <div class="relative border-2 border-dashed border-blue-300 rounded-lg p-6 text-center hover:bg-blue-50 transition-colors cursor-pointer">
                            <input type="file" name="content[]" id="content" multiple accept="image/*,video/*"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-blue-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>

                            <p class="text-sm text-gray-600">Glissez-déposez vos fichiers ici ou cliquez pour parcourir</p>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF, MP4, MOV (max 10MB)</p>
                        </div>
                    </div>

                    <!-- Conteneur de prévisualisation avec style amélioré -->
                    <div id="preview-container" class="grid grid-cols-2 gap-3 mb-6"></div>

                    <!-- Bulle de texte style Tracemapchat -->
                    <div class="tracemap-bubble bg-yellow-100 text-yellow-800 mb-6 mx-auto text-center">
                        Partagez votre moment avec le monde !
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancel-btn" class="px-5 py-2.5 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            Annuler
                        </button>
                        <button type="button" id="submit-btn" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-full hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all transform hover:scale-105">
                            Publier maintenant
                        </button>
                    </div>
                </div>

                <!-- Indicateur de chargement -->
                <div id="upload-loading" class="hidden flex flex-col items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-blue-500 mb-4"></div>
                    <p class="text-gray-600">Publication en cours...</p>
                </div>
            </form>
        </div>
    </div>
    </div>

    <!-- Élément contenant les données JSON des tracemaps -->
    <div id="tracemaps-data" data-tracemaps='{!! json_encode($tracemaps) !!}' style="display: none;"></div>

    <script>
        // Éléments du DOM pour le modal
        const uploadModal = document.getElementById('upload-modal');
        const closeModalBtn = document.getElementById('close-modal');
        const cancelBtn = document.getElementById('cancel-btn');
        const submitBtn = document.getElementById('submit-btn');
        const tracemapForm = document.getElementById('tracemap-form');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const contentInput = document.getElementById('content');
        const previewContainer = document.getElementById('preview-container');
        const uploadFormContent = document.getElementById('upload-form-content');
        const uploadLoading = document.getElementById('upload-loading');
        const uploadSuccess = document.getElementById('upload-success');
        const uploadError = document.getElementById('upload-error');
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');

        // Variables pour stocker les coordonnées du clic et le marqueur temporaire
        let clickedLat, clickedLng;
        let tempMarker = null;
        let userLocationMarker = null;

        // Initialisation de la carte (centrée temporairement sur Paris)
        const map = L.map('map', {
            zoomControl: false, // Désactiver les contrôles de zoom par défaut pour les ajouter manuellement
            attributionControl: true
        }).setView([48.8566, 2.3522], 13);

        // Définition des différentes couches de carte
        const baseMaps = {
            "Standard": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }),
            "Satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
            }),
            "Terrain": L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
            })
        };

        // Ajouter la couche par défaut à la carte
        baseMaps["Standard"].addTo(map);

        // Ajouter le contrôle de couches
        const layersControl = L.control.layers(baseMaps, null, {position: 'topright'}).addTo(map);
        
        // Ajouter l'attribut data-intro au contrôle de couches pour le tutoriel
        setTimeout(() => {
            const layersElement = document.querySelector('.leaflet-control-layers');
            if (layersElement) {
                layersElement.setAttribute('data-intro', 'Changez le style de la carte en utilisant ce contrôle de couches.');
                layersElement.setAttribute('data-step', '5');
            }
        }, 500);

        // Ajouter le contrôle de zoom en bas à droite
        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);
        
        // Ajouter l'attribut data-intro au contrôle de localisation pour le tutoriel
        setTimeout(() => {
            const locateElement = document.querySelector('.leaflet-control-locate');
            if (locateElement) {
                locateElement.setAttribute('data-intro', 'Cliquez ici pour centrer la carte sur votre position actuelle.');
                locateElement.setAttribute('data-step', '3');
            }
        }, 500);
        
        // Ajouter l'attribut data-intro au contrôle de zoom pour le tutoriel
        setTimeout(() => {
            const zoomElement = document.querySelector('.leaflet-control-zoom');
            if (zoomElement) {
                zoomElement.setAttribute('data-intro', 'Utilisez ces boutons pour zoomer et dézoomer sur la carte.');
                zoomElement.setAttribute('data-step', '6');
            }
        }, 500);

        // Ajouter des styles personnalisés pour le bouton de localisation
        const customLocateStyles = `
            .leaflet-control-locate a {
                background-color: #3b82f6 !important;
                color: white !important;
                border-radius: 4px !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
                transition: all 0.2s ease !important;
            }
            .leaflet-control-locate a:hover {
                background-color: #2563eb !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 3px 6px rgba(0,0,0,0.3) !important;
            }
            .leaflet-control-locate.active a {
                background-color: #1e40af !important;
            }
            
            /* Styles pour remonter les contrôles sur mobile */
            @media (max-width: 768px) {
                .leaflet-control-zoom,
                .leaflet-control-locate,
                .leaflet-control-layers {
                    margin-bottom: 60px !important;
                }
                
                .leaflet-bottom .leaflet-control {
                    margin-bottom: 60px !important;
                }
            }
        `;
        
        // Ajouter les styles au document
        const styleElement = document.createElement('style');
        styleElement.textContent = customLocateStyles;
        document.head.appendChild(styleElement);
        
        // Ajouter le contrôle de localisation à la carte en utilisant le plugin Leaflet.Locate
        const lc = L.control.locate({
            position: 'bottomright',
            strings: {
                title: "Centrer sur ma position"
            },
            locateOptions: {
                enableHighAccuracy: true,
                maxZoom: 16,
                timeout: 10000,
                maximumAge: 60000
            },
            icon: 'fa fa-location-crosshairs',
            iconLoading: 'fa fa-spinner fa-spin',
            iconElementTag: 'span',
            flyTo: false, // Désactivé pour éviter l'erreur getBounds
            cacheLocation: true,
            showPopup: false,
            showCompass: true,
            returnToPrevBounds: false, // Désactivé pour éviter l'erreur de bounds
            keepCurrentZoomLevel: false,
            drawCircle: true,
            drawMarker: true,
            markerStyle: {
                radius: 5,
                color: '#3b82f6',
                fillColor: '#3b82f6',
                fillOpacity: 0.7,
                weight: 2,
                opacity: 0.9
            },
            circleStyle: {
                color: '#3b82f6',
                fillColor: '#60a5fa',
                fillOpacity: 0.15,
                weight: 2,
                opacity: 0.5
            },
            onLocationError: function(err) {
                console.error('Erreur de localisation:', err.message);
                alert('Impossible d\'obtenir votre position: ' + err.message);
            },
            onLocationOutsideMapBounds: function(context) {
                console.log('Position en dehors des limites de la carte');
                alert('Votre position est en dehors des limites de la carte');
            },
            onLocationFound: function(e) {
                console.log('Position trouvée:', e.latlng.lat, e.latlng.lng);
                
                try {
                    // Implémentation personnalisée du flyTo pour éviter l'erreur getBounds
                    map.flyTo(e.latlng, 16, {
                        duration: 1.5,
                        easeLinearity: 0.25,
                        animate: true
                    });
                } catch (error) {
                    console.error('Erreur lors du déplacement de la carte:', error);
                    // Fallback simple en cas d'erreur
                    try {
                        map.setView(e.latlng, 16);
                    } catch (setViewError) {
                        console.error('Erreur lors du setView:', setViewError);
                    }
                }

                // Mettre à jour ou créer le marqueur de position de l'utilisateur
                if (userLocationMarker) {
                    userLocationMarker.setLatLng(e.latlng);
                } else {
                    userLocationMarker = L.marker(e.latlng, {icon: userIcon}).addTo(map);
                    userLocationMarker.bindPopup('Votre position actuelle');
                }

                userLocationMarker.openPopup();
            }
        }).addTo(map);

        // Fonction personnalisée pour centrer sur la position de l'utilisateur
        function manualCenterOnUserLocation() {
            console.log('Demande manuelle de localisation');
            
            try {
                // Utiliser un try-catch pour éviter les erreurs de bounds
                lc.start();
            } catch (error) {
                console.error('Erreur lors de la localisation:', error);
                
                // Fallback en cas d'erreur: utiliser la géolocalisation native
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            // Animation douce vers la position
                            map.flyTo([lat, lng], 16, {
                                duration: 1.5,
                                easeLinearity: 0.25,
                                animate: true
                            });
                            
                            // Mettre à jour ou créer le marqueur
                            if (userLocationMarker) {
                                userLocationMarker.setLatLng([lat, lng]);
                            } else {
                                userLocationMarker = L.marker([lat, lng], {icon: userIcon}).addTo(map);
                                userLocationMarker.bindPopup('Votre position actuelle');
                            }
                            
                            userLocationMarker.openPopup();
                        },
                        function(error) {
                            console.error('Erreur de géolocalisation:', error.message);
                            alert('Impossible d\'obtenir votre position: ' + error.message);
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 60000
                        }
                    );
                } else {
                    alert('La géolocalisation n\'est pas prise en charge par votre navigateur.');
                }
            }
        }
        
        // Le bouton de recentrage a été supprimé pour simplifier l'interface
        // La fonctionnalité de localisation est toujours disponible via le contrôle Leaflet.Locate

        // Fonction pour centrer la carte sur la position de l'utilisateur (maintenue pour compatibilité)
        function centerOnUserLocation() {
            console.log('Fonction centerOnUserLocation appelée - redirection vers le plugin Leaflet.Locate');
            
            try {
                // Utiliser le plugin Leaflet.Locate avec gestion d'erreur
                lc.start();
            } catch (error) {
                console.error('Erreur lors de l\'utilisation du plugin Leaflet.Locate:', error);
                
                // Fallback en cas d'erreur: utiliser la géolocalisation native
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            try {
                                // Animation douce vers la position
                                map.flyTo([lat, lng], 16, {
                                    duration: 1.5,
                                    easeLinearity: 0.25,
                                    animate: true
                                });
                            } catch (flyToError) {
                                console.error('Erreur lors du flyTo:', flyToError);
                                try {
                                    // Fallback sans animation
                                    map.setView([lat, lng], 16);
                                } catch (setViewError) {
                                    console.error('Erreur lors du setView:', setViewError);
                                }
                            }
                            
                            // Mettre à jour ou créer le marqueur
                            if (userLocationMarker) {
                                userLocationMarker.setLatLng([lat, lng]);
                            } else {
                                userLocationMarker = L.marker([lat, lng], {icon: userIcon}).addTo(map);
                                userLocationMarker.bindPopup('Votre position actuelle');
                            }
                            
                            userLocationMarker.openPopup();
                        },
                        function(error) {
                            console.error('Erreur de géolocalisation:', error.message);
                            alert('Impossible d\'obtenir votre position: ' + error.message);
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 60000
                        }
                    );
                } else {
                    alert('La géolocalisation n\'est pas prise en charge par votre navigateur. La carte sera centrée sur Paris.');
                    try {
                        map.flyTo([48.8566, 2.3522], 13, {
                            duration: 1.5,
                            easeLinearity: 0.25,
                            animate: true
                        });
                    } catch (error) {
                        console.error('Erreur lors du flyTo vers Paris:', error);
                        try {
                            map.setView([48.8566, 2.3522], 13);
                        } catch (setViewError) {
                            console.error('Erreur lors du setView vers Paris:', setViewError);
                        }
                    }
                }
            }
            //         });
            //     }
            // }, 2000);
        }

        // Icône personnalisée pour la position de l'utilisateur
        const userIcon = L.divIcon({
            className: 'user-location-marker',
            html: `<div style="
            width: 20px;
            height: 20px;
            background-color: #4285F4;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.5), 0 0 10px rgba(0, 0, 0, 0.3);
        "></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        // Essayer d'obtenir la position actuelle de l'utilisateur
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Centrer la carte sur la position de l'utilisateur avec une animation douce
                map.flyTo([lat, lng], 15, {
                    duration: 1.5,
                    easeLinearity: 0.25,
                    animate: true
                });

                // Utiliser la fonction centerOnUserLocation pour créer le marqueur
                // afin d'éviter la duplication de marqueurs
                centerOnUserLocation();

                // Créer un cercle autour de la position pour indiquer la précision
                L.circle([lat, lng], {
                    color: '#4285F4',
                    fillColor: '#4285F4',
                    fillOpacity: 0.1,
                    radius: 100
                }).addTo(map);

            }, function(error) {
                console.log('Erreur de géolocalisation:', error.message);
            });
        }

        // Données des tracemaps depuis le contrôleur
        // Utilisation d'un élément HTML pour stocker les données JSON
        // L'analyseur JavaScript ne verra pas la syntaxe Blade
        const tracemapsDataElement = document.getElementById('tracemaps-data');
        const tracemaps = tracemapsDataElement ? JSON.parse(tracemapsDataElement.getAttribute('data-tracemaps') || '[]') : [];

        // Ajout des marqueurs personnalisés pour chaque tracemap
        tracemaps.forEach(tracemap => {
            // Vérifier si le tracemap a des médias
            if (tracemap.media && tracemap.media.length > 0) {
                // Utiliser le premier média comme miniature pour le marqueur
                const firstMedia = tracemap.media[0];
                const isVideo = (firstMedia.file_type && firstMedia.file_type.startsWith('video/')) ||
                    (firstMedia.file_path && ['mp4', 'mov', 'avi'].some(ext => firstMedia.file_path.toLowerCase().endsWith(ext)));

                // Créer un marqueur personnalisé avec l'image miniature
                const customIcon = L.divIcon({
                    className: '',
                    html: `<div class="custom-marker" style="background-image: url('${isVideo ? '/images/video-placeholder.svg' : '/' + (firstMedia.file_path || '')}'); background-size: cover; background-position: center;"></div>`,
                    iconSize: [50, 50],
                    iconAnchor: [25, 25]
                });

                const marker = L.marker([tracemap.latitude, tracemap.longitude], { icon: customIcon }).addTo(map);

                // Ajouter un badge indiquant le nombre de médias si > 1
                if (tracemap.media.length > 1) {
                    const markerElement = marker.getElement();
                    if (markerElement) {
                        const badge = document.createElement('div');
                        badge.className = 'absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold';
                        badge.textContent = tracemap.media.length;
                        badge.style.cssText = 'position: absolute; top: -5px; right: -5px; z-index: 1000;';
                        markerElement.appendChild(badge);
                    }
                }

                // Au clic sur le marqueur, afficher tous les médias en plein écran avec navigation
                marker.on('click', function() {
                    showFullscreenMedia(firstMedia, tracemap.media, 0, tracemap);
                });

                // Ajouter un effet d'animation au survol
                marker.on('mouseover', function() {
                    const markerElement = marker.getElement();
                    if (markerElement) {
                        const customMarker = markerElement.querySelector('.custom-marker');
                        if (customMarker) {
                            customMarker.style.transform = 'scale(1.1)';
                        }
                    }
                });

                marker.on('mouseout', function() {
                    const markerElement = marker.getElement();
                    if (markerElement) {
                        const customMarker = markerElement.querySelector('.custom-marker');
                        if (customMarker) {
                            customMarker.style.transform = 'scale(1)';
                        }
                    }
                });
            }
        });

        // Gestion du clic sur le bouton flottant pour ouvrir le modal
        document.getElementById('open-upload-modal').addEventListener('click', function() {
            // Utiliser la position actuelle de la carte comme coordonnées par défaut
            const center = map.getCenter();
            document.getElementById('latitude').value = center.lat;
            document.getElementById('longitude').value = center.lng;

            // Afficher le modal
            document.getElementById('upload-modal').classList.remove('hidden');
        });

        // Événement de clic sur la carte pour ajouter un nouveau tracemap
        map.on('click', function(e) {
            // Stocke les coordonnées du clic
            clickedLat = e.latlng.lat;
            clickedLng = e.latlng.lng;

            // Met à jour les champs cachés du formulaire
            latitudeInput.value = clickedLat;
            longitudeInput.value = clickedLng;

            // Si un marqueur temporaire existe déjà, le supprimer
            if (tempMarker) {
                map.removeLayer(tempMarker);
            }

            // Ajouter un marqueur temporaire à l'emplacement cliqué
            tempMarker = L.marker([clickedLat, clickedLng]).addTo(map);
            tempMarker.bindPopup('Position du nouveau Tracemap').openPopup();

            // Afficher le modal
            uploadModal.classList.remove('hidden');
        });

        // Fermer le modal lorsqu'on clique sur le bouton de fermeture ou d'annulation
        closeModalBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Fermer le modal si on clique en dehors
        uploadModal.addEventListener('click', function(e) {
            if (e.target === uploadModal) {
                closeModal();
            }
        });

        // Gestionnaire d'événement pour le bouton de soumission
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Vérifier si des fichiers ont été sélectionnés
            if (contentInput.files.length === 0) {
                errorMessage.textContent = 'Veuillez sélectionner au moins un fichier.';
                uploadError.classList.remove('hidden');
                return;
            }

            // Masquer les messages précédents
            uploadSuccess.classList.add('hidden');
            uploadError.classList.add('hidden');

            // Afficher l'indicateur de chargement
            uploadFormContent.classList.add('hidden');
            uploadLoading.classList.remove('hidden');

            // Créer un objet FormData pour envoyer les fichiers
            const formData = new FormData(tracemapForm);

            // Envoyer la requête AJAX
            fetch('{{ route("tracemap.store.ajax") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                },
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    // Masquer l'indicateur de chargement
                    uploadLoading.classList.add('hidden');
                    uploadFormContent.classList.remove('hidden');

                    if (data.success) {
                        // Afficher le message de succès
                        successMessage.textContent = data.message;
                        uploadSuccess.classList.remove('hidden');

                        // Créer un nouveau marqueur sur la carte
                        const tracemap = data.tracemap;
                        if (tracemap.media && tracemap.media.length > 0) {
                            const firstMedia = tracemap.media[0];
                            const marker = createNewMarker(
                                tracemap.latitude,
                                tracemap.longitude,
                                firstMedia.file_path,
                                firstMedia.is_video || false
                            );

                            // Ajouter un badge indiquant le nombre de médias si > 1
                            if (tracemap.media.length > 1) {
                                const markerElement = marker.getElement();
                                if (markerElement) {
                                    const badge = document.createElement('div');
                                    badge.className = 'absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold';
                                    badge.textContent = tracemap.media.length;
                                    badge.style.cssText = 'position: absolute; top: -5px; right: -5px; z-index: 1000;';
                                    markerElement.appendChild(badge);
                                }
                            }

                            // Configurer le clic sur le marqueur pour afficher les médias
                            marker.on('click', function() {
                                showFullscreenMedia(firstMedia, tracemap.media, 0, tracemap);
                            });
                        }

                        // Réinitialiser le formulaire après un court délai
                        setTimeout(() => {
                            closeModal();
                        }, 2000);
                    } else {
                        // Afficher le message d'erreur
                        errorMessage.textContent = data.message || 'Une erreur est survenue lors du téléversement.';
                        uploadError.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    // Masquer l'indicateur de chargement et afficher l'erreur
                    uploadLoading.classList.add('hidden');
                    uploadFormContent.classList.remove('hidden');
                    errorMessage.textContent = 'Erreur de connexion: ' + error.message;
                    uploadError.classList.remove('hidden');
                    console.error('Erreur:', error);
                });
        });

        // Fonction pour fermer le modal
        function closeModal() {
            uploadModal.classList.add('hidden');

            // Réinitialiser le formulaire
            tracemapForm.reset();
            previewContainer.innerHTML = '';

            // Supprimer le marqueur temporaire
            if (tempMarker) {
                map.removeLayer(tempMarker);
                tempMarker = null;
            }
        }

        // Fonction pour créer un nouveau marqueur après téléversement
        function createNewMarker(lat, lng, mediaPath, isVideo = false) {
            // Créer un élément HTML pour le marqueur personnalisé
            const markerHtml = document.createElement('div');
            markerHtml.className = 'custom-marker marker-pulse';

            // Définir l'image de fond
            markerHtml.style.backgroundImage = `url('${isVideo ? '/images/video-placeholder.svg' : '/' + mediaPath}')`;
            markerHtml.style.backgroundSize = 'cover';
            markerHtml.style.backgroundPosition = 'center';

            // Créer l'icône personnalisée
            const customIcon = L.divIcon({
                html: markerHtml,
                className: '',
                iconSize: [50, 50],
                iconAnchor: [25, 25]
            });

            // Créer le marqueur avec l'icône personnalisée
            const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);

            // Après 5 secondes, retirer l'effet de pulsation
            setTimeout(() => {
                const markerElement = marker.getElement();
                if (markerElement) {
                    const customMarker = markerElement.querySelector('.custom-marker');
                    if (customMarker) {
                        customMarker.classList.remove('marker-pulse');
                    }
                }
            }, 5000);

            return marker;
        }

        // Prévisualisation des fichiers sélectionnés avec style amélioré
        contentInput.addEventListener('change', function() {
            // Vider le conteneur de prévisualisation
            previewContainer.innerHTML = '';

            // Afficher un message si aucun fichier n'est sélectionné
            if (this.files.length === 0) {
                previewContainer.innerHTML = '<div class="col-span-2 text-center text-gray-500 py-4">Aucun fichier sélectionné</div>';
                return;
            }

            // Parcourir tous les fichiers sélectionnés
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item bg-gray-50 border rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow';

                // Ajouter un indicateur de chargement
                const loadingIndicator = document.createElement('div');
                loadingIndicator.className = 'flex items-center justify-center h-32 bg-gray-100';
                loadingIndicator.innerHTML = `
                <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
                previewItem.appendChild(loadingIndicator);

                reader.onload = function(e) {
                    // Supprimer l'indicateur de chargement
                    previewItem.removeChild(loadingIndicator);

                    // Conteneur pour le média
                    const mediaContainer = document.createElement('div');
                    mediaContainer.className = 'relative h-32';

                    // Vérifier si le fichier est une image ou une vidéo
                    if (file.type.startsWith('image/')) {
                        // Créer un élément img pour les images
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-32 object-cover';
                        mediaContainer.appendChild(img);
                    } else if (file.type.startsWith('video/')) {
                        // Créer un élément video pour les vidéos
                        const video = document.createElement('video');
                        video.src = e.target.result;
                        video.className = 'w-full h-32 object-cover';
                        video.controls = true;
                        mediaContainer.appendChild(video);

                        // Ajouter un badge pour indiquer que c'est une vidéo
                        const videoBadge = document.createElement('div');
                        videoBadge.className = 'absolute top-2 right-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded-full';
                        videoBadge.textContent = 'VIDEO';
                        mediaContainer.appendChild(videoBadge);
                    }

                    previewItem.appendChild(mediaContainer);

                    // Ajouter les informations du fichier
                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'p-2';

                    // Nom du fichier
                    const fileName = document.createElement('p');
                    fileName.className = 'text-xs font-medium text-gray-800 truncate';
                    fileName.textContent = file.name;
                    fileInfo.appendChild(fileName);

                    // Taille du fichier
                    const fileSize = document.createElement('p');
                    fileSize.className = 'text-xs text-gray-500';
                    fileSize.textContent = formatFileSize(file.size);
                    fileInfo.appendChild(fileSize);

                    previewItem.appendChild(fileInfo);

                    // Ajouter l'élément de prévisualisation au conteneur
                    previewContainer.appendChild(previewItem);
                };

                // Lire le fichier comme une URL de données
                reader.readAsDataURL(file);
            });
        });

        // Fonction pour formater la taille du fichier
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        // Affichage des médias en plein écran avec navigation entre les médias d'un même tracemap
        function showFullscreenMedia(media, allMedias = null, currentIndex = 0, tracemapObj = null) {
            // Si allMedias n'est pas fourni, on considère qu'il n'y a qu'un seul média
            const mediaArray = allMedias || [media];
            const index = currentIndex;

            // Créer le conteneur pour l'affichage plein écran
            const fullscreenContainer = document.createElement('div');
            fullscreenContainer.className = 'fullscreen-media fade-in';

            // Bouton de fermeture
            const closeButton = document.createElement('div');
            closeButton.className = 'close-fullscreen';
            closeButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        `;
            closeButton.addEventListener('click', () => {
                document.body.removeChild(fullscreenContainer);
            });

            // Conteneur pour le média
            const mediaContainer = document.createElement('div');
            mediaContainer.className = 'media-content relative scale-up';

            // Déterminer si c'est une image ou une vidéo
            const currentMedia = mediaArray[index];
            const isVideo = (currentMedia.file_type && currentMedia.file_type.startsWith('video/')) ||
                (currentMedia.file_path && ['mp4', 'mov', 'avi'].some(ext => currentMedia.file_path.toLowerCase().endsWith(ext)));

            if (isVideo) {
                const video = document.createElement('video');
                video.src = currentMedia.file_path || '';
                video.controls = true;
                video.autoplay = true;
                video.className = 'mb-4 rounded-lg shadow-lg';
                mediaContainer.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = currentMedia.file_path || '';
                img.className = 'mb-4 rounded-lg shadow-lg';
                mediaContainer.appendChild(img);
            }

            // Indicateur de progression (style Stories)
            if (mediaArray.length > 1) {
                const progressContainer = document.createElement('div');
                progressContainer.className = 'fixed top-4 left-0 right-0 flex justify-center space-x-1 px-4';

                for (let i = 0; i < mediaArray.length; i++) {
                    const progressBar = document.createElement('div');
                    progressBar.className = `h-1 rounded-full flex-1 ${i === index ? 'bg-white' : 'bg-white bg-opacity-40'}`;
                    progressContainer.appendChild(progressBar);
                }

                fullscreenContainer.appendChild(progressContainer);
            }

            // Boutons de navigation (si plusieurs médias)
            if (mediaArray.length > 1) {
                // Bouton précédent
                if (index > 0) {
                    const prevButton = document.createElement('div');
                    prevButton.className = 'nav-button prev-button';
                    prevButton.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                `;
                    prevButton.addEventListener('click', (e) => {
                        e.stopPropagation();
                        document.body.removeChild(fullscreenContainer);
                        showFullscreenMedia(null, mediaArray, index - 1, tracemapObj);
                    });
                    fullscreenContainer.appendChild(prevButton);
                }

                // Bouton suivant
                if (index < mediaArray.length - 1) {
                    const nextButton = document.createElement('div');
                    nextButton.className = 'nav-button next-button';
                    nextButton.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                `;
                    nextButton.addEventListener('click', (e) => {
                        e.stopPropagation();
                        document.body.removeChild(fullscreenContainer);
                        showFullscreenMedia(null, mediaArray, index + 1, tracemapObj);
                    });
                    fullscreenContainer.appendChild(nextButton);
                }
            }

            // Ajouter des bulles de texte style Tracemapchat/WhatsApp
            const bubbleContainer = document.createElement('div');
            bubbleContainer.className = 'flex flex-col items-center mt-4 slide-up';
            bubbleContainer.style.position = 'fixed';
            bubbleContainer.style.top = '80px';
            bubbleContainer.style.left = '50%';
            bubbleContainer.style.transform = 'translateX(-50%)';
            bubbleContainer.style.zIndex = '10000';

            // Afficher la durée depuis laquelle le statut est publié
            const timeAgo = document.createElement('div');
            timeAgo.className = 'tracemap-bubble';
            timeAgo.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
            timeAgo.style.color = 'white';
            timeAgo.style.padding = '10px 20px';
            timeAgo.style.borderRadius = '20px';
            timeAgo.style.margin = '10px 0';
            timeAgo.style.fontSize = '16px';
            timeAgo.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';

            // Calculer la durée depuis la publication
            const createdAt = new Date(currentMedia.created_at || (tracemapObj && tracemapObj.created_at) || new Date());
            const now = new Date();
            const diffInSeconds = Math.floor((now - createdAt) / 1000);

            let timeAgoText = '';
            if (diffInSeconds < 60) {
                timeAgoText = `Il y a ${diffInSeconds} secondes`;
            } else if (diffInSeconds < 3600) {
                const minutes = Math.floor(diffInSeconds / 60);
                timeAgoText = `Il y a ${minutes} minute${minutes > 1 ? 's' : ''}`;
            } else if (diffInSeconds < 86400) {
                const hours = Math.floor(diffInSeconds / 3600);
                timeAgoText = `Il y a ${hours} heure${hours > 1 ? 's' : ''}`;
            } else {
                const days = Math.floor(diffInSeconds / 86400);
                timeAgoText = `Il y a ${days} jour${days > 1 ? 's' : ''}`;
            }

            timeAgo.textContent = timeAgoText;
            bubbleContainer.appendChild(timeAgo);

            // Créer le message d'expiration et l'ajouter en bas
            const expirationMessage = document.createElement('div');
            expirationMessage.className = 'tracemap-bubble mt-2 text-center text-sm text-gray-300';
            expirationMessage.style.position = 'fixed';
            expirationMessage.style.bottom = '30px';
            expirationMessage.style.left = '50%';
            expirationMessage.style.transform = 'translateX(-50%)';
            expirationMessage.style.padding = '10px 20px';
            expirationMessage.style.borderRadius = '20px';
            expirationMessage.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
            expirationMessage.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
            expirationMessage.textContent = 'Status updates disappear after 24 hours';

            // Ajouter tous les éléments au conteneur
            fullscreenContainer.appendChild(closeButton);
            fullscreenContainer.appendChild(mediaContainer);
            fullscreenContainer.appendChild(bubbleContainer);
            fullscreenContainer.appendChild(expirationMessage);

            // Ajouter au body
            document.body.appendChild(fullscreenContainer);

            // Fermer en cliquant n'importe où
            fullscreenContainer.addEventListener('click', (e) => {
                if (e.target === fullscreenContainer) {
                    document.body.removeChild(fullscreenContainer);
                }
            });

            // Navigation avec les touches du clavier
            document.addEventListener('keydown', function keyNavigation(e) {
                if (e.key === 'Escape') {
                    document.body.removeChild(fullscreenContainer);
                    document.removeEventListener('keydown', keyNavigation);
                } else if (e.key === 'ArrowRight' && index < mediaArray.length - 1) {
                    document.body.removeChild(fullscreenContainer);
                    document.removeEventListener('keydown', keyNavigation);
                    showFullscreenMedia(null, mediaArray, index + 1, tracemapObj);
                } else if (e.key === 'ArrowLeft' && index > 0) {
                    document.body.removeChild(fullscreenContainer);
                    document.removeEventListener('keydown', keyNavigation);
                    showFullscreenMedia(null, mediaArray, index - 1, tracemapObj);
                }
            });
        }
        
        // Fonction pour vérifier si le tutoriel a déjà été affiché
        function hasSeenTutorial() {
            return localStorage.getItem('tracemap_tutorial_seen') === 'true';
        }
        
        // Fonction pour marquer le tutoriel comme vu
        function markTutorialAsSeen() {
            localStorage.setItem('tracemap_tutorial_seen', 'true');
        }
        
        // Fonction pour démarrer le tutoriel
        function startTutorial() {
            const intro = introJs();
            
            intro.setOptions({
                nextLabel: 'Suivant',
                prevLabel: 'Précédent',
                skipLabel: 'X',
                doneLabel: 'Terminer',
                hidePrev: false,
                hideNext: false,
                showProgress: true,
                showBullets: true,
                showStepNumbers: false,
                keyboardNavigation: true,
                exitOnOverlayClick: false,
                exitOnEsc: true,
                tooltipPosition: 'auto',
                disableInteraction: false,
                scrollToElement: true,
                overlayOpacity: 0.8
            });
            
            intro.oncomplete(function() {
                markTutorialAsSeen();
            });
            
            intro.onexit(function() {
                markTutorialAsSeen();
            });
            
            intro.start();
        }
        
        // Vérifier si l'utilisateur a déjà vu le tutoriel
        document.addEventListener('DOMContentLoaded', function() {
            // Attendre que tous les éléments de la carte soient chargés
            setTimeout(() => {
                if (!hasSeenTutorial()) {
                    startTutorial();
                }
            }, 1000);
            
            // Ajouter l'événement pour le bouton de redémarrage du tutoriel
            const restartTutorialBtn = document.getElementById('restart-tutorial');
            if (restartTutorialBtn) {
                restartTutorialBtn.addEventListener('click', function() {
                    startTutorial();
                });
            }
        });
    </script>

    <!-- Conteneur pour l'affichage des médias en plein écran -->
    <div id="fullscreen-container"></div>
@endsection
