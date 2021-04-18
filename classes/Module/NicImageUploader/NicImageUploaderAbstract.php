<?php

declare(strict_types=1);

namespace Classroom\Module\NicImageUploader;

/**
 * Images uploader for nicEdit.
 */
abstract class NicImageUploaderAbstract {
    
    /**
     * Uploads file.
     */
    abstract public function upload(array $fileInfo): array;
    
}
