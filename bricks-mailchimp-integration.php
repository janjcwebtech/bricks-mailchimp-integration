<?php

/**
 * Plugin Name: Bricks Mailchimp Integration
 * Description: Adds a custom form action in Bricks Builder to send subscriber data to Mailchimp.
 * Version: 0.1.2
 * Author: Jan Cerny from JCwebTECH
 * Author URI: https://jcweb.tech/
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/mailchimp-handler.php';

//Register Bricks Form Action
/*
add_action('bricks/form/register', function ($form) {
    $form->register_action('mailchimp_subscribe', [
        'label'       => __('Mailchimp Subscribe', 'bricks-mailchimp'),
        'description' => __('Adds the form submission to Mailchimp', 'bricks-mailchimp'),
    ]);
});

// Handle form submission
add_action('bricks/form/submit/mailchimp_subscribe', 'bmci_handle_form_submission', 10, 2);
*/

//via custom action
add_action('bricks/form/custom_action', 'bmci_custom_mailchimp_action', 10, 1);
