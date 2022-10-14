<?php
// src/Service/FileManager.php
namespace App\Service;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager
{
    private $targetDirectory;
    private $slugger;
    private $em;

    //check service.yaml for more infos
    // App\Service\FileManager:
    // arguments:
    //     $targetDirectory: '%upload_directory%'

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function delete($file)
    {
       
        try {

            $filesystem = new Filesystem;
            $filePath = $this->getTargetDirectory().'/'.$file->getUploadName();
            $filesystem->remove($filePath);

        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            throw new \Exception(sprintf('Error deleting "%s"', $file));
        }


    }
    public function download($file)
    {
        $filePath = $this->getTargetDirectory().'/'.$file->getUploadName();
        //$filesystem = new Filesystem;
        
        return $filePath;
        
        
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}