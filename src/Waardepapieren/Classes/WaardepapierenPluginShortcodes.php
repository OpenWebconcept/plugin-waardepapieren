<?php

namespace OWC\Waardepapieren\Classes;

use OWC\Waardepapieren\Foundation\Plugin;
use OWC\Waardepapieren\Classes\GFFieldWaardePapierType;

class WaardepapierenPluginShortcodes
{
    /** @var Plugin */
    protected $plugin;
    protected $typeService;

    public function __construct(Plugin $plugin, GFFieldWaardePapierType $typeService)
    {
        $this->plugin = $plugin;
        $this->typeService = $typeService;
        $this->add_shortcode();
        $this->load_hooks();
    }

    private function add_shortcode(): void
    {
        add_shortcode('waardepapieren-result', [$this, 'waardepapieren_result_shortcode']);
    }

    private function load_hooks(): void
    {
        add_action('gform_after_submission', function ($entry, $form) {
            unset($_SESSION['certificate']);

            $organization = get_option('waardepapieren_organization', '');

            foreach ($form['fields'] as $field) {
                switch ($field['type']) {
                    case 'waardepapier':
                        $this->type = rgar($entry, (string) $field->id);
                        break;
                    case 'person':
                        $this->bsn = rgar($entry, (string) $field->id);
                        break;
                }
            }

            $templates = $this->typeService->getTemplates();
            $this->isValid = false;
            foreach ($templates as $template) {
                if (isset($template['description']) && $template['description'] == $this->type) {
                    $this->isValid = true;
                }
            }


            if (empty($this->type) || empty($this->bsn) || !$this->isValid) {
                return;
            }

            $data = [
                "person"        => $this->bsn,
                "type"          => $this->type,
                "organization"  => isset($organization) ? $organization : 'Demodam'
            ];

            $this->waardepapieren_post($data);
        }, 10, 2);
    }

    /**
     * Handles post from this Gravity Form that uses the advanced fields Waardepapier Person and Waardepapier Type.
     *
     * @param array $data should contain an array with a person, type and organization value.
     *
     * @return void
     */
    public function waardepapieren_post(array $data): void
    {
        $key      = get_option('waardepapieren_api_key', '');
        $endpoint = get_option('waardepapieren_api_domain', '') . '/api/waar/certificates';

        // if (empty($key) || empty($endpoint)) {
        if (empty($endpoint)) {
            return;
        }

        // unset any existing session.
        unset($_SESSION['certificate']);

        $headers = ['Content-Type' => 'application/json'];
        isset($key) && !empty($key) && $headers['Authorization'] = $key;

        $data = wp_remote_post($endpoint, [
            'headers'     => $headers,
            'body'        => json_encode($data),
            'method'      => 'POST',
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

        $decodedBody = json_decode($responseBody, true);

        $_SESSION['certificate'] = $decodedBody;
        // die;
    }

    /**
     * Callback for shortcode [waardepapieren-result].
     *
     * @return string
     */
    public function waardepapieren_result_shortcode(): string
    {
        $downloadButtons = [];

        if (!empty($_SESSION['certificate']['document'])) {
            $downloadButtons[] = $this->createButton($_SESSION['certificate']['document'], 'document');
        }

        if (!empty($_SESSION['certificate']['image'])) {
            $downloadButtons[] = $this->createButton($_SESSION['certificate']['image'], 'image');
        }

        if (!empty($_SESSION['certificate']['claim'])) {
            $claim             = $_SESSION['certificate']['claim'];
            $claim             = base64_encode(json_encode($claim));
            $downloadButtons[] = $this->createButton($claim, 'claim', true);
        }

        $type = $_SESSION['certificate']['type'] ?? 'Type onbekend';

        return $this->shortcodeResult($type, $downloadButtons);
    }

    /**
     * Create html for the download button.
     *
     * @param string $value
     * @param string $type
     * @param boolean $isClaim
     *
     * @return string
     */
    private function createButton(string $value, string $type, bool $isClaim = false): string
    {
        if ($isClaim) {
            return '<button><a href="data:application/json;base64,' . $value . '" download>download ' . $type . '</a></button>';
        }

        return '<button style="margin-right: 15px"><a href="' . $value . '" download>download ' . $type . '</a></button>';
    }

    /**
     * Create html for the result of the shortcode.
     *
     * @param string $type
     * @param array $downloadButtons
     *
     * @return string
     */
    private function shortcodeResult(string $type, array $downloadButtons): string
    {
        if (empty($downloadButtons)) {
            return '<div style="text-align: center">' . esc_html__('Er ging iets fout met het ophalen van uw gegevens.', 'waardepapierenaddon') . '</div>';
        }
        return '<div style="text-align: center"> <h3>' . ucfirst(str_replace('_', ' ', $type)) . '</h3>' . implode(" ", $downloadButtons) . '</div>';
    }
}
