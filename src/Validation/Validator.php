<?php

namespace App\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Http\Request;

class Validator
{
    /**
     * @var array errors
     */
    protected $errors;
    /**
     * @param Request $request there are params to validate
     * @param array $rules rules according to which params will be valited
     * @return object returns object of this class to easily check if validation psases
     */
    public function validate(Request $request, $rules)
    {
        foreach ($rules as $field => $rule) {
            try {
                $rule->setName(ucfirst($field))->assert($request->getParam($field));
            } catch (NestedValidationException $e) {
                $this->errors[$field] = $e->getMessages();
            }
        }
        $_SESSION['errors'] = $this->errors;
        return $this;
    }
    /**
     * @return boolean true if validation passes
     */
    public function passed()
    {
        return empty($this->errors);
    }
    /**
     * @return boolean false if validation failed
     */
    public function failed()
    {
        return !empty($this->errors);
    }
    /**
     * @return array errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
