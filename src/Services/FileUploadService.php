<?php

namespace Moi\UserAppClaude\Services;

class FileUploadService
{
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif'];
    private const MAX_SIZE = 5242880; // 5MB
    private const UPLOAD_PATH = __DIR__ . '/../../public/uploads/profile_pictures/';

    public function __construct()
    {
        // Create upload directory if it doesn't exist
        if (!file_exists(self::UPLOAD_PATH)) {
            mkdir(self::UPLOAD_PATH, 0755, true);
        }
    }

    public function uploadProfilePicture(array $file): array
    {
        try {
            // Validate file
            $this->validateFile($file);

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('profile_', true) . '.' . $extension;
            $filepath = self::UPLOAD_PATH . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new \RuntimeException('Failed to move uploaded file.');
            }

            return [
                'success' => true,
                'filename' => $filename
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function validateFile(array $file): void
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload failed with error code ' . $file['error']);
        }

        // Check file size
        if ($file['size'] > self::MAX_SIZE) {
            throw new \RuntimeException('File size exceeds limit of 5MB.');
        }

        // Check file type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, self::ALLOWED_TYPES)) {
            throw new \RuntimeException('Invalid file type. Allowed types: JPG, PNG, GIF');
        }
    }

    public function deleteProfilePicture(string $filename): bool
    {
        $filepath = self::UPLOAD_PATH . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}


