<?php
namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

class ImageUpload
{
    /** Returns a generated filename, null for no upload, or throws an error for unsafe files. */
    public static function save(?UploadedFile $file, string $folder): ?string
    {
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) return null;
        if (! $file->isValid()) throw new \RuntimeException('Image upload failed: ' . $file->getErrorString());
        if ($file->getSize() > 2 * 1024 * 1024) throw new \RuntimeException('Image must be 2 MB or smaller.');
        $info = @getimagesize($file->getTempName());
        $mime = $info['mime'] ?? '';
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (! isset($extensions[$mime]) || ! in_array($file->getMimeType(), array_keys($extensions), true)) {
            throw new \RuntimeException('Upload a real JPG, PNG, or WebP image.');
        }
        $destination = FCPATH . 'uploads/' . $folder;
        if (! is_dir($destination) || ! is_writable($destination)) throw new \RuntimeException('Image folder is not writable.');
        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        $file->move($destination, $filename);
        return $filename;
    }

    public static function url(?string $filename, string $folder): ?string
    {
        if (! $filename || basename($filename) !== $filename) return null;
        return base_url('uploads/' . $folder . '/' . rawurlencode($filename));
    }
}
