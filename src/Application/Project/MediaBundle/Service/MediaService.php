<?php

namespace App\Application\Project\MediaBundle\Service;

use App\Entity\SonataMediaMedia;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Process\Process;

class MediaService
{

    protected ObjectManager $em;
    protected array $fileMimeTypes = [ 'application/pdf', 'application/x-pdf', 'application/rtf', 'text/html', 'text/rtf', 'text/plain' ];
    protected array $imageMimeTypes = [
        'image/jpeg',
        'image/jpeg',
        'image/png',
        'image/x-png',
        'image/webp',
    ];


    public function __construct(
        protected ManagerRegistry $managerRegistry,
        protected string $projectDir,
    )
    {
        $this->em = $this->managerRegistry->getManager();
    }

    public function getConxtext(UploadedFile $file){

        if(  in_array($file->getClientMimeType(), $this->imageMimeTypes) ){
            return "image";

        }else if(in_array($file->getClientMimeType(), $this->fileMimeTypes)){
            return "file";
        }else{
            return "other";
        }

    }

    public function createMedia(UploadedFile $file, SonataMediaMedia $media = null)
    {
        $context = $this->getConxtext($file);

        if(!$media)
            $media = new SonataMediaMedia();

        $media->setName($file->getClientOriginalName());
        $media->setDescription(null);
        $media->setEnabled(true);
        $media->setProviderName($this->getProviderName($file));
        $media->setProviderStatus(1);
        $media->setProviderMetadata([ "filename"=> $file->getClientOriginalName() ]);
        $media->setProviderReference($file->getClientOriginalName());
        $media->setContentType($file->getClientMimeType());
        $media->setContext('default');

        if($context === "image"){
            $media->setWidth(getimagesize($file)[0]);
            $media->setHeight(getimagesize($file)[1]);
            $media->setSize($file->getSize());
            $media->setLength(null);
        }

        $fileName = $this->upload($file, 'default/0001/01');
        $media->setProviderReference($fileName);

        $this->em->persist($media);
        $this->em->flush();

        /** Sincroniza as Thumbnails */
        $this->syncThumbnails();

        return $media;
    }

    public function upload(UploadedFile $file, $folder)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        //dd($originalFilename);
        //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->projectDir. "/public/uploads/" .$folder, $fileName);
        } catch (FileException $e) {
        }

        return $fileName;
    }

    public function syncThumbnails()
    {
        $process = Process::fromShellCommandline('bin/console sonata:media:sync-thumbnails sonata.media.provider.image default', $this->projectDir,  null, null );
        $process->run();
    }

    public function getProviderName($file)
    {
        if(in_array($file->getClientMimeType(),$this->getProviders() ))
            return $this->getProviders()[$file->getClientMimeType()];

        return 'sonata.media.provider.file';
    }

    public function getProviders()
    {
       return [
            'application/pdf' => 'sonata.media.provider.file',
            'application/x-pdf' => 'sonata.media.provider.file',
            'application/rtf' => 'sonata.media.provider.file',
            'text/html' => 'sonata.media.provider.file',
            'text/rtf' => 'sonata.media.provider.file',
            'text/plain' => 'sonata.media.provider.file',
            'text/csv' => 'sonata.media.provider.file',

            'image/jpg' => 'sonata.media.provider.image',
            'image/jpeg' => 'sonata.media.provider.image',
            'image/png' => 'sonata.media.provider.image',
            'image/x-png' => 'sonata.media.provider.image',
        ];
    }

}