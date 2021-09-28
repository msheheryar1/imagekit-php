<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ImageKit\ImageKit;

if (php_sapi_name() !== 'cli') {
    exit;
}

$public_key = 'your_public_key';
$your_private_key = 'your_private_key';
$url_end_point = 'https://ik.imagekit.io/demo';

$sample_file_url = 'https://cdn.pixabay.com/photo/2020/02/04/22/29/owl-4819550_960_720.jpg';
$sample_file_path = '/sample_image.jpg';
$sample_file_image_kit_url = $url_end_point . '/sample.jpg';

$imageKit = new ImageKit(
    $public_key,
    $your_private_key,
    $url_end_point
);

echo "\n\n-------------------------------------------------------------------\n\n";

// URL for Image with relative path

$imageURL = $imageKit->url(['path' => '/default-image.jpg']);

echo('URL for Image with relative path : ' . $imageURL);

echo "\n\n-------------------------------------------------------------------\n\n";

// URL for Image with relative path and custom URL Endpoint

$imageURL = $imageKit->url([
    'urlEndpoint' => 'https://ik.imagekit.io/test',
    'path' => '/default-image.jpg',
]);

echo('URL for Image with relative path and custom URL Endpoint : ' . $imageURL);

echo "\n\n-------------------------------------------------------------------\n\n";

//  URL for Image with absolute url

$imageURL = $imageKit->url([
    'src' => 'https://ik.imagekit.io/test/default-image.jpg'
]);

echo('URL for Image with absolute url : ' . $imageURL);

echo "\n\n-------------------------------------------------------------------\n\n";

// Resizing Images

$imageURL = $imageKit->url([
    'path' => '/default-image.jpg',
    'transformation' => [
        [
            'height' => '300',
            'width' => '400',
        ],
    ],
]);

echo('Resized Image Url : ' . $imageURL);
echo "\n\n-------------------------------------------------------------------\n\n";

// Quality manipulation

$imageURL = $imageKit->url([
    'path' => '/default-image.jpg',
    'transformation' => [
        [
            'quality' => '40',
        ],
    ],
]);

echo('Quality Manipulated Image Url : ' . $imageURL);
echo "\n\n-------------------------------------------------------------------\n\n";

// Chained transformation

$imageURL = $imageKit->url([
    'path' => '/default-image.jpg',
    'transformation' => [
        [
            'height' => '300',
            'width' => '400',
        ],
        [
            'rotation' => '90'
        ]
    ],
]);

echo('Chained transformation Image Url : ' . $imageURL);
echo "\n\n-------------------------------------------------------------------\n\n";

// Adding overlays to images in PHP


$imageURL = $imageKit->url([
    'path' => '/default-image.jpg',
    'urlEndpoint' => 'https://ik.imagekit.io/pshbwfiho',

    // It means first resize the image to 400x300 and then rotate 90 degree
    'transformation' => [
        [
            'height' => '300',
            'width' => '300',
            'overlayImage' => 'default-image.jpg',
            'overlayWidth' => '100',
            'overlayX' => '0',
            'overlayImageBorder' => '10_CDDC39' // 10px border of color CDDC39
        ]
    ],
]);

echo('Overlay Image Url : ' . $imageURL);
echo "\n\n-------------------------------------------------------------------\n\n";

// Sharpening and contrast transforms and a progressive JPG image

$imageURL = $imageKit->url([
    'path' => '/sample_image.jpg',
    'transformation' => [
        [
            'format' => 'jpg',
            'progressive' => true,
            'effectSharpen' => '-',
            'effectContrast' => '1',
        ],
    ],
]);

echo('Sharpening and contrast transforms : ' . $imageURL);
echo "\n\n-------------------------------------------------------------------\n\n";

// Signed url
$imageURL = $imageKit->url([
    'path' => '/default-image.jpg',
    'transformation' => [
        [
            'height' => '300',
            'width' => '400',
        ],
    ],
    'signed' => true,
    'expireSeconds' => 300,
]);

echo('Signed url : ' . $imageURL);

echo "\n\n-------------------------------------------------------------------\n\n";

