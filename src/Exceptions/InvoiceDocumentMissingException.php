<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Exceptions;

/**
 * El documento (XML/PDF) que la operación necesita no está registrado o el
 * archivo físico no existe en el disco configurado.
 */
final class InvoiceDocumentMissingException extends FacturaMxException {}
