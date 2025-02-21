<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu page
add_action('admin_menu', function () {
    add_options_page(
        'Bricks Mailchimp Settings',
        'Bricks Mailchimp',
        'manage_options',
        'bricks-mailchimp-settings',
        'bmci_settings_page'
    );
});

// Register settings
add_action('admin_init', function () {
    register_setting('bricks_mailchimp_options', 'bmci_api_key');
    register_setting('bricks_mailchimp_options', 'bmci_list_id');
    register_setting('bricks_mailchimp_options', 'bmci_update_existing');
});

// Render settings page
function bmci_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Bricks Mailchimp Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('bricks_mailchimp_options');
    do_settings_sections('bricks_mailchimp_options');
    ?>
            <table class="form-table">
                <tr>
                    <th><label for="bmci_api_key">Mailchimp API Key</label></th>
                    <td><input type="password" name="bmci_api_key" id="bmci_api_key" value="<?php echo esc_attr(get_option('bmci_api_key')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="bmci_list_id">Mailchimp List ID</label></th>
                    <td><input type="text" name="bmci_list_id" id="bmci_list_id" value="<?php echo esc_attr(get_option('bmci_list_id')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="bmci_update_existing">Update Existing Subscribers?</label></th>
                    <td>
                        <input type="checkbox" name="bmci_update_existing" id="bmci_update_existing" value="1" <?php checked(1, get_option('bmci_update_existing'), true); ?>>
                        <label for="bmci_update_existing">Yes, update existing subscribers</label>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
