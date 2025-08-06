<?php

namespace App\Services;

class CepService
{
    /**
     * Consulta CEP via API
     */
    public static function consultarCep($cep)
    {
        // Remove caracteres não numéricos
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        // Valida se tem 8 dígitos
        if (strlen($cep) != 8) {
            return [
                'success' => false,
                'message' => 'CEP inválido'
            ];
        }
        
        try {
            // Tenta primeiro via ViaCEP
            $resultado = self::consultarViaCep($cep);
            
            if (!$resultado['success']) {
                // Se falhar, tenta via API dos Correios
                $resultado = self::consultarCorreios($cep);
            }
            
            return $resultado;
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao consultar CEP: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Consulta via ViaCEP
     */
    private static function consultarViaCep($cep)
    {
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            return [
                'success' => false,
                'message' => 'Erro ao consultar CEP'
            ];
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['erro']) && $data['erro']) {
            return [
                'success' => false,
                'message' => 'CEP não encontrado'
            ];
        }
        
        return [
            'success' => true,
            'data' => [
                'cep' => $data['cep'],
                'logradouro' => $data['logradouro'],
                'complemento' => $data['complemento'],
                'bairro' => $data['bairro'],
                'cidade' => $data['localidade'],
                'estado' => $data['uf'],
                'ibge' => $data['ibge'],
                'ddd' => $data['ddd']
            ]
        ];
    }
    
    /**
     * Consulta via API dos Correios
     */
    private static function consultarCorreios($cep)
    {
        $url = "https://cep.correios.com.br/ws/{$cep}/json/";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            return [
                'success' => false,
                'message' => 'Erro ao consultar CEP'
            ];
        }
        
        $data = json_decode($response, true);
        
        if (empty($data) || isset($data['erro'])) {
            return [
                'success' => false,
                'message' => 'CEP não encontrado'
            ];
        }
        
