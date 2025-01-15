<?php

namespace Core\Exceptions;

use Exception;

/**
 * Excepción personalizada para errores relacionados con la base de datos.
 * 
 * Esta clase permite distinguir los errores de base de datos de otros tipos
 * de excepciones en la aplicación.
 */
class DatabaseException extends Exception {}