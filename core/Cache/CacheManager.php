<?php

namespace Core\Cache;

/**
 * Sistema de Cache Avançado para FramePhp
 * Baseado no sistema de cache do SpeedPHP
 */
class CacheManager
{
    private static $instance = null;
    private string $cacheDir;
    private int $defaultTtl = 3600; // 1 hora por padrão
    private bool $enabled = true;

    private function __construct()
    {
        $this->cacheDir = BASE_PATH . '/storage/cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Habilita ou desabilita o cache
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Verifica se o cache está habilitado
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Define o TTL padrão
     */
    public function setDefaultTtl(int $ttl): void
    {
        $this->defaultTtl = $ttl;
    }

    /**
     * Armazena um valor no cache
     */
    public function set(string $key, $value, int $ttl = null): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $ttl = $ttl ?? $this->defaultTtl;
        $filename = $this->getCacheFilename($key);
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
            'created' => time()
        ];

        $cacheDir = dirname($filename);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        return file_put_contents($filename, serialize($data)) !== false;
    }

    /**
     * Recupera um valor do cache
     */
    public function get(string $key, $default = null)
    {
        if (!$this->enabled) {
            return $default;
        }

        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return $default;
        }

        $data = unserialize(file_get_contents($filename));
        
        if (!$data || !isset($data['expires']) || !isset($data['value'])) {
            $this->delete($key);
            return $default;
        }

        if (time() > $data['expires']) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    /**
     * Verifica se uma chave existe no cache
     */
    public function has(string $key): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return false;
        }

        $data = unserialize(file_get_contents($filename));
        
        if (!$data || !isset($data['expires'])) {
            $this->delete($key);
            return false;
        }

        if (time() > $data['expires']) {
            $this->delete($key);
            return false;
        }

        return true;
    }

    /**
     * Remove uma chave do cache
     */
    public function delete(string $key): bool
    {
        $filename = $this->getCacheFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }

    /**
     * Remove múltiplas chaves do cache
     */
    public function deleteMultiple(array $keys): bool
    {
        $success = true;
        
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Limpa todo o cache
     */
    public function clear(): bool
    {
        return $this->clearDirectory($this->cacheDir);
    }

    /**
     * Limpa cache por padrão de chave
     */
    public function clearPattern(string $pattern): bool
    {
        $files = glob($this->cacheDir . '/' . $pattern);
        $success = true;
        
        foreach ($files as $file) {
            if (is_file($file) && !unlink($file)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Obtém informações sobre o cache
     */
    public function getInfo(): array
    {
        $totalFiles = 0;
        $totalSize = 0;
        $expiredFiles = 0;
        
        $this->scanCacheDirectory($this->cacheDir, $totalFiles, $totalSize, $expiredFiles);
        
        return [
            'total_files' => $totalFiles,
            'total_size' => $this->formatBytes($totalSize),
            'expired_files' => $expiredFiles,
            'cache_dir' => $this->cacheDir,
            'enabled' => $this->enabled,
            'default_ttl' => $this->defaultTtl
        ];
    }

    /**
     * Gera estatísticas do cache
     */
    public function getStats(): array
    {
        $stats = $this->getInfo();
        $stats['hit_rate'] = $this->calculateHitRate();
        $stats['memory_usage'] = memory_get_usage(true);
        $stats['peak_memory'] = memory_get_peak_usage(true);
        
        return $stats;
    }

    /**
     * Cache com callback (método remember)
     */
    public function remember(string $key, callable $callback, int $ttl = null)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }

    /**
     * Cache com callback assíncrono
     */
    public function rememberAsync(string $key, callable $callback, int $ttl = null)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        // Em uma implementação real, você poderia usar filas ou workers
        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }

    /**
     * Incrementa um valor numérico
     */
    public function increment(string $key, int $value = 1, int $ttl = null): int
    {
        $current = $this->get($key, 0);
        $newValue = $current + $value;
        $this->set($key, $newValue, $ttl);
        
        return $newValue;
    }

    /**
     * Decrementa um valor numérico
     */
    public function decrement(string $key, int $value = 1, int $ttl = null): int
    {
        $current = $this->get($key, 0);
        $newValue = $current - $value;
        $this->set($key, $newValue, $ttl);
        
        return $newValue;
    }

    /**
     * Gera o nome do arquivo de cache
     */
    private function getCacheFilename(string $key): string
    {
        $hash = md5($key);
        $subDir = substr($hash, 0, 2);
        return $this->cacheDir . '/' . $subDir . '/' . $hash . '.cache';
    }

    /**
     * Limpa um diretório recursivamente
     */
    private function clearDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return true;
        }

        $files = scandir($dir);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $path = $dir . '/' . $file;
            
            if (is_dir($path)) {
                $this->clearDirectory($path);
                rmdir($path);
            } else {
                unlink($path);
            }
        }
        
        return true;
    }

    /**
     * Escaneia o diretório de cache
     */
    private function scanCacheDirectory(string $dir, int &$totalFiles, int &$totalSize, int &$expiredFiles): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $path = $dir . '/' . $file;
            
            if (is_dir($path)) {
                $this->scanCacheDirectory($path, $totalFiles, $totalSize, $expiredFiles);
            } else {
                $totalFiles++;
                $totalSize += filesize($path);
                
                $data = unserialize(file_get_contents($path));
                if (isset($data['expires']) && time() > $data['expires']) {
                    $expiredFiles++;
                }
            }
        }
    }

    /**
     * Calcula a taxa de acerto do cache
     */
    private function calculateHitRate(): float
    {
        // Implementação básica - em produção você pode usar Redis ou Memcached
        return 0.0;
    }

    /**
     * Formata bytes para leitura humana
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}