<?php

function productImageDirectory(): string
{
    return '/var/www/html/uploads/products';
}

function productImageExtension(string $fileName): string
{
    return strtolower((string) pathinfo($fileName, PATHINFO_EXTENSION));
}

function listProductLibraryImages(string $directory): array
{
    $images = [];

    foreach (glob($directory . '/*') ?: [] as $path) {
        if (!is_file($path)) {
            continue;
        }

        $extension = productImageExtension($path);
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $images[] = basename($path);
        }
    }

    natcasesort($images);
    return array_values($images);
}

function validateExistingProductImages(array $requestedNames, string $directory): array
{
    $validNames = [];

    foreach ($requestedNames as $requestedName) {
        if (!is_string($requestedName) || $requestedName === '') {
            continue;
        }

        $safeName = basename($requestedName);
        if ($safeName !== $requestedName) {
            throw new InvalidArgumentException('Tên ảnh đã chọn không hợp lệ.');
        }

        if (!in_array(productImageExtension($safeName), ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw new InvalidArgumentException('Thư viện chỉ hỗ trợ ảnh JPG, JPEG, PNG hoặc WebP.');
        }

        $path = $directory . DIRECTORY_SEPARATOR . $safeName;
        if (!is_file($path)) {
            throw new InvalidArgumentException('Một ảnh được chọn không còn tồn tại trong thư viện.');
        }

        if (!in_array($safeName, $validNames, true)) {
            $validNames[] = $safeName;
        }
    }

    return $validNames;
}

function findProductImageByHash(string $temporaryFile, string $directory): ?string
{
    $temporaryHash = sha1_file($temporaryFile);
    if ($temporaryHash === false) {
        return null;
    }

    foreach (listProductLibraryImages($directory) as $fileName) {
        $existingHash = sha1_file($directory . DIRECTORY_SEPARATOR . $fileName);
        if ($existingHash !== false && hash_equals($existingHash, $temporaryHash)) {
            return $fileName;
        }
    }

    return null;
}

