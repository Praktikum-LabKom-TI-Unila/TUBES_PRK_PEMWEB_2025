<?php

/**
 * Response Helper
 */

class Response
{
    /**
     * Send JSON success response
     */
    public static function success($message = 'Success', $data = [], $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    /**
     * Send JSON error response
     */
    public static function error($message = 'Error', $errors = [], $statusCode = 400)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ]);
        exit;
    }

    /**
     * Send validation error response
     */
    public static function validationError($errors, $message = 'Validation Error')
    {
        self::error($message, $errors, 422);
    }

    /**
     * Send unauthorized response
     */
    public static function unauthorized($message = 'Unauthorized')
    {
        self::error($message, [], 401);
    }

    /**
     * Send forbidden response
     */
    public static function forbidden($message = 'Forbidden')
    {
        self::error($message, [], 403);
    }

    /**
     * Send not found response
     */
    public static function notFound($message = 'Not Found')
    {
        self::error($message, [], 404);
    }

    /**
     * Send created response
     */
    public static function created($message = 'Created', $data = [])
    {
        self::success($message, $data, 201);
    }
}
