<?php

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Google\Cloud\Storage\StorageClient;

function uploadBase64Image($base64Image, $uploadPath)
{

    if (!$tmpFileObject = validateBase64($base64Image, ['png', 'jpg', 'jpeg'])) {
        return [
            'error' => 'Invalid image format.'
        ];
    }

    $storedFilePath = storeFile($tmpFileObject, $uploadPath);

    if (!$storedFilePath) {
        return [
            'error' => 'Something went wrong, the file was not stored.'
        ];
    }

    return [
        'url' => 'public/' . $storedFilePath
    ];
}

function validateBase64(string $base64data, array $allowedMimeTypes)
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

function storeFile(File $tmpFileObject, $uploadPath = 'uploadPath')
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

function uploadBase64ImageToGoogleCloudStorage($base64Image, $uploadPath)
{
    $dataUriScheme = explode(',', $base64Image)[0];
    if (!in_array($dataUriScheme, ['data:image/png;base64', 'data:image/jpg;base64', 'data:image/jpeg;base64'])) {
        throw new Exception('Invalid image format.');
    }

    $fileExtension = explode('/', explode(';', $dataUriScheme)[0])[1];
    if (!in_array($fileExtension, ['png', 'jpg', 'jpeg'])) {
        throw new Exception('Invalid image format.');
    }
    
    $bucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
    $objectName = time() . '.' . $fileExtension;
    try {
        $storage = new StorageClient([
            'keyFilePath' => base_path(env('GOOGLE_APPLICATION_CREDENTIALS')),
        ]);  
        $storage->bucket($bucketName)->upload($base64Image, [
            'name' => $uploadPath . '/' . $objectName
        ]);
    
        $object = $storage->bucket($bucketName)->object($objectName);
        $publicUrl = 'https://' . $bucketName . '.storage.googleapis.com/' . $uploadPath . '/' . $object->name();
    
        return ['success' => true, 'url' => $publicUrl];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}