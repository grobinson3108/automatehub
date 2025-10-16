<?php

$serverUrl = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/convert-to-url.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: text/html');

    $gdInstalled = extension_loaded('gd') ? 'Installed ✅' : 'Not Installed ❌';
    $curlInstalled = function_exists('curl_version') ? 'Installed ✅' : 'Not Installed ❌';
    $permissions = is_writable(__DIR__) ? 'Writable ✅' : 'Not Writable ❌ (Check folder permissions)';

    echo "<html><head><title>PNG to JPG Conversion API</title>";
    echo "<style>";
    echo "body { font-family: 'Roboto', sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }";
    echo "h1 { background-color: #2563eb; color: white; padding: 20px; text-align: center; margin: 0; }";
    echo "div.container { padding: 30px; margin: 30px auto; max-width: 800px; background: white; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); }";
    echo "code, pre { background-color: #e5e7eb; padding: 10px; border-radius: 5px; display: block; margin-bottom: 15px; }";
    echo "ul { line-height: 1.8; }";
    echo "li { margin-bottom: 8px; }";
    echo "button { padding: 10px 20px; background-color: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 15px; }";
    echo "button:hover { background-color: #1e40af; }";
    echo "</style>";
    echo "<link href='https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap' rel='stylesheet'>";
    echo "</head><body>";
    echo "<h1>PNG to JPG Conversion API</h1>";
    echo "<div class='container'>";
    echo "<p>This API allows you to convert PNG images to JPG format with optional quality adjustment and file naming.</p>";

    echo "<h2>Server Information</h2>";
    echo "<ul>";
    echo "<li><strong>GD Library:</strong> $gdInstalled</li>";
    echo "<li><strong>cURL Library:</strong> $curlInstalled</li>";
    echo "<li><strong>Folder Permissions:</strong> $permissions</li>";
    echo "</ul>";

    echo "<h2>Usage</h2>";
    echo "<h3>Endpoint</h3>";
    echo "<code>$serverUrl</code>";

    echo "<h3>HTTP Method</h3>";
    echo "<code>POST</code>";

    echo "<h3>Parameters</h3>";
    echo "<ul>";
    echo "<li><strong>url</strong> (Required): The URL of the PNG image to be converted.</li>";
    echo "<li><strong>name</strong> (Optional): Custom name for the image. Spaces will be replaced with dashes. If not provided, a unique name will be generated.</li>";
    echo "<li><strong>folder</strong> (Optional): Folder name where the image will be saved. Default is <code>converted</code>.</li>";
    echo "<li><strong>quality</strong> (Optional): Image quality (1 to 100). Default is 100.</li>";
    echo "</ul>";

    echo "<h3>Response</h3>";
    echo "<p>The response will be in JSON format with a URL to the converted image:</p>";
    echo "<pre>{ \"url\": \"$serverUrl/converted/Your-Custom-Name.jpg\" }</pre>";

    echo "<h3>Example cURL Request</h3>";
    echo "<pre>curl -X POST $serverUrl \
    -F \"url=https://example.com/your-image.png\" \
    -F \"name=Your Custom Name With Spaces\" \
    -F \"folder=converted\"</pre>";

    echo "<h3>Important Notes</h3>";
    echo "<ul>";
    echo "<li>Ensure that your server has write permissions for the <code>converted</code> folder.</li>";
    echo "<li>Make sure the URL provided points to a valid PNG image.</li>";
    echo "</ul>";
    echo "<button onclick=\"location.reload()\">Refresh</button>";

    echo "</div></body></html>";
    exit;
}

// Proceed with the POST request handling...

function convertPngToJpg($url, $outputPath, $quality = 100, $width = null, $height = null)
{
    $image = file_get_contents($url);
    if ($image === false) {
        die(json_encode(["error" => "Failed to download the image."]));
    }

    $pngImage = imagecreatefromstring($image);
    if (!$pngImage) {
        die(json_encode(["error" => "Invalid PNG file."]));
    }

    $originalWidth = imagesx($pngImage);
    $originalHeight = imagesy($pngImage);

    $jpgImage = imagecreatetruecolor($originalWidth, $originalHeight);
    imagecopy($jpgImage, $pngImage, 0, 0, 0, 0, $originalWidth, $originalHeight);

    if (!file_exists(dirname($outputPath))) {
        mkdir(dirname($outputPath), 0777, true);
    }

    if (!imagejpeg($jpgImage, $outputPath, $quality)) {
        die(json_encode(["error" => "Failed to save JPG file. Check file permissions."]));
    }

    imagedestroy($pngImage);
    imagedestroy($jpgImage);

    return $outputPath;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'] ?? '';
    $quality = 100;
    $name = isset($_POST['name']) ? preg_replace('/[^a-zA-Z0-9-_]/', '', str_replace(' ', '-', $_POST['name'])) : uniqid();
    $folder = isset($_POST['folder']) ? rtrim($_POST['folder'], '/') . '/' : 'converted/';

    if (!$url) {
        die(json_encode(["error" => "URL is required."]));
    }

    $folderPath = __DIR__ . '/' . $folder;

    if (!file_exists($folderPath)) {
        if (!mkdir($folderPath, 0777, true)) {
            die(json_encode(["error" => "Failed to create directory: $folderPath"]));
        }
    }

    $outputPath = $folderPath . $name . '.jpg';

    $convertedImagePath = convertPngToJpg($url, $outputPath, $quality);

    if (file_exists($convertedImagePath)) {
        $imageURL = 'https://' . $_SERVER['HTTP_HOST'] . '/api/' . $folder . basename($convertedImagePath);
        header('Content-Type: application/json');
        echo json_encode(["url" => $imageURL]);
    } else {
        die(json_encode(["error" => "Conversion failed."]));
    }
}
