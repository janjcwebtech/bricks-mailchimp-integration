<?php

if (!defined('ABSPATH')) {
    exit;
}

function bmci_custom_mailchimp_action($form)
{
    // Get form fields
    $fields = $form->get_fields();

    // Extract email, name, and consent fields
    $email   = isset($fields['Email']) ? sanitize_email($fields['Email']) : '';
    $name    = isset($fields['Name']) ? sanitize_text_field($fields['Name']) : '';
    //$consent = (isset($fields['Consent']) && is_array($fields['Consent']) && !empty($fields['Consent'][0])) ? true : false;

    // Ensure required fields exist
    if (!$email) { //|| !$consent) {
        error_log('Mailchimp: Email is missing.');
        $form->set_result([
            'action'  => 'mailchimp_subscribe',
            'type'    => 'danger',
            'message' => esc_html__('Missing required fields.', 'bricks'),
        ]);
        return;
    }

    // Get settings from the plugin's options page
    $api_key = get_option('bmci_api_key');
    $list_id = get_option('bmci_list_id');
    $update_existing = get_option('bmci_update_existing', false);

    // Validate API settings
    if (!$api_key || !$list_id) {
        error_log('Mailchimp API Key or List ID is missing in settings.');
        $form->set_result([
            'action'  => 'mailchimp_subscribe',
            'type'    => 'danger',
            'message' => esc_html__('Mailchimp settings missing.', 'bricks'),
        ]);
        return;
    }

    // Extract datacenter from API key
    $dc = explode('-', $api_key)[1] ?? '';
    if (!$dc) {
        error_log('Invalid Mailchimp API key format.');
        return;
    }

    // Convert email to lowercase and hash with MD5 (needed for updating)
    $subscriber_hash = md5(strtolower($email));

    // Mailchimp API endpoint (for adding or updating subscribers)
    $url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$list_id}/members/{$subscriber_hash}";

    // Prepare subscriber data
    $data = [
        'email_address' => $email,
        'status_if_new' => 'subscribed',  // Ensures new users are subscribed
        'merge_fields'  => [
            'FNAME' => $name
        ]
    ];

    // Choose method based on settings (PUT updates, POST adds new)
    $method = $update_existing ? 'PUT' : 'POST';

    // Send request to Mailchimp
    $response = wp_remote_request($url, [
        'body'    => json_encode($data),
        'headers' => [
            'Authorization' => 'apikey ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'method'  => $method,
    ]);

    // Check response
    if (is_wp_error($response)) {
        error_log('Mailchimp API Error: ' . $response->get_error_message());
        $form->set_result([
            'action'  => 'mailchimp_subscribe',
            'type'    => 'danger',
            'message' => esc_html__('Mailchimp API Error.', 'bricks'),
        ]);
        return;
    }

    // Get response details
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $response_data = json_decode($response_body, true);

    // Log error responses only
    if ($response_code != 200 && $response_code != 201) {
        error_log('Mailchimp API Error Response: ' . print_r($response_data, true));
    }

    // Handle response codes
    if ($response_code == 200) {
        $form->set_result([
            'action'  => 'mailchimp_subscribe',
            'type'    => 'success',
            'message' => esc_html__('Subscriber updated successfully!', 'bricks'),
        ]);
    } elseif ($response_code == 201) {
        $form->set_result([
            'action'  => 'mailchimp_subscribe',
            'type'    => 'success',
            'message' => esc_html__('Subscriber added successfully!', 'bricks'),
        ]);
    } else {
        $form->set_result([
            'action'  => 'mailchimp_subscribe',
            'type'    => 'danger',
            'message' => esc_html__('Mailchimp error: ' . ($response_data['detail'] ?? 'Unknown error'), 'bricks'),
        ]);
    }
}
