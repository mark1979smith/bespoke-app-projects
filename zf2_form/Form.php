<?php
/**
 * BespokeApps / ZF2 Form
 * The class aims to simplify building form fields with inbuilt validation.
 * It has been created to work out of the box, or should you wish, you can
 * modify in any way to better suit your needs.
 *
 * @requires PHP 5.5 & Zend Framework 2
 *
 * @author Mark Smith
 * @link www.bespoke-apps.co.uk
 */
namespace BespokeApps;

use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\AggregateResolver;
use Zend\View\Resolver\TemplatePathStack;


class Form
{
    const VALIDATE_NOT_EMPTY = 'NotEmpty';

    const VALIDATE_NOT_VALID = 'NotValid';

    protected $formFields = [];

    protected $formNamespace = NULL;

    /**
     * Construct the form
     * Assigns the form namespace and fields before processing
     * @param string $form_namespace
     * @param array $form_fields
     */
    public function __construct($form_namespace, $form_fields)
    {
        $this->formNamespace = $form_namespace;
        $this->formFields = $form_fields;
    }

    /**
     * Validate
     * Pass in the posted data to validate any requirements
     * @param $posted_data
     * @return array
     */
    public function validate($posted_data)
    {
        // Ensure posted data is an array
        $posted_data = new Parameters($posted_data);

        // Define error array
        $errors = [];

        // Iterate over data
        // This will ONLY check the data that has been passed, ie. it will not throw an error on a NotEmpty
        // field if you do not pass the field into the $posted_data array. It is done this way to allow you
        // to validate only the data you wish. For example, if you only want to validate an email address, you
        // can simply by passing that one field data into the validate method.
        foreach($posted_data as $field_key => $value) {
            // Ensure field should be checked
            if (array_key_exists($field_key, $this->formFields)) {
                // Ensure Field has validation options
                if (
                    array_key_exists('validate', $this->formFields[$field_key]) &&
                    is_array($this->formFields[$field_key]['validate']) &&
                    !empty($this->formFields[$field_key]['validate'])
                ) {
                    foreach($this->formFields[$field_key]['validate'] as $validation_key => $validation_message) {
                        switch($validation_key) {
                            case self::VALIDATE_NOT_EMPTY :
                                if (strlen(trim($value)) == 0) {
                                    $errors[$field_key] = $validation_message;
                                    continue;
                                }
                                break;
                            case self::VALIDATE_NOT_VALID :
                                $validator = new Zend_Validate_EmailAddress();
                                if (!$validator->isValid($value)) {
                                    // email appears to not be valid
                                    $errors[$field_key] = $validation_message;
                                    continue;
                                }
                                break;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Get Fields
     * Getter method to get all fields attached to Form
     * @return array
     */
    public function getFields()
    {
        return $this->formFields;
    }

    public function getNamespace()
    {
        return $this->formNamespace;
    }

    /**
     * Output Form
     * This is where all the form fields are generated with their labels
     * and returned as one string
     * @return string
     * @throws \Exception
     */
    public function outputForm()
    {
        if (empty($this->getFields())) {
            throw new \InvalidArgumentException('No fields exist in form');
        }

        $output = [];

        $resolver = new AggregateResolver();
        $stack = new TemplatePathStack([
            'script_paths' => [
                __DIR__
            ]
        ]);
        $resolver->attach($stack);

        $renderer = new PhpRenderer();
        $renderer->setResolver($resolver);

        /**
         * Iterate over all fields
         */
        foreach($this->getFields() as $form_key => $form_field) {

            /**
             * Generate the label
             */
            $view_label = new ViewModel([
                'form_namespace'    => $this->getNamespace(),
                'form_key'          => $form_key,
                'form_field'        => $form_field,
                'is_required'       => self::isFormFieldRequired($form_field)
            ]);
            $view_label->setTemplate('Label/template');

            /**
             * Generate the element
             */
            $view_element = new ViewModel([
                'form_field' => $form_field,
                'form_field_name' => self::generateFieldName($this->getNamespace(), $form_key),
                'form_field_id' => self::generateFieldId($this->getNamespace(), $form_key),
                'form_field_value' => self::get($this->getNamespace(), $form_key)
            ]);
            $view_element->setTemplate('Element/template');


            /**
             * Generate the container containing both the label and element
             */
            $view_field_open = new ViewModel([
                'renderer' => $renderer
            ]);
            $view_field_open->setTemplate('Field/template');
            $view_field_open->addChild($view_label)
                ->addChild($view_element);

            // Build up list of fields
            $output[] = $renderer->render($view_field_open);
        }

        // Return the list of fields as a string
        return implode(PHP_EOL, $output);
    }

    /**
     * Is form field required
     * @param string $form_field
     * @return bool
     */
    protected static function isFormFieldRequired($form_field)
    {
        return (array_key_exists('rqd', $form_field) && $form_field['rqd'] === true);
    }

    /**
     * Generate Field Name
     * Used when outputting the form to generate the field name attribute
     * @param string $form_namespace
     * @param string $form_field_key
     * @return string
     */
    protected static function generateFieldName($form_namespace, $form_field_key)
    {
        return $form_namespace .'[' .$form_field_key .']';
    }

    /**
     * Generate Field Id
     * Used when outputting the form to generate the field id attribute
     * @param string $form_namespace
     * @param string $form_field_key
     * @return string
     */
    protected static function generateFieldId($form_namespace, $form_field_key)
    {
        return $form_namespace .'_'. $form_field_key;
    }

    /**
     * Get Field Value
     * Will return the posted data of the form field (if there is one) or an empty string
     * @param string $form_namespace
     * @param string $form_field_key
     * @return string
     */
    protected static function get($form_namespace, $form_field_key) {
        return (
            array_key_exists($form_namespace, $_POST) && // Does $_POST have $form_namespace key
            array_key_exists($form_field_key, $_POST[$form_namespace]) ? // Does $_POST[$form_namespace] have $form_field_key
                $_POST[$form_namespace][$form_field_key] : // Return posted value
                '' // Return empty string
        );
    }
}