        return [
            'success' => true,
            'data' => [
                'cep' => $data['cep'],
                'logradouro' => $data['logradouro'],
                'complemento' => $data['complemento'] ?? '',
                'bairro' => $data['bairro'],
                'cidade' => $data['localidade'],
                'estado' => $data['uf'],
                'ibge' => $data['ibge'] ?? '',
                'ddd' => $data['ddd'] ?? ''
            ]
        ];
    }
    
    /**
     * Formata CEP
     */
    public static function formatarCep($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }
    
    /**
     * Valida CEP
     */
    public static function validarCep($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        return strlen($cep) == 8;
    }
    
    /**
     * Obtém estado por CEP
     */
    public static function getEstadoPorCep($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        $prefixo = substr($cep, 0, 2);
        
        $estados = [
            '01' => 'AC', '02' => 'AL', '03' => 'AM', '04' => 'AP', '05' => 'BA',
            '06' => 'CE', '07' => 'DF', '08' => 'ES', '09' => 'GO', '10' => 'MA',
            '11' => 'MG', '12' => 'MS', '13' => 'MT', '14' => 'PA', '15' => 'PB',
            '16' => 'PE', '17' => 'PI', '18' => 'PR', '19' => 'RJ', '20' => 'RN',
            '21' => 'RO', '22' => 'RR', '23' => 'RS', '24' => 'SC', '25' => 'SE',
            '26' => 'SP', '27' => 'TO'
        ];
        
        return $estados[$prefixo] ?? null;
    }
    
    /**
     * Obtém DDD por CEP
     */
    public static function getDddPorCep($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        $prefixo = substr($cep, 0, 2);
        
        $ddds = [
            '01' => '68', '02' => '82', '03' => '92', '04' => '96', '05' => '71',
            '06' => '85', '07' => '61', '08' => '27', '09' => '62', '10' => '98',
            '11' => '31', '12' => '67', '13' => '65', '14' => '91', '15' => '83',
            '16' => '81', '17' => '86', '18' => '89', '19' => '21', '20' => '84',
            '21' => '69', '22' => '95', '23' => '51', '24' => '47', '25' => '79',
            '26' => '11', '27' => '63'
        ];
        
        return $ddds[$prefixo] ?? null;
    }
    
    /**
     * Obtém região por CEP
     */
    public static function getRegiaoPorCep($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        $prefixo = substr($cep, 0, 2);
        
        $regioes = [
            '01' => 'Norte', '02' => 'Nordeste', '03' => 'Norte', '04' => 'Norte',
            '05' => 'Nordeste', '06' => 'Nordeste', '07' => 'Centro-Oeste', '08' => 'Sudeste',
            '09' => 'Centro-Oeste', '10' => 'Nordeste', '11' => 'Sudeste', '12' => 'Centro-Oeste',
            '13' => 'Centro-Oeste', '14' => 'Norte', '15' => 'Nordeste', '16' => 'Nordeste',
            '17' => 'Nordeste', '18' => 'Sul', '19' => 'Sudeste', '20' => 'Nordeste',
            '21' => 'Norte', '22' => 'Norte', '23' => 'Sul', '24' => 'Sul', '25' => 'Nordeste',
            '26' => 'Sudeste', '27' => 'Norte'
        ];
        
        return $regioes[$prefixo] ?? null;
    }
    
    /**
     * Calcula frete por CEP
     */
    public static function calcularFrete($cepOrigem, $cepDestino, $peso, $comprimento = 16, $altura = 2, $largura = 11)
    {
        // Implementação básica de cálculo de frete
        // Em produção, usar API dos Correios ou outras transportadoras
        
        $cepOrigem = preg_replace('/[^0-9]/', '', $cepOrigem);
        $cepDestino = preg_replace('/[^0-9]/', '', $cepDestino);
        
        // Calcula distância aproximada baseada nos CEPs
        $distancia = self::calcularDistanciaCep($cepOrigem, $cepDestino);
        
        // Preços base por região
        $precos = [
            'Norte' => 25.00,
            'Nordeste' => 20.00,
            'Centro-Oeste' => 18.00,
            'Sudeste' => 15.00,
            'Sul' => 12.00
        ];
        
        $regiao = self::getRegiaoPorCep($cepDestino);
        $precoBase = $precos[$regiao] ?? 20.00;
        
        // Adiciona peso ao cálculo
        $precoPeso = $peso * 2.50;
        
        // Adiciona distância
        $precoDistancia = $distancia * 0.10;
        
        $frete = $precoBase + $precoPeso + $precoDistancia;
        
        return [
            'success' => true,
            'data' => [
                'valor' => round($frete, 2),
                'prazo' => self::calcularPrazo($regiao),
                'servico' => 'PAC',
                'distancia' => $distancia,
                'regiao' => $regiao
            ]
        ];
    }
    
    /**
     * Calcula distância aproximada entre CEPs
     */
    private static function calcularDistanciaCep($cepOrigem, $cepDestino)
    {
        // Implementação simplificada
        // Em produção, usar API de geocoding
        
        $origem = substr($cepOrigem, 0, 2);
        $destino = substr($cepDestino, 0, 2);
        
        // Distâncias aproximadas entre regiões
        $distancias = [
            '11' => ['11' => 0, '12' => 800, '13' => 1200, '14' => 2000, '15' => 1800],
            '12' => ['11' => 800, '12' => 0, '13' => 400, '14' => 1200, '15' => 1000],
            '13' => ['11' => 1200, '12' => 400, '13' => 0, '14' => 800, '15' => 600],
            '14' => ['11' => 2000, '12' => 1200, '13' => 800, '14' => 0, '15' => 200],
            '15' => ['11' => 1800, '12' => 1000, '13' => 600, '14' => 200, '15' => 0]
        ];
        
        return $distancias[$origem][$destino] ?? 1000;
    }
    
    /**
     * Calcula prazo de entrega
     */
    private static function calcularPrazo($regiao)
    {
        $prazos = [
            'Norte' => 10,
            'Nordeste' => 8,
            'Centro-Oeste' => 7,
            'Sudeste' => 5,
            'Sul' => 4
        ];
        
        return $prazos[$regiao] ?? 7;
    }
} 