// Upload Image - Base64

$img = file_get_contents(__DIR__ . '/sample_image.jpeg');

// Encode the image string data into base64
$encodedImageData = base64_encode($img);
$uploadFile = $imageKit->upload([
    'file' => $encodedImageData,
    'fileName' => 'sample-base64-upload',
    'folder' => '/php-sample',
    'tags' => ['abd', 'def'],
    'useUniqueFileName' => false,
    'customCoordinates' => implode(',', ['10', '10', '100', '100'])
]);

echo('Upload base64 encoded file : ' . json_encode($uploadFile, JSON_PRETTY_PRINT));

echo "\n\n-------------------------------------------------------------------\n\n";

// Upload Image - Binary

$uploadFile = $imageKit->upload([
    'file' => fopen(__DIR__ . '/sample_image.jpeg', 'r'),
    'fileName' => 'sample-binary',
    'folder' => '/php-sample',
    'tags' => ['tag1', 'tag2'],
    'useUniqueFileName' => false,
    'customCoordinates' => implode(',', ['10', '10', '100', '100'])
]);

echo('Upload binary file : ' . json_encode($uploadFile, JSON_PRETTY_PRINT));

echo "\n\n-------------------------------------------------------------------\n\n";

// Upload Image - URL

$uploadFile = $imageKit->upload([
    'file' => $sample_file_url,
    'fileName' => 'sample-url',
    'folder' => '/php-sample',
    'responseFields' => implode(',', ['isPrivateFile', 'customCoordinates']),
    'useUniqueFileName' => false,
]);

echo('Upload with url : ' . json_encode($uploadFile, JSON_PRETTY_PRINT));

echo "\n\n-------------------------------------------------------------------\n\n";

// List Files

$listFiles = $imageKit->listFiles([
    'path' => '/php-sample',
]);

echo('List files : ' . json_encode($listFiles, JSON_PRETTY_PRINT));

$files = $listFiles->success;

echo "\n\n-------------------------------------------------------------------\n\n";

$randomFile = $files[array_rand((array)$files)];
$fileId = $randomFile->fileId;

// Get file details
$getFileDetails = $imageKit->getFileDetails($fileId);
echo('File details : ' . json_encode($getFileDetails, JSON_PRETTY_PRINT));

echo "\n\n-------------------------------------------------------------------\n\n";

// Update details

$updateFileDetails = $imageKit->updateFileDetails($fileId, ['tags' => ['image_tag'], 'customCoordinates' => '100,100,100,100']);
echo('Updated detail : ' . json_encode($updateFileDetails, JSON_PRETTY_PRINT));

echo "\n\n-------------------------------------------------------------------\n\n";

$fileIds = array_map(function ($f) { return $f->fileId; }, $files);
$tags = ['image_tag_1', 'image_tag_2'];

// Bulk Add Tags

$bulkAddTags = $imageKit->bulkAddTags($fileIds, $tags);
echo('Bulk Add Tags Response : ' . json_encode($bulkAddTags, JSON_PRETTY_PRINT));

// Bulk Remove Tags
echo "\n\n-------------------------------------------------------------------\n\n";

$bulkAddTags = $imageKit->bulkRemoveTags($fileIds, $tags);
echo('Bulk Remove Tags Response : ' . json_encode($getFileDetails, JSON_PRETTY_PRINT));

// ---- Fixture for copy

$fixtureForCopy = $imageKit->upload([
    'file' => $sample_file_url,
    'fileName' => 'sample-copy',
    'folder' => '/php-sample',
    'responseFields' => implode(',', ['isPrivateFile', 'customCoordinates']),
    'useUniqueFileName' => false,
]);

$fixtureForCopy = $fixtureForCopy->success;

// -----

// Copy file
echo "\n\n-------------------------------------------------------------------\n\n";

$copyFile = $imageKit->copyFile($fixtureForCopy->filePath, '/php-sample/folder1');

echo('Copy file : ' . json_encode($copyFile, JSON_PRETTY_PRINT));

// ---- Fixture for move

