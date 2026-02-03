<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\IndexDocumentJob;
use App\Jobs\ProductImportJob;
use App\Models\Document;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Upload et créer un document pour un site
     */
    public function store(Request $request, Site $site)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,csv,txt',
            'mapping' => 'nullable|json',
        ]);

        if ($request->hasFile('file')) {
            $files = $request->file('file');
            $document = $this->saveDocument($files, $site, 'file');
            if ($request->input('mapping') !== null) {
                $mapping = json_decode($request->mapping, true, 512, JSON_THROW_ON_ERROR);
            }


            Log::info("Document uploadé: {$document->path}");

            // Dispatch indexation (support WooCommerce inclus)
            if($document->extension === 'csv' || $document->extension === 'xlsx' || $document->extension === 'xls') {
                ProductImportJob::dispatch(
                    $document,
                    $mapping,
                    $site
                );
            }else{
                IndexDocumentJob::dispatch($document);
            }

            return response()->json([
                'success' => true,
                'document_id' => $document->id,
                'message' => 'Document uploadé et indexation en cours',
            ]);
        }

    }
    private function moveImage($file)
    {
        $currentDateTime = Carbon::now();
        $formattedDateTime = $currentDateTime->format('Ymd_His');

        $path_file = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets/resources/documents/'), $path_file);

        return "assets/resources/documents/" . $path_file;
    }
    // Méthode pour supprimer une image
    private function deleteImage($path)
    {
        if ( file_exists( public_path($path) ) ) {
            unlink(public_path($path));
        }
    }
    private function saveDocument($files, Site $site, string $type){

        $document = null;
        if (is_array($files)) {

            foreach ($files as $file) {
                $documentPath = $this->moveImage($file);
                $extension = $files->getClientOriginalExtension();
                $document = new Document([ 'id' => (string) Str::uuid(), 'path' => $documentPath, 'type' => $type, 'extension' => $extension]);
                $document = $site->documents()->save($document);
            }

        } else {

            $documentPath = $this->moveImage($files);
            $extension = $files->getClientOriginalExtension();
            $document = new Document([ 'id' => (string) Str::uuid(), 'path' => $documentPath, 'type' => $type, 'extension' => $extension]);
            $document = $site->documents()->save($document);

        }

        return $document;
    }

    /**
     * Liste des documents pour un site
     */
    /*public function index(Site $site)
    {
        $documents = $site->documents()->orderBy('priority')->get();

        return response()->json([
            'success' => true,
            'documents' => $documents,
        ]);
    }*/

    /**
     * Récupérer un document (download)
     */
    /*public function show(Document $document)
    {
        if (!Storage::disk('public')->exists($document->path)) {
            return response()->json(['error' => 'Fichier introuvable'], 404);
        }

        return response()->download(storage_path("app/public/{$document->path}"));
    }*/

    /**
     * Supprimer un document
     */
    /*public function destroy(Document $document)
    {
        try {
            if (Storage::disk('public')->exists($document->path)) {
                Storage::disk('public')->delete($document->path);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document supprimé',
            ]);
        } catch (\Throwable $e) {
            Log::error("Erreur suppression document: {$document->id}", [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer le document',
            ], 500);
        }
    }*/
}
