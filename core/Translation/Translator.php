<?php

namespace Core\Translation;

use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Symfony\Component\Translation\Loader\ArrayLoader;

class Translator
{
    private static $instance = null;
    private $translator;
    private $locale;
    private $fallbackLocale;

    private function __construct()
    {
        // Usar valores fixos para evitar problemas com config()
        $this->locale = $_SESSION['locale'] ?? 'pt_BR';
        $this->fallbackLocale = 'en';
        
        $this->translator = new SymfonyTranslator($this->locale);
        $this->translator->setFallbackLocales([$this->fallbackLocale]);
        $this->translator->addLoader('array', new ArrayLoader());
        
        $this->loadTranslations();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
        $this->translator->setLocale($locale);
        $_SESSION['locale'] = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function trans(string $key, array $parameters = [], string $domain = 'messages'): string
    {
        return $this->translator->trans($key, $parameters, $domain);
    }

    private function loadTranslations(): void
    {
        $locales = [$this->locale, $this->fallbackLocale];
        
        foreach ($locales as $locale) {
            $path = BASE_PATH . "/resources/lang/{$locale}";
            
            if (!is_dir($path)) {
                continue;
            }
            
            $files = glob("{$path}/*.php");
            
            foreach ($files as $file) {
                $domain = pathinfo($file, PATHINFO_FILENAME);
                $translations = require $file;
                
                $this->translator->addResource('array', $translations, $locale, $domain);
            }
        }
    }
}