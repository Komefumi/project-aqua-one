<?php

/*
  Plugin Name: Project Aqua One
  Description: A Plugin
  Version: 0.1
  Author: Vecktor [komefumi]
  Author URI: komefumi.github.io
*/

if (!defined('ABSPATH')) exit; // exit if accessed directly

class Vecktor_AquaOnePlugin
{
  private string $settings_slug = 'aquaoneplugin-our-word-filter';
  private string $settings_section_slug = 'aquaoneplugin-word-filter-options';
  private string $settings_section_id = 'aquaoneplugin__settings_section_id';
  private string $settings__replacement_text_section_slug = 'aquaoneplugin-settings-replacement-text-section';
  private string $settings__replacement__replacement_fields__group_id = 'aquaoneplugin__settings__replacement_fields__group_id';
  private string $words_to_filter_db_option = 'aquaoneplugin__words_to_filter';
  private string $replacement_text__db_option = 'aquaoneplugin__replacement_text';
  private string $action_name_for_save_filter_words = 'aquaoneplugin__action__save_filter_words';
  private string $nonce_name_for_save_filter_words = 'aquaoneplugin__nonce__save_filter_words';
  function __construct()
  {
    add_action('admin_menu', array($this, 'our_menu'));
    add_action('admin_init', array($this, 'our_settings'));
    if (get_option($this->words_to_filter_db_option)) add_filter('the_content', array($this, 'filter_logic'));
  }

  function our_settings()
  {
    add_settings_section($this->settings__replacement_text_section_slug, null, null, $this->settings_slug);
    register_setting($this->settings__replacement__replacement_fields__group_id, $this->replacement_text__db_option);
    add_settings_field('replacement-text', 'Filtered Text', array($this, 'replacement_fields_html'), $this->settings_slug, $this->settings_section_slug);
  }

  function replacement_fields_html()
  {
?>
    <input type="text" name="<?php echo $this->replacement_text__db_option ?>" value="<?php echo esc_attr(get_option($this->replacement_text__db_option, '****')); ?>">
    <p class="description">Leave blank to simply remove the filtered words.</p>
  <?php }

  function filter_logic($content)
  {
    $bad_words = explode(',', get_option($this->words_to_filter_db_option));
    $bad_words_trimmed = array_map('trim', $bad_words);
    return str_ireplace($bad_words_trimmed, '****', $content);
  }

  function our_menu()
  {
    $required_capability_as_permission = 'manage_options';
    $main_page_hook = add_menu_page(
      'Word to Filter',
      'Word Filter',
      $required_capability_as_permission,
      $this->settings_slug,
      array($this, 'word_filter_page'),
      plugin_dir_url(__FILE__) . 'custom.svg',
      100,
    );
    /*
    add_menu_page(
      'Word to Filter',
      'Word Filter',
      $required_capability_as_permission,
      $this->setting_slug,
      array($this, 'word_filter_page'),
      'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMCAyMEMxNS41MjI5IDIwIDIwIDE1LjUyMjkgMjAgMTBDMjAgNC40NzcxNCAxNS41MjI5IDAgMTAgMEM0LjQ3NzE0IDAgMCA0LjQ3NzE0IDAgMTBDMCAxNS41MjI5IDQuNDc3MTQgMjAgMTAgMjBaTTExLjk5IDcuNDQ2NjZMMTAuMDc4MSAxLjU2MjVMOC4xNjYyNiA3LjQ0NjY2SDEuOTc5MjhMNi45ODQ2NSAxMS4wODMzTDUuMDcyNzUgMTYuOTY3NEwxMC4wNzgxIDEzLjMzMDhMMTUuMDgzNSAxNi45Njc0TDEzLjE3MTYgMTEuMDgzM0wxOC4xNzcgNy40NDY2NkgxMS45OVoiIGZpbGw9IiNGRkRGOEQiLz4KPC9zdmc+Cg==',
      100,
    );
    */
    add_submenu_page(
      $this->settings_slug,
      'Words To Filter',
      'Words List',
      $required_capability_as_permission,
      $this->settings_section_slug,
      array($this, 'word_filter_page'),
    );
    add_submenu_page(
      $this->settings_slug,
      'Word Filter Options',
      'Options',
      $required_capability_as_permission,
      $this->settings_section_slug,
      array($this, 'options_sub_page'),
    );
    add_action("load-$main_page_hook", array($this, 'main_page_assets'));
  }

  function main_page_assets()
  {
    wp_enqueue_style('filter_admin_css', plugin_dir_url(__FILE__) . 'styles/filter_admin.css');
  }

  function word_filter_page()
  {
    $word_filter_option = $this->words_to_filter_db_option;
  ?>
    <div class="wrap">
      <h1>Word Filter</h1>
      <?php if (isset($_POST['just_submitted']) and $_POST['just_submitted'] == "true") $this->handle_form(); ?>
      <form method="POST">
        <input type="hidden" name="just_submitted" value="true" />
        <?php wp_nonce_field($this->action_name_for_save_filter_words, $this->nonce_name_for_save_filter_words); ?>
        <label for="plugin_words_to_filter">
          <p>Enter a <strong>comma-separated</strong> list of words to filter your site contents</p>
        </label>
        <div class="word-filter__flex-container">
          <textarea name="<?php echo $word_filter_option; ?>" id="<?php echo $word_filter_option; ?>" cols="30" rows="10" placeholder="bad, mean, awful, horrible"><?php echo esc_textarea(get_option($word_filter_option)); ?></textarea>
        </div>
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
      </form>
    </div>
    <?php }

  function handle_form()
  {
    if (wp_verify_nonce($_POST[$this->nonce_name_for_save_filter_words], $this->action_name_for_save_filter_words) and current_user_can('manage_options')) {
      update_option($this->words_to_filter_db_option, sanitize_text_field($_POST[$this->words_to_filter_db_option])); ?>
      <div class="updated">
        <p>Your filtered words were saved</p>
      </div>
    <?php } else { ?>
      <div class="error">
        <p>Sorry, you do not have permission to perform that action</p>
      </div>
    <?php }
  }

  function options_sub_page()
  { ?>
    <div class="wrap">
      <h1>Word Filter Options</h1>
      <form action="options.php" method="post">
        <?php
        settings_fields($this->settings__replacement__replacement_fields__group_id);
        do_settings_sections($this->settings_section_slug);
        submit_button();
        ?>
      </form>
    </div>
<?php }
}

$aquaOnePlugin = new Vecktor_AquaOnePlugin();
