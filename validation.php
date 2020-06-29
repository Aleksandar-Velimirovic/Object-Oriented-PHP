<?php

interface ValidationInterface{

    public function __construct(string $value, string $name);

    public function validate();
}

class Email implements ValidationInterface{

    protected $value;
    protected $name;

    public function __construct(string $value, string $name){
        $this->value = $value;
        $this->name = $name;
    }

    public function validate(){
        if(!filter_var($this->value, FILTER_VALIDATE_EMAIL)){
            return "$this->name has to be an email!";
        }

        return '';
    }
}

class Numeric implements ValidationInterface{

    protected $value;
    protected $name;

    public function __construct(string $value, string $name){
        $this->value = $value;
        $this->name = $name;
    }

    public function validate(){
        if(!is_numeric($this->value)){
            return "$this->name has to be a valid number!";
        }

        return '';
    }
}

class Required implements ValidationInterface{

    protected $value;
    protected $name;

    public function __construct(string $value, string $name){
        $this->value = $value;
        $this->name = $name;
    }

    public function validate(){
        if(!strlen($this->value)){
            return "$this->name field is required!";
        }

        return '';
    }
}

class ValidationStrategy{
    
    protected $validation;

    public function __construct(ValidationInterface $validation){
        $this->validation = $validation;
    }

    public function validate(){
        return $this->validation->validate();
    }

}

function strategy($request){

    Validator::validate($request);
}

class Validator{

    public static function validate($request){

        $errors = [];

        foreach($request as $field){
            $rules = explode('|', $field['rules']);

            foreach($rules as $rule){
               
                $class = ucwords($rule);

                $error = (new ValidationStrategy(new $class($field['value'], $field['name'])))->validate();

                if($error){

                    $errors[$field['name']]['errors'][] = $error;
                }
            }
        }

        echo '<pre>';
        print_r($errors);
        echo '</pre>'; 
    }
}

$request = [
        
    [
        'name' => 'email',
        'value' => 'notValid',
        'rules' => 'email|required'
    ],
    [
        'name' => 'price',
        'value' => 123,
        'rules' => 'numeric|required'
    ],
    [
        'name' => 'quantity',
        'value' => '',
        'rules' => 'numeric|required'
    ],
];

strategy($request);
