<?php

namespace App\Http\Controllers;

use App\File;
use Carbon\Carbon;
use Spatie\Dropbox\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function __construct()
    {
        // Necesitamos obtener una instancia de la clase Client la cual tiene algunos métodos
        // que serán necesarios.
        $this->dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();   
    }

    public function store(Request $request)
    {
        \DB::beginTransaction();
        try{
            // Guardamos el archivo indicando el driver y el método putFileAs el cual recibe
            // el directorio donde será almacenado, el archivo y el nombre.
            // ¡No olvides validar todos estos datos antes de guardar el archivo!
            
            $extension = $request->file('file')->getClientOriginalExtension();
            $name_file = Carbon::now()->format('Y-m-d h:m:s').".".$extension;
            Storage::disk('dropbox')->putFileAs(
                '/archivos', 
                $request->file('file'), $name_file
            );
    
            // Creamos el enlace publico en dropbox utilizando la propiedad dropbox
            // definida en el constructor de la clase y almacenamos la respuesta.
            $response = $this->dropbox->createSharedLinkWithSettings(
                '/archivos/'.$name_file, 
                ["requested_visibility" => "public"]
            );
    
            // Creamos un nuevo registro en la tabla files con los datos de la respuesta.
            $file = File::create([
                'name' => $response['name'],
                'extension' => $extension,
                'size' => $response['size'],
                'public_url' => $response['url']
            ]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            $success = $exception->getMessage();
        }
        // Retornamos un redirección hacía atras
        return response()->json($file);
    }

    // DESCARGAR ARCHIVO
    public function download($id){
        $file = File::whereId($id)->first();
        $file->update(['download' => true]);
        return Storage::disk('dropbox')->download('/archivos/'.$file->name);
    }
    
}
