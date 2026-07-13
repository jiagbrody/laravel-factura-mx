<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Exceptions;

use RuntimeException;

/**
 * Base de todas las excepciones del paquete. Permite al app anfitrión
 * capturar cualquier fallo de facturación con un solo catch.
 */
class FacturaMxException extends RuntimeException {}
