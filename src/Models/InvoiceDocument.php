<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JiagBrody\LaravelFacturaMx\Sat\Helper\ConvertXmlContentToObjectHelper;

class InvoiceDocument extends Model
{
    use HasFactory;

    const SAVE_TO_STORAGE_DISK = 'public';

    const FOLDER_NAME = 'media/documents'; // aquí se guardan todos los archivos de la aplicación

    protected $fillable = [
        'document_type_id',
        'documentable_type',
        'documentable_id',
        'file_name',
        'file_path',
        'mime_type',
        'extension',
        'storage',
    ];

    protected $appends = [
        'file',
        'public_url',
        'location_root',
    ];

    public function documentable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /*
     * Genero el nombre del archivo que se va a guardar.
     */
    public static function getInitialFileName($model, string $fileExtension, ?string $addMoreToName = null): string
    {
        return Str::slug(class_basename($model).'-'.$model->id.($model?->document_name ? '-'.$model->document_name : '')).($addMoreToName ? '-'.$addMoreToName : '').'.'.$fileExtension;
    }

    /*
     * Genero la ruta donde se guardaran los archivos
     */
    public static function getInitialFilePathUsingModel($model): string
    {
        $folder = self::FOLDER_NAME.'/'.(strtolower(class_basename($model)));

        return $folder.'/'.now()->format('Y').'/'.now()->format('m');
    }

    /*
     * Concatenación del archivo.
     */
    public function getFileAttribute(): string
    {
        return $this->file_path.'/'.$this->file_name.'.'.$this->extension;
    }

    /*
     * Obtiene el link público para mostrar por web.
     */
    public function getPublicUrlAttribute(): string
    {
        $file = $this->file;

        return (Storage::disk($this->storage)->exists($file)) ? asset(Storage::url($file)) : 'The file does not exists';
    }

    /*
     * Obtiene la ruta completa en el sistema.
     */
    public function getLocationRootAttribute(): string
    {
        $file = $this->file;

        return (Storage::disk($this->storage)->exists($file)) ? storage_path('app/'.$this->storage.'/'.$file) : 'The file does not exists';
    }

    /*
     * Obtiene el documento en lectura (string) para trabajarlo.
     */
    public static function obtainDocumentContent(InvoiceDocument $document): ?string
    {
        if (Storage::disk($document->storage)->exists($document->file)) {
            return Storage::disk($document->storage)->get($document->file);
        }

        return null;
    }

    /*
     * Leer el XML en formato
     */
    public static function xmlDocumentReadingConverter(InvoiceDocument $document, $associative = null, $depth = 512, $flags = 0)
    {
        $contents = InvoiceDocument::obtainDocumentContent($document);

        if ($contents === null) {
            return false;
        }

        return ConvertXmlContentToObjectHelper::make($contents, $associative);
    }
}
