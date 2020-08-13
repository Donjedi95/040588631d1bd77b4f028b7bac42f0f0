<?php

namespace App\Service;

use Exception;

trait FileDownloaderTrait
{
    protected function getContentsFromUrl($url)
    {
        return file_get_contents($url);
    }

    protected function getFileExtension($imageName)
    {
        $parts = explode('.', $imageName);
        return $parts[count($parts) - 1];
    }

    /**
     * @param $contents
     * @param $imageName
     * @param $fullPath
     * @param array $allowed
     * @throws Exception
     */
    protected function saveContents($contents, $imageName, $fullPath, $allowed = []): void
    {
        if (!empty($allowed)) {
            if (!in_array(strtolower($this->getFileExtension($imageName)), $allowed)) {
                throw new Exception('The file is not valid');
            }
        }

        file_put_contents($fullPath, $contents);
    }

    protected function checkFileExists(string $fullPath)
    {
        return file_exists($fullPath);
    }

    protected function createNewDirectory($directory): void
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
    }
}
