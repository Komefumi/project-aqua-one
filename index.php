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
  private string $setting_slug = 'our-word-filter';
  private string $setting_slug__options = 'word-filter-options';
  function __construct()
  {
    add_action('admin_menu', array($this, 'our_menu'));
  }

  function our_menu()
  {
    $required_capability_as_permission = 'manage_options';
    $main_page_hook = add_menu_page(
      'Word to Filter',
      'Word Filter',
      $required_capability_as_permission,
      $this->setting_slug,
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
      $this->setting_slug,
      'Words To Filter',
      'Words List',
      $required_capability_as_permission,
      $this->setting_slug,
      array($this, 'word_filter_page'),
    );
    add_submenu_page(
      $this->setting_slug,
      'Word Filter Options',
      'Options',
      $required_capability_as_permission,
      $this->setting_slug__options,
      array($this, 'options_sub_page'),
    );
    add_action("load-$main_page_hook", array($this, 'main_page_assets'));
  }

  function main_page_assets()
  {
    wp_enqueue_style('filter_admin_css', plugin_dir_url(__FILE__) . 'styles/filter_admin.css');
  }

  function word_filter_page()
  { ?>
    <div class="wrap">
      <h1>Word Filter</h1>
      <form method="POST">
        <label for="plugin_words_to_filter">
          <p>Enter a <strong>comma-separated</strong> list of words to filter your site contents</p>
        </label>
        <div class="word-filter__flex-container">
          <textarea name="plugin_words_to_filter" id="plugin_words_to_filter" cols="30" rows="10" placeholder="bad, mean, awful, horrible"></textarea>
        </div>
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
      </form>
    </div>
  <?php }

  function options_sub_page()
  { ?>
    <div>Options Page</div>
<?php }
}

$aquaOnePlugin = new Vecktor_AquaOnePlugin();
