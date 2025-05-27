<?php

namespace Core\Cache;

class CacheManager
{
    private static $instance = null;
    private $driver = 'file'; // Padrão: arquivo
    private $redis = null;
    
    private function __construct()
    {
        // Verificar se Redis está disponível
        if (extension_loaded('redis')) {
            try {
                $this->redis = new \Redis();
                $host = getenv('REDIS_HOST') ?: '127.0.0.1';
                $port = getenv('REDIS_PORT') ?: 6379;
                if ($this->redis->connect($host, $port)) {
                    $this->driver = 'redis';
                }
            } catch (\Exception $e) {
                error_log('Redis não disponível: ' . $e->getMessage());
            }
        }
        // Verificar se APCu está disponível como fallback
        elseif (extension_loaded('apcu') && apcu_enabled()) {
            $this->driver = 'apcu';
        }
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function get(string $key, $default = null)
    {
        switch ($this->driver) {
            case 'redis':
                $value = $this->redis->get($key);
                return $value !== false ? json_decode($value, true) : $default;
            case 'apcu':
                $success = false;
                $value = apcu_fetch($key, $success);
                return $success ? $value : $default;
            default:
                // Implementação baseada em arquivo
                $cacheFile = $this->getCacheFilePath($key);
                if (file_exists($cacheFile) && is_readable($cacheFile)) {
                    $content = file_get_contents($cacheFile);
                    $data = json_decode($content, true);
                    if ($data && isset($data['expiry']) && $data['expiry'] > time()) {
                        return $data['value'];
                    }
                }
                return $default;
        }
    }
    
    public function set(string $key, $value, int $ttl = 3600): bool
    {
        switch ($this->driver) {
            case 'redis':
                return $this->redis->setex($key, $ttl, json_encode($value));
            case 'apcu':
                return apcu_store($key, $value, $ttl);
            default:
                // Implementação baseada em arquivo
                $cacheFile = $this->getCacheFilePath($key);
                $cacheDir = dirname($cacheFile);
                if (!is_dir($cacheDir)) {
                    mkdir($cacheDir, 0777, true);
                }
                $data = [
                    'value' => $value,
                    'expiry' => time() + $ttl
                ];
                return file_put_contents($cacheFile, json_encode($data)) !== false;
        }
    }
    
    public function delete(string $key): bool
    {
        switch ($this->driver) {
            case 'redis':
                return $this->redis->del($key) > 0;
            case 'apcu':
                return apcu_delete($key);
            default:
                $cacheFile = $this->getCacheFilePath($key);
                if (file_exists($cacheFile)) {
                    return unlink($cacheFile);
                }
                return false;
        }
    }
    
    public function increment(string $key, int $step = 1): int
    {
        switch ($this->driver) {
            case 'redis':
                return $this->redis->incrBy($key, $step);
            case 'apcu':
                return apcu_inc($key, $step) ?: $step;
            default:
                $value = (int) $this->get($key, 0);
                $newValue = $value + $step;
                $this->set($key, $newValue);
                return $newValue;
        }
    }
    
    private function getCacheFilePath(string $key): string
    {
        $safeKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
        return BASE_PATH . '/storage/cache/' . $safeKey . '.cache';
    }
    
    public function getDriver(): string
    {
        return $this->driver;
    }
}