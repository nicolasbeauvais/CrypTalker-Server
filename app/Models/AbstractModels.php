<?php

namespace Models;

use Exception;
use ReflectionMethod;
use stdClass;

abstract class AbstractModels
{
    /**
     * Contain all errors for the MessageBag.
     * @var array
     */
    private $errors = array();

    /**
     * Contain all data to return.
     * @var array
     */
    private $data = array();

    /**
     * Add a error message.
     *
     * @param $message
     * @param $key
     */
    protected function error($key = null, $message)
    {
        if ($key) {
            $this->errors[$key] = array('message' => $message);
        } else {
            $this->errors[] = array('message' => $message);
        }
    }

    /**
     * Add data.
     *
     * @param string|array $key data key
     * @param string|array $value data value
     */
    protected function data($key, $value = null)
    {
        if (is_array($key) && $value == null) {

            foreach ($key as $k => $v) {
                $this->data[$k] = $v;
            }
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * Transform a Laravel validation object into messages to feed the page answer.
     *
     * @param stdClass $validation
     */
    protected function parseValidation(stdClass $validation)
    {
        $errors = $validation->errors->getMessages();

        foreach ($errors as $key => $error) {
            $this->error($key, $error);
        }
    }

    protected function success()
    {
        $this->errors['success'] = true;
    }

    /**
     * Generic answer to return to the view.
     *
     * @return array
     */
    protected function response()
    {
        // handle errors
        $errors = array(
            'messages' => $this->errors,
            'errno' => count($this->errors) ? 400 : 200
        );

        $response = array(
            'data' => $this->data,
            'success' => $errors['errno'] === 200 ? true : false,
            'errors' => $errors
        );

        return $response;
    }

    /**
     * Call the specified validator and return the validation object.
     *
     * @param string $validator
     * @param array $fields
     * @param array $data
     *
     * @return stdClass
     *
     * @throws Exception
     */
    protected function validate($validator, array $fields, array $data = array())
    {
        $callerValidator = get_called_class() . 'Validator';

        if (!class_exists($callerValidator)) {
            throw new Exception('Cant find requested class ' . $callerValidator);
        }

        return with(new $callerValidator)->validate($fields, $validator, $data);
    }

    /**
     * Check if all required parameter are set, if not, return an error with the missing ones.
     *
     * @param string $method The caller method
     */
    protected function required($method)
    {
        $args = func_get_args();
        array_shift($args);// remove the function name

        $missing = array();

        foreach ($args as $key => $arg) {
            if ($arg === null) {
                $missing[$key] = true;
            }
        }

        // if missing parameters
        if (!empty($missing)) {

            $class = get_called_class();

            $ReflectionMethod = new ReflectionMethod($class, $method);
            $methodParams = $ReflectionMethod->getParameters();

            foreach ($missing as $key => $param) {
                $this->error($methodParams[$key]->getName(), 'Missing parameter ' . $methodParams[$key]->getName());
            }

            // faire dans base controlleur une fonction qui gere la vue de ce retour.
            \Controllers\Api\AbstractApiController::answerJson($this->response());

        }
    }

    protected function randomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
} 
