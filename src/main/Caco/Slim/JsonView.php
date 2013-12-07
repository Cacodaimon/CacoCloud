<?php
namespace Caco\Slim;

/**
 * Class JsonView
 * @package Caco\Slim
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class JsonView extends \Slim\View
{
    public function render($status)
    {
        $app = \Slim\Slim::getInstance();
        $app->contentType('application/json');
        $app->expires(0);
        $app->response()->setStatus(intval($status));
        $response = ['status' => $status];

        $error = $this->data->get('error', false);
        switch ($status) {
            case 404:
                $error = $error ? $error : 'Resource not found';
                break;
            case 500:
                $error = $error ? $error : 'Server Error';
                break;
        }

        if ($error) {
            $response['error'] = $error;
        }

        $keys = $this->data->keys();
        unset($keys[array_search('flash', $keys)]);

        foreach ($keys as $key) {
            $response[$key] = $this->data->get($key);
        }

        echo json_encode($response, JSON_NUMERIC_CHECK);
    }
}