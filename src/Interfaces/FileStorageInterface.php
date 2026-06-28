<?php

namespace App\Interfaces;

interface FileStorageInterface
{
    public function upload(array $fichier, string $prefixe = ''): string;
    public function delete(string $nomFichier): bool;
}

?>