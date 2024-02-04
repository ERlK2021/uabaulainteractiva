<?php

class GitHubFileDoesNotExist extends RuntimeException {
    public function __construct($url, $code = 0) {
        $message = "El fitxer especificat no existeix: $url";
        return parent::__construct($message, $code);
    }
}

class SpecifiedUrlNotADirectory extends RuntimeException {
    public function __construct($url, $code = 0) {
        $message = "L'URL no apunta a un directori: " . urldecode($url);
        return parent::__construct($message, $code);
    }
}

class DirectoryAlreadyExists extends RuntimeException {
    public function __construct($directory, $code = 0) {
        $message = "Un problema amb el mateix nom ja existeix: $directory";
        return parent::__construct($message, $code);
    }
}

class FileTooLarge extends RuntimeException {
    public function __construct($file, $size, $maxSize, $code = 0) {
        $message = "La mida del fitxer '$file' supera el màxim ($size > $maxSize)";
        return parent::__construct($message, $code);
    }
}

class WrongFileExtension extends RuntimeException {
    public function __construct($file, $code = 0) {
        $message = "L'extensio del fitxer '$file' no és vàlida";
        return parent::__construct($message, $code);
    }
}