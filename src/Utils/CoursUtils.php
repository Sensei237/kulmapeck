<?php
namespace App\Utils;

use App\Entity\Cours;
use App\Service\FileUploader;

final class CoursUtils
{
    public static function uploadCourseMedia(Cours $cours, FileUploader $fileUploader)
    {
        if ($cours->getMedia()->imageFileUpload !== null) {
            $fileName = $fileUploader->upload($cours->getMedia()->imageFileUpload, 'media/courses');
            $cours->getMedia()->setImageFile($fileName);
        }

        if ($cours->getMedia()->webMFileUpload !== null) {
            $fileName = $fileUploader->upload($cours->getMedia()->webMFileUpload, 'media/courses');
            $cours->getMedia()->setWebMFile($fileName);
        }

        if ($cours->getMedia()->oggFileUpload !== null) {
            $fileName = $fileUploader->upload($cours->getMedia()->oggFileUpload, 'media/courses');
            $cours->getMedia()->setOggFile($fileName);
        }

        if ($cours->getMedia()->mp4FileUpload !== null) {
            $fileName = $fileUploader->upload($cours->getMedia()->mp4FileUpload, 'media/courses');
            $cours->getMedia()->setMp4File($fileName);
        }
    }
}
