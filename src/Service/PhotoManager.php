<?php
// src/Service/PhotoManager.php
namespace App\Service;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class PhotoManager
{
    private $targetPhotoDirectory;
    private $slugger;
    private $em;

        /*App\Service\PhotoManager:
            arguments:
                $targetPhotoDirectory: '%upload_photo_directory%'*/

    public function __construct($targetPhotoDirectory, SluggerInterface $slugger)
    {
        $this->targetPhotoDirectory = $targetPhotoDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetPhotoDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function delete($file)
    {

        try {

            $filesystem = new Filesystem;
            $filePath = $this->getTargetPhotoDirectory().'/'.$file;
            $filesystem->remove($filePath);

        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            throw new \Exception(sprintf('Error deleting "%s"', $file));
        }


    }
    public function download($file)
    {
        $filePath = $this->getTargetPhotoDirectory().'/'.$file->getUploadName();
        //$filesystem = new Filesystem;

        return $filePath;


    }

    public function getTargetPhotoDirectory()
    {
        return $this->targetPhotoDirectory;
    }
}