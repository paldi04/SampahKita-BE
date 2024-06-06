<?php
namespace App\Helpers;

use Exception;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
        if (!$tmpFileObject = self::validateBase64($base64Image, ['png', 'jpg', 'jpeg'])) {
            return ['success' => false, 'error' => 'Invalid image format.'];
        }
    
        $storedFilePath = self::storeTemporaryLocalFile($tmpFileObject, $uploadPath);
        try {
            self::$cloudStorage->bucket(self::$bucketName)->upload(Storage::disk('public')->get($storedFilePath), [
                'name' => $storedFilePath
            ]);
            Storage::disk('public')->delete($storedFilePath);

            $publicUrl = 'https://storage.googleapis.com/' . self::$bucketName . '/' . $storedFilePath;
        
            $updateAccess = self::setPublicAccess($publicUrl);
            if (!$updateAccess['success']) {
                return $updateAccess;
            }
            return ['success' => true, 'url' => $publicUrl];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public static function validateBase64(string $base64data, array $allowedMimeTypes)
    {
        // strip out data URI scheme information (see RFC 2397)
        if (str_contains($base64data, ';base64')) {
            list(, $base64data) = explode(';', $base64data);
            list(, $base64data) = explode(',', $base64data);
        }
    
        // strict mode filters for non-base64 alphabet characters
        if (base64_decode($base64data, true) === false) {
            return false;
        }
    
        // decoding and then re-encoding should not change the data
        if (base64_encode(base64_decode($base64data)) !== $base64data) {
            return false;
        }
    
        $fileBinaryData = base64_decode($base64data);
    
        // temporarily store the decoded data on the filesystem to be able to use it later on
        $tmpFileName = tempnam(sys_get_temp_dir(), 'medialibrary');
        file_put_contents($tmpFileName, $fileBinaryData);
    
        $tmpFileObject = new File($tmpFileName);
    
        // guard against invalid mime types
        $allowedMimeTypes = Arr::flatten($allowedMimeTypes);
    
        // if there are no allowed mime types, then any type should be ok
        if (empty($allowedMimeTypes)) {
            return $tmpFileObject;
        }
    
        // Check the mime types
        $validation = Validator::make(
            ['file' => $tmpFileObject],
            ['file' => 'mimes:' . implode(',', $allowedMimeTypes)]
        );
    
        if ($validation->fails()) {
            return false;
        }
    
        return $tmpFileObject;
    }
    
    public static function storeTemporaryLocalFile(File $tmpFileObject, $uploadPath = 'uploadPath')
    {
        $tmpFileObjectPathName = $tmpFileObject->getPathname();
    
        $file = new UploadedFile(
            $tmpFileObjectPathName,
            $tmpFileObject->getFilename(),
            $tmpFileObject->getMimeType(),
            0,
            true
        );
    
        $storedFile = $file->store($uploadPath, ['disk' => 'public']);
        unlink($tmpFileObjectPathName); // delete temp file
        return $uploadPath . '/' . basename($storedFile);
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