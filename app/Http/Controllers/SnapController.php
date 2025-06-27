<?php

namespace App\Http\Controllers;

use App\Models\Snap;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SnapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupère uniquement les snaps de moins de 24 heures avec leurs médias associés
        $snaps = Snap::with('media')
            ->where('created_at', '>=', now()->subHours(24))
            ->get();
        return view('snap.index', compact('snaps'));
    }

    // La méthode create a été supprimée car nous utilisons maintenant un modal dans la vue index

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valide les données du formulaire
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'content' => 'required|array',
            'content.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240',
        ]);
        
        // Crée un nouveau snap avec les données validées
        $snap = Snap::create([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);
        
        // Stocke chaque fichier téléversé et crée un enregistrement Media
        foreach ($request->file('content') as $file) {
            // Stocke le fichier dans le disque 'public' au lieu de 'public/snaps'
            $path = $file->store('snaps', 'public');
            $fileType = $file->getClientMimeType();
            
            // Crée un nouvel enregistrement Media associé au Snap
            Media::create([
                'snap_id' => $snap->id,
                'file_path' => Storage::url($path),
                'file_type' => $fileType,
            ]);
        }
        
        // Redirige vers la page d'accueil avec un message de succès
        return redirect()->route('snap.index')->with('success', 'Snap créé avec succès!');
    }
    
    /**
     * Store a newly created resource in storage via AJAX.
     * Cette méthode est similaire à store() mais retourne une réponse JSON pour les requêtes AJAX.
     */
    public function storeAjax(Request $request)
    {
        try {
            // Valide les données du formulaire
            $validated = $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'content' => 'required|array',
                'content.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240',
            ]);
            
            // Crée un nouveau snap avec les données validées
            $snap = Snap::create([
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);
            
            $mediaItems = [];
            
            // Stocke chaque fichier téléversé et crée un enregistrement Media
            foreach ($request->file('content') as $file) {
                // Stocke le fichier dans le disque 'public'
                $path = $file->store('snaps', 'public');
                $fileType = $file->getClientMimeType();
                
                // Crée un nouvel enregistrement Media associé au Snap
                $media = Media::create([
                    'snap_id' => $snap->id,
                    'file_path' => Storage::url($path),
                    'file_type' => $fileType,
                ]);
                
                $mediaItems[] = [
                    'id' => $media->id,
                    'file_path' => $media->file_path,
                    'file_type' => $media->file_type,
                    'is_video' => str_starts_with($fileType, 'video/') || 
                                  in_array(pathinfo($path, PATHINFO_EXTENSION), ['mp4', 'mov', 'avi'])
                ];
            }
            
            // Retourne une réponse JSON avec les informations du snap créé
            return response()->json([
                'success' => true,
                'message' => 'Snap créé avec succès!',
                'snap' => [
                    'id' => $snap->id,
                    'latitude' => $snap->latitude,
                    'longitude' => $snap->longitude,
                    'media' => $mediaItems
                ]
            ]);
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourne une réponse JSON avec le message d'erreur
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du snap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
