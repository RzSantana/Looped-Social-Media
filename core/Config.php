<?php


namespace Core;

use RuntimeException;

/**
 * Clase Config
 * 
 * Gestiona la carga y acceso a variables de entorno desde el archivo .env.
 * Esta clase implementa un sistema de carga perezosa (lazy loading) y
 * caché en memoria para optimizar el rendimiento.
 */
class Config
{
    /**
     * Almacena las variables de entorno en memoria una vez cargadas.
     * @var array<string,mixed>
     */
    private static array $varibles = [];

    /**
     * Indica si el archivo .env ya ha sido cargado.
     * @var bool
     */
    private static bool $loaded = false;

    /**
     * Obtiene el valor de una variable de entorno.
     * 
     * Si es la primera vez que se accede a una variable, carga el archivo .env.
     * Si la variable no existe, devuelve el valor por defecto proporcionado.
     * 
     * @param string $key Nombre de la variable de entorno a obtener
     * @param mixed $default Valor por defecto si la variable no existe
     * @return mixed El valor de la variable o el valor por defecto
     * @throws \RuntimeException Si el archivo .env no existe
     */
    public static function get(string $key, mixed $default): mixed
    {
        if (!self::$loaded) {
            self::loadEnvFile();
        }

        return self::$varibles[$key] ?? $default;
    }

    /**
     * Carga y procesa el archivo .env.
     * 
     * Este método:
     * 1. Lee el archivo .env línea por línea
     * 2. Ignora líneas vacías y comentarios
     * 3. Procesa cada línea para extraer la clave y el valor
     * 4. Limpia y convierte los valores según sea necesario
     * 
     * @throws \RuntimeException Si no se encuentra el archivo .env
     */
    public static function loadEnvFile(): void
    {
        $envFile = $_SERVER['DOCUMENT_ROOT'] . '/.env';

        if (!file_exists($envFile)) {
            throw new RuntimeException("El archivo '.env' no existe.");
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignoramos líneas que comienzan con #
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Procesamos solo líneas que contienen un =
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);

                $key = trim($key);
                $value = self::processEnvironmentValue($value);

                self::$varibles[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Procesa y limpia un valor del archivo .env.
     * 
     * Este método:
     * 1. Elimina espacios en blanco al inicio y final
     * 2. Elimina comillas simples o dobles si están presentes
     * 3. Convierte valores especiales ('true', 'false', 'null') a sus tipos PHP correspondientes
     * 
     * Ejemplos de procesamiento:
     * - 'true'    -> true (boolean)
     * - "false"   -> false (boolean)
     * - 'null'    -> null
     * - "texto"   -> texto (string)
     * - 'texto'   -> texto (string)
     * 
     * @param string $value El valor sin procesar del archivo .env
     * @return mixed El valor procesado y convertido al tipo apropiado
     */
    private static function processEnvironmentValue(string $value): mixed
    {
        // Eliminamos espacios al inicio y final
        $value = trim($value);

        // Eliminamos comillas simples, dobles y barras invertidas si existen
        $value = trim($value, '"\'\\');

        // Convertimos valores especiales a sus tipos PHP correspondientes
        return match (strtolower($value)) {
            'true' => true,
            'false' => false,
            'null' => null,
            default => $value
        };
    }
}