$fixtureForMove = $imageKit->upload([
    'file' => $sample_file_url,
    'fileName' => 'sample-move',
    'folder' => '/php-sample',
    'responseFields' => implode(',', ['isPrivateFile', 'customCoordinates']),
    'useUniqueFileName' => false,
]);

$fixtureForMove = $fixtureForMove->success;

// -----

// Move file
echo "\n\n-------------------------------------------------------------------\n\n";

$moveFile = $imageKit->moveFile($fixtureForMove->filePath, '/php-sample/folder2');

echo('Move file : ' . json_encode($moveFile, JSON_PRETTY_PRINT));


// ---- Fixture for rename

$fixtureForRename = $imageKit->upload([
    'file' => $sample_file_url,
    'fileName' => 'sample-rename',
    'folder' => '/php-sample',
    'responseFields' => implode(',', ['isPrivateFile', 'customCoordinates']),
    'useUniqueFileName' => true,
]);

$fixtureForRename = $fixtureForRename->success;

// -----

// Rename file
echo "\n\n-------------------------------------------------------------------\n\n";

$renameFile = $imageKit->renameFile($fixtureForRename->filePath, 'sample-renamed');

echo('Rename file : ' . json_encode($renameFile, JSON_PRETTY_PRINT));

// Create Folder
echo "\n\n-------------------------------------------------------------------\n\n";

$createFolder = $imageKit->createFolder('new-folder', '/php-sample');
echo('Create folder : ' . json_encode($createFolder, JSON_PRETTY_PRINT));

// Delete Folder
echo "\n\n-------------------------------------------------------------------\n\n";

$deleteFolder = $imageKit->deleteFolder('php-sample/new-folder');
echo('Delete folder : ' . json_encode($deleteFolder, JSON_PRETTY_PRINT));

// --- Fixture for Copy Folder
$imageKit->createFolder('folder-for-copy-and-move', '/php-sample');
$imageKit->createFolder('folder-to-copy-to', '/php-sample');
$imageKit->createFolder('folder-to-move-to', '/php-sample');
// ---

// Copy Folder
echo "\n\n-------------------------------------------------------------------\n\n";

$copyFolder = $imageKit->copyFolder('/php-sample/folder-for-copy-and-move', '/php-sample/folder-to-copy-to');
echo('Copy folder : ' . json_encode($copyFolder, JSON_PRETTY_PRINT));

// Move Folder
echo "\n\n-------------------------------------------------------------------\n\n";

$moveFolder = $imageKit->moveFolder('/php-sample/folder-for-copy-and-move', '/php-sample/folder-to-copy-to');
echo('Move folder : ' . json_encode($copyFolder, JSON_PRETTY_PRINT));

// Get Copy Folder Job Status
echo "\n\n-------------------------------------------------------------------\n\n";

$bulkJob = $imageKit->getBulkJobStatus($copyFolder->success->jobId);
echo('Copy Folder Job Status : ' . json_encode($bulkJob, JSON_PRETTY_PRINT));


// Get Move Folder Job Status
echo "\n\n-------------------------------------------------------------------\n\n";

$bulkJob = $imageKit->getBulkJobStatus($moveFolder->success->jobId);
echo('Move Folder Job Status : ' . json_encode($bulkJob, JSON_PRETTY_PRINT));

// Auth params
echo "\n\n-------------------------------------------------------------------\n\n";

$authenticationParameters = $imageKit->getAuthenticationParameters();

echo('Auth params : ' . json_encode($authenticationParameters, JSON_PRETTY_PRINT));

//  Phash distance
echo "\n\n-------------------------------------------------------------------\n\n";

$distance = $imageKit->pHashDistance('2d5ad3936d2e015b', '2d6ed293db36a4fb');;
echo('Phash Distance : ' . $distance);

//  Similarity
echo "\n\n-------------------------------------------------------------------\n\n";

$similarity = $imageKit->evaluateSimilarity('2d5ad3936d2e015b', '2d6ed293db36a4fb');;
echo('Similarity : ' . json_encode($similarity, JSON_PRETTY_PRINT));
echo("\n");
