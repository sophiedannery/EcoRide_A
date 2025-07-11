<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileImageService
{

    public function __construct(
        private string $uploadDir,
        private SluggerInterface $slugger
    ) {}

    public function updateProfileImage(User $user, UploadedFile $file): void
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($this->uploadDir, $newFilename);

        if ($oldFilename = $user->getPhotoFilename()) {
            $oldPath = $this->uploadDir . '/' . $oldFilename;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $user->setPhotoFilename($newFilename);
    }
}
