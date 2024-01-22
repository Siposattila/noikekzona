<?php

namespace App\Constant;

class FileConstant
{
    public const FILE_UPLOAD_DIR = 'uploads/files';

    public const FILE_MIME_TYPE_PDF = 'application/pdf';
    public const FILE_MIME_TYPE_PNG = 'image/png';
    public const FILE_MIME_TYPE_JPG = 'image/jpeg';

    public const FILE_MIME_TYPES = [
        self::FILE_MIME_TYPE_PDF,
        self::FILE_MIME_TYPE_PNG,
        self::FILE_MIME_TYPE_JPG,
    ];

    public const FILE_MIME_TYPES_BY_EXTENSION = [
        'pdf' => self::FILE_MIME_TYPE_PDF,
        'png' => self::FILE_MIME_TYPE_PNG,
        'jpg' => self::FILE_MIME_TYPE_JPG,
        'jpeg' => self::FILE_MIME_TYPE_JPG,
    ];
}
