<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * Service for compressing uploaded images for leave request attachments.
 */
class ImageCompressionService
{
    /**
     * Process and optionally compress an uploaded file.
     * Images > threshold are resized. PDFs are stored as-is.
     *
     * @return string The stored file path
     */
    public function processAttachment(UploadedFile $file, string $directory = 'leave_attachments'): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        // For PDFs, just store directly
        if ($extension === 'pdf') {
            return $file->store($directory, 'public');
        }

        // For images, compress if needed
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return $this->compressAndStore($file, $directory);
        }

        // Fallback: store as-is
        return $file->store($directory, 'public');
    }

    /**
     * Compress an image: resize to max 800px width, 80% quality.
     */
    protected function compressAndStore(UploadedFile $file, string $directory): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getPathname());

        // Resize if width exceeds 800px, maintaining aspect ratio
        if ($image->width() > 800) {
            $image->scale(width: 800);
        }

        // Generate filename
        $filename = uniqid() . '_' . time() . '.jpg';
        $path = $directory . '/' . $filename;

        // Encode and store
        $encoded = $image->toJpeg(80);
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}
