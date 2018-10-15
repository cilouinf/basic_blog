<?php
class Image {
    private const SUPPORTED_EXTENSIONS = ['jpg', 'jpeg', 'png'];
    public const    DIRECTORY = 'articles/images/',
                    ARTICLE_MAX_SIZE = 1048576;
    private function __construct() {}

    /**
     * @param string $id
     * @param string $filename
     * @return string
     */
    public static function generateFilename(string $id, string $filename) : string {
        if(empty($filename) || empty($id) || !self::isSupported($filename)) {
            return '';
        }

        $currentTime = new DateTime('now', new DateTimeZone('Europe/Brussels'));
        $fileHash = bin2hex($id . substr($filename, 0, 48) . $currentTime->getTimestamp());
        
        return $fileHash . '.' . self::getExtension($filename);
    }

    /**
     * @param string $filename
     * @return bool
     */
    public static function isSupported(string $filename) : bool {
        $extension = self::getExtension($filename);
        return in_array($extension, self::SUPPORTED_EXTENSIONS);
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function getExtension(string $filename) : string {
        if(empty($filename)) {
            return '';
        }

        $extension = explode('.', $filename);
        return $extension[count($extension) - 1];
    }
}