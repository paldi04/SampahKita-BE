<?php
namespace App\Helpers;

use Exception;
use Google\Cloud\Storage\StorageClient;

class CloudStorage {

    protected static $cloudStorage;
    protected static $bucketName;

    private static function init() {
        self::$cloudStorage = new StorageClient([
            'keyFilePath' => base_path(env('GOOGLE_APPLICATION_CREDENTIALS'))
        ]);
        self::$bucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
    }

    public static function uploadBase64Image($base64Image, $uploadPath)
    {
        self::init();
        $dataUriScheme = explode(',', $base64Image)[0];
        if (!in_array($dataUriScheme, ['data:image/png;base64', 'data:image/jpg;base64', 'data:image/jpeg;base64'])) {
            throw new Exception('Invalid image format.');
        }
    
        $fileExtension = explode('/', explode(';', $dataUriScheme)[0])[1];
        if (!in_array($fileExtension, ['png', 'jpg', 'jpeg'])) {
            throw new Exception('Invalid image format.');
        }
        
        $objectName = time() . '.' . $fileExtension;
        try {
            self::$cloudStorage->bucket(self::$bucketName)->upload($base64Image, [
                'name' => $uploadPath . '/' . $objectName
            ]);
            
            $object = self::$cloudStorage->bucket(self::$bucketName)->object($objectName);
            $publicUrl = 'https://storage.googleapis.com/' . self::$bucketName . '/' . $uploadPath . '/' . $object->name();
        
            // $updateAccess = self::setPublicAccess($publicUrl);
            // if (!$updateAccess['success']) {
            //     return $updateAccess;
            // }
            return ['success' => true, 'url' => $publicUrl];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public static function setPublicAccess($filePath)
    {
        self::init();
        $objectName = explode(self::$bucketName . '/', $filePath)[1];
        $object = self::$cloudStorage->bucket(self::$bucketName)->object($objectName);
        try {
            $object->update(['acl' => []], ['predefinedAcl' => 'PUBLICREAD']);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public static function delete($filePath)
    {
        self::init();
        if (is_string($filePath)) {
            $filePath = [$filePath];
        }
        try {
            foreach ($filePath as $path) {
                $objectName = explode(self::$bucketName . '/', $path)[1];
                self::$cloudStorage->bucket(self::$bucketName)->object($objectName)->delete();
            }
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

}