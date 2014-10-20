<?php

namespace Models;

use Illuminate\Support\Facades\Validator;
use stdClass;

/**
 * Class AbstractValidator
 * @package Models\Repositories
 */
abstract class AbstractValidator
{
    /**
     * @var array list of the input to keep after validation
     */
    protected $inputs = array();

    /**
     * @var array the list of attributes to remove after validation
     */
    protected $blacklist = array();

    /**
     * Perform the validation using laravel Validator Facade.
     *
     * @param array $fields
     * @param array $rulesName
     * @param array $data
     *
     * @return stdClass validation results
     */
    public function validate(array $fields, $rulesName, $data = array())
    {
        $rules = $this->$rulesName;

        if ($data) {
            $rules = $this->makeRules($rules, $data);
        }

        return $this->result($fields, $rulesName, Validator::make($fields,$rules));
    }

    /**
     * Construct dynamic rules
     *
     * @param $rules
     * @param $data
     */
    public function makeRules($rules, $data)
    {
        foreach ($rules as $key => $rule) {
            foreach ($data as $dataKey => $param) {
                $rules[$key] = str_replace('{{' . $dataKey . '}}', $param, $rules[$key]);
            }
        }

        return $rules;
    }

    /**
     * Return an object of the validation result and chosen inputs data.
     *
     * @param array $fields
     * @param string $rulesName
     * @param $validator
     *
     * @return stdClass
     */
    private function result(array $fields, $rulesName, $validator)
    {

        if (!isset($this->inputs[$rulesName])) {
            // Remove unwanted field (start with _ or end with _confirmation)
            foreach ($fields as $key => $field) {
                if (strpos($key, '_') === 0 || strpos($key, '_confirmation') > -1 || in_array($key, $this->blacklist)) {
                    unset($fields[$key]);
                }
            }
        } else {
            $fields = array_only($fields, explode(',',$this->inputs[$rulesName]));
        }

        $response = new stdClass;
        $response->inputs = $fields;
        $response->validated = $validator->passes();
        $response->errors = $validator->messages();
        $response->validator = $validator;

        return $response;
    }
}
