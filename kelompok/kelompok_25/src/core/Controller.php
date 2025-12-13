<?php

/**
 * Base Controller
 */

class Controller
{
    /**
     * Render view
     */
    protected function view($viewPath, $data = [], $layout = 'main')
    {
        // Extract data variables
        extract($data);

        // Start output buffering
        ob_start();

        // Include view file
        $viewFile = ROOT_PATH . "/views/$viewPath.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            die("View tidak ditemukan: $viewPath");
        }

        // Get view content
        $content = ob_get_clean();

        // Include layout
        if ($layout) {
            $layoutFile = ROOT_PATH . "/views/layouts/$layout.php";
            if (file_exists($layoutFile)) {
                include $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to URL
     */
    protected function redirect($path)
    {
        redirect($path);
    }

    /**
     * Redirect back
     */
    protected function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        header("Location: $referer");
        exit;
    }

    /**
     * Validate request
     */
    protected function validate($data, $rules)
    {
        $validator = Validator::make($data, $rules);

        if (!$validator->validate()) {
            set_flash('error', 'Terdapat kesalahan pada form.');
            set_flash('errors', $validator->errors());
            set_old($data);
            $this->back();
        }

        return $validator->validated();
    }
}
