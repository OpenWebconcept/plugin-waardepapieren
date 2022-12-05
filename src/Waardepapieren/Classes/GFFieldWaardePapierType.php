<?php

namespace OWC\Waardepapieren\Classes;

class GFFieldWaardePapierType extends \GF_Field_Select
{
    public $type = 'waardepapier';
    public $label;
    public $choices;
    public $isRequired;

    public function __construct($data = array())
    {
        parent::__construct($data);

        $this->label = 'Waardepapieren Type';


        $templates = $this->getTemplates();

        if ($templates) {
            $choices = [];
            foreach ($templates as $template) {
                $choices[] = [
                    'text'  => $template['name'],
                    'value' => $template['description']
                ];
            }

            $this->choices = $choices;
        } else {
            $this->choices = [['text' => 'Something wen\'t wrong fetching the certificate types']];
            $this->isDisabled = true;
        }
        $this->isRequired = true;
    }

    public function getTemplates()
    {
        $key      = get_option('waardepapieren_api_key', '');
        $endpoint = get_option('waardepapieren_api_domain', '') . '/api/template_groups?name=Waardepapieren certificaten';

        $headers = ['Content-Type' => 'application/json'];
        isset($key) && !empty($key) && $headers['Authorization'] = $key;


        $data = wp_remote_get($endpoint, [
            'headers'     => $headers,
            'method'      => 'GET',
            'data_format' => 'body',
            'timeout'     => 20
        ]);

        if (is_wp_error($data)) {
            return;
        }

        $responseBody = wp_remote_retrieve_body($data);

        if (is_wp_error($responseBody)) {
            return;
        }

        $decodedJson = json_decode($responseBody, true);

        if (isset($decodedJson['results'][0]['embedded']['templates'])) {
            return $decodedJson['results'][0]['embedded']['templates'];
        } else {
            return [];
        }
    }

    public function get_form_editor_field_title()
    {
        return esc_attr__('Waardepapier Type', 'gravityforms');
    }

    public function get_form_editor_button()
    {
        return array(
            'group' => 'advanced_fields',
            'text'  => $this->get_form_editor_field_title()
        );
    }

    public function get_field_label($bool, $value)
    {
        $this->label = 'Waardepapieren Type';
        return \GFCommon::get_label($this);
    }
}
