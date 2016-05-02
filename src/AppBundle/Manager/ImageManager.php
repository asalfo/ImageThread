<?php
/**
 * Created by PhpStorm.
 * User: asalfo
 * Date: 01/05/16
 * Time: 14:02
 */


namespace AppBundle\Manager;

use AppBundle\Entity\Post;
use AppBundle\Entity\Config;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class ImageManager
{
    const  VALID_TYPE = ['jpg', 'jpeg', 'png', 'gif'];
    const  STATUS_OK = 1;
    const  STATUS_INVALID = 2;
    const  STATUS_INVALID_DIMENSION = 3;
    const  STATUS_INVALID_TYPE = 4;
    const  STATUS_INVALID_SIZE = 5;
    const  STATUS_ERROR = 6;
    const  MAX_WITH = 1920;
    const  MAX_HEIGTH = 1080;

    protected $entityManager;
    protected $rootDirectory;
    protected $uploadDirectory;

    /**
     * ImageManger constructor.
     * @param $rootDirectory
     * @param $uploadDirectory
     */
    public function __construct($entityManager, $rootDirectory, $uploadDirectory)
    {
        $this->entityManager = $entityManager;
        $this->rootDirectory = $rootDirectory;
        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * @return string
     */
    public function getRootDirectory()
    {
        return $this->rootDirectory;
    }

    /**
     * @param string $rootDirectory
     */
    public function setRootDirectory($rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * @return mixed
     */
    public function getUploadDirectory()
    {
        return $this->uploadDirectory;
    }

    /**
     * @param mixed $uploadDirectory
     */
    public function setUploadDirectory($uploadDirectory)
    {
        $this->uploadDirectory = $uploadDirectory;
    }


    public function uploadPath()
    {
        return $this->getRootDirectory() . '/../web/' . $this->getUploadDirectory();
    }


    /**
     * Upload the post image and return the file path if successfull
     * @param UploadedFile $file
     * @param string $title
     * @return bool
     */
    public function uploadFile(UploadedFile $file, $title = "")
    {

        if (null === $file) {
            return self::STATUS_ERROR;
        }

        $status = $this->validateImage($file);
        if ($status == self::STATUS_OK) {
            try {
                $filename = sha1($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                $target = $file->move(
                    $this->uploadPath(),
                    $filename);
                if (file_exists($target->getRealPath())) {
                    $post = new Post();
                    $post->setTitle($title);
                    $post->setImagePath($filename);
                    $this->entityManager->persist($post);
                    $this->entityManager->flush();
                }
            } catch (Exception $ex) {
                $status = self::STATUS_ERROR;
            }
        }
        return $status;
    }


    /**
     * Updates the views count
     * @return int
     */
    public function updateViewCount()
    {
        $count = 1;
        $config = $this->entityManager->getRepository('AppBundle:Config')
            ->findOneBy(array('name' => 'views'));
        if (null == $config) {
            $config = new Config();
            $config->setName('views');
            $config->setValue(1);
            $this->entityManager->persist($config);
            $this->entityManager->flush();
        } else {
            $count = $config->getValue() + 1;
            $config->setValue($count);
            $this->entityManager->persist($config);
            $this->entityManager->flush();
        }

        return $count;
    }


    
    public function CreateExportFile()
    {
        $filename = null;
        $csv_file = $this->createCsv();
        $images_file = $this->zipImages();
        if($images_file && $csv_file){
            try {

                $filename = "/tmp/export/export_" . mt_rand() . ".zip";
                $zip = new \ZipArchive();

                if ($zip->open($filename, \ZipArchive::CREATE) !== TRUE) {
                    return false;
                }
                
                    $new_file = str_replace("/tmp/export/", '', $csv_file);
                    $zip->addFile($csv_file, $new_file);

                $new_file = str_replace("/tmp/export/", '', $images_file);
                $zip->addFile($images_file, $new_file);
                
            } catch (IOExceptionInterface $e) {
                throw  new \Exception("An error occurred while creating file at " . $e->getPath());
            }  
        }
        return $filename;
        
    }
    public function createCsv()
    {
        $filename = null;
        $posts = $this->entityManager->getRepository('AppBundle:Post')
            ->retrievePost();
        $fs = new Filesystem();

        try {
            if (!$fs->exists('/tmp/export')) {
                $fs->mkdir('/tmp/export');
            }
            $filename = "/tmp/export/file_" . mt_rand() . ".csv";
            $fp = fopen($filename, 'w');

            fputcsv($fp, array('Title', 'Filename'));
            foreach ($posts as $post) {
                fputcsv($fp, $post,',','"');
            }
            fclose($fp);
        } catch (IOExceptionInterface $e) {
            throw  new \Exception("An error occurred while creating file at " . $e->getPath());
        }

        return $filename ;

    }


    public function zipImages()
    {
        $filename = null;
        $fs = new Filesystem();
        try {
            if (!$fs->exists('/tmp/export')) {
                $fs->mkdir('/tmp/export');
            }
            $filename = "/tmp/export/images_" . mt_rand() . ".zip";
            $zip = new \ZipArchive();

            if ($zip->open($filename, \ZipArchive::CREATE) !== TRUE) {
                return false;
            }
            $path = $this->uploadPath();
            $files = glob($path . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $new_file = str_replace($path, '', $file);
                    $zip->addFile($file, $new_file);
                }
            }
        } catch (IOExceptionInterface $e) {
            throw  new \Exception("An error occurred while creating file at " . $e->getPath());
        }
        
        $zip->close();

        return $filename;
    }


    public function addDirectoryToZip($zip, $dir, $base)
    {
        $newFolder = str_replace($base, '', $dir);
        $zip->addEmptyDir($newFolder);
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                $zip = $this->addDirectoryToZip($zip, $file, $base);
            } else {
                $newFile = str_replace($base, '', $file);
                $zip->addFile($file, $newFile);
            }
        }
        return $zip;
    }


    /**
     * Validate an image
     * @param $file
     * @return array
     */
    private function validateImage($file)
    {
        if (($file instanceof UploadedFile) && $file->getError() == '0') {
            if ($file->getSize() <= 20000000) { //20M
                if (in_array(strtolower($file->getClientOriginalExtension()), self::VALID_TYPE)) {
                    list($width, $height) = getimagesize($file->getRealPath());
                    if ($width <= self::MAX_WITH && $height <= self::MAX_HEIGTH) {
                        $status = self::STATUS_OK;
                    } else {
                        $status = self::STATUS_INVALID_DIMENSION;
                    }

                } else {
                    $status = self::STATUS_INVALID_TYPE;
                }
            } else {
                $status = self::STATUS_INVALID_SIZE;
            }

        } else {
            $status = self::STATUS_INVALID;
        }

        return $status;
    }

}