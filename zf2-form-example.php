<?php
/**
 * Bespoke Apps / PHP-ZF2 Form
 * This example should give you an insight into the options that are available
 * User: Mark Smith
 */
use Zend\Loader\StandardAutoloader;

/**
 * CONFIGURATION
 */
// Set this only if the path to the Zend Framework 2 library is not on your include path
$zend_library_location = 'C:\ZendServer\share\ZendFramework2\library';

/**
 * END CONFIGURATION
 */

if ($zend_library_location) {
    set_include_path($zend_library_location);
}

/**
 * AUTO LOADER
 */
require_once 'Zend/Loader/StandardAutoloader.php';
$loader = new StandardAutoloader();
$loader->registerNamespace('Zend', $zend_library_location . DIRECTORY_SEPARATOR . 'Zend');
$loader->registerNamespace('BespokeApps', __DIR__ . DIRECTORY_SEPARATOR . 'zf2_form');
$loader->register();

$form_namespace = 'enquiry';

$form_fields = [
    'name' => [
        'rqd' => true,
        'label' => 'Name',
        'validate' => [
            'NotEmpty' => 'Please enter your name',
        ]
    ],
    'email' => [
        'rqd' => true,
        'label' => 'Email',
        'type' => 'email',
        'validate' => [
            'NotEmpty' => 'Please enter your email address',
            'NotValid' => 'Please enter a valid email address',
        ]
    ],
    'contact_number' => [
        'rqd' => true,
        'label' => 'Contact Number',
        'validate' => [
            'NotEmpty' => 'Please enter your telephone number',
        ]
    ],
    'company_name' => [
        'label' => 'Company Name'
    ],
    'industry' => [
        'label' => 'Industry'
    ],
    'project_detail' => [
        'rqd' => true,
        'label' => 'Tell us a bit about your project',
        'type' => 'textarea',
        'validate' => [
            'NotEmpty' => 'Please tell us about your project',
        ]
    ],
    'own_domain_and_hosting' => [
        'label' => 'Do you have your own domain name and hosting?',
        'options' => [
            'I have both my domain and hosting',
            'I just have my domain',
            'I just have my hosting',
            'I don\'t have either'
        ]
    ],
    'how_many_pages' => [
        'label' => 'How many pages do you envisage your website being?',
        'options' => [
            'Below 5',
            '5-10',
            '10-20',
            'Above 20',
        ]
    ],
    'online_payments' => [
        'label' => 'Is your website selling items online or take payments online?',
        'options' => [
            'Yes',
            'No',
        ]
    ],
    'information_website_only' => [
        'rqd' => true,
        'label' => 'Is the website purely to provide information to visitors about your business?',
        'type' => 'textarea',
        'validate' => [
            'NotEmpty' => 'Please fill in this field',
        ]
    ],
    'logo_provided' => [
        'label' => 'Do you already have your logo?',
        'options' => [
            'Yes',
            'No',
        ]
    ],
    'require_cms' => [
        'label' => 'Do you require a content management system to enable you to update the website yourself?',
        'options' => [
            'Yes',
            'No',
        ]
    ],
    'specific_requirements' => [
        'rqd' => true,
        'label' => 'Are there any specific requirements such as the need for galleries, a certain look/feel, illustrations, search, animation, newsletter signups etc?',
        'type' => 'textarea',
        'validate' => [
            'NotEmpty' => 'Please fill in this field'
        ],
    ],
    'other_websites' => [
        'label' => 'Are their any websites that you have visited recently that you feel would be an appropriate design/feel for your business?',
        'type' => 'textarea',
    ],
    'budget' => [
        'label' => 'What is you estimated budget for your project?',
        'options' => [
            'Below £1000',
            '£1000-2500',
            '£2500-5000',
            'Above £5000'
        ]
    ]

];

header('Content-Type:text/html; charset=UTF-8');

$form = new \BespokeApps\Form($form_namespace, $form_fields);
    echo $form->outputForm();