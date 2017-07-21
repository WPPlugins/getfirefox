<?php

/*
Plugin Name: GetFirefox
Plugin URI: http://www.firefox-jetzt.de
Description: Spread the word of firefox by displaying a <strong>"get firefox"</strong> button in the sidebar of your blog via widget interface or function call <em>&lt;?php get_firefox(); ?&gt;</em> to recommend your readers to <a href="http://www.firefox-jetzt.de">firefox download</a> and take back the web!  
Version: 0.1
Author: habbylow
Author URI: http://www.firefox-jetzt.de
Tags: mozilla, badget, widget, firefox, browser, web, internet, sidebar, widgets, badgets
*/

if(class_exists('WP_Widget')):

class GetFirefoxWidget extends WP_Widget {
	
  function GetFirefoxWidget() {		
		$this->WP_Widget(
      'getfirefoxwidget', 
      'GetFirefox Widget',
      array(
        'classname' => 'widget_getfirefox',
        'description' => __('Displays a "GetFirefox" button in the sidebar of your blog.', $this->id)
      )
    );
	}
 
	function widget($args, $instance) {
    global $GetFirefox;
    
    $GetFirefox->display(true);
	}
 
	function update($new_instance, $old_instance) {
 
		return $instance;
	}
 
	function form($instance) {
    global $GetFirefox;
    $GetFirefox->_widgetControl();
	}
}

endif;

class GetFirefox {
  var $id;
  var $path;
  var $options;
  var $version;
  var $locale;
  var $languages;
  var $plugin_url;

  function GetFirefox() {
    $this->id         = 'getfirefox';
    $this->name       = 'GetFirefox';
    $this->version    = '0.1';
    $this->path       = dirname(__FILE__);
    $this->plugin_url = get_bloginfo('wpurl'). '/wp-content/plugins/'. $this->id;

    $this->layouts = array(
      // width, height, title, description, sidebar
      '1' => array(88, 33, 'Button 88x33', __('Icon and text, rounded border.', $this->id), true),
      '2' => array(80, 15, 'Button 80x15', __('Icon and text, edgy border.', $this->id), true),
      '3' => array(30, 28, 'Button 30x28', __('Icon only, rounded border.', $this->id), true),
      '4' => array(120, 121, 'Pagepeel 120x121', __('Pagepeel, top left.', $this->id), false),
      '5' => array(120, 121, 'Pagepeel 120x121', __('Pagepeel, top right.', $this->id), false)
    );
    
    $this->languages = array('de_DE');
    
	  $this->locale = get_locale();

	  if(empty($this->locale)) {
		  $this->locale = 'en_US';
    }

    load_textdomain($this->id, $this->path. '/'. $this->locale. '.mo');
 
    if(!$this->options = get_option($this->id)) {
      $this->options = array(
        'installed'     => time(),
        'count_show'    => 0,
        'count_value'   => 1312797759,
        'count_timeout' => 3600,
        'show_always'   => 1,
        'layout'        => 1,
        'title'         => 'Get Firefox'
			);
    }
    
    if(is_admin()) {
      add_action('admin_menu', array(&$this, 'optionMenu')); 
    }
    else {
      add_action('wp_head', array(&$this, 'blogHeader'));
      add_action('wp_footer', array(&$this, 'blogFooter'));
    }

    add_action('widgets_init', array(&$this, 'initWidgets'));
  }
  
  function blogFooter() {
    if(!$this->layouts[$this->options['layout']][4]) {
      $this->display(false);
    }
    
    if(intval($this->options['show_always']) == 1) {
      echo "<script type=\"text/javascript\">if(navigator.userAgent.toLowerCase().indexOf('firefox')!=-1)if(getfirefox=document.getElementById('getfirefox'))getfirefox.style.display='none';</script>";
    }
  }
  
  function blogHeader() {    
    printf('<link rel="stylesheet" type="text/css" href="%s/wp-content/plugins/%s/%s.css" />', get_bloginfo('wpurl'), $this->id, $this->id);
  }
  
  function initWidgets() {
    // since wp 2.8
    if(class_exists('WP_Widget')) {
      register_widget('GetFirefoxWidget');
    }
    else {
      if(function_exists('register_sidebar_widget' )) {
  			register_sidebar_widget('GetFirefoxWidget', array(&$this, 'widget'));
  			
  			if(function_exists('register_widget_control' )) {
          register_widget_control('GetFirefoxWidget', array(&$this, 'widgetControl' ), null, 75);
        }
      }
    }
  }

  function widget($args) {
    if($this->layouts[$this->options['layout']][4]) {
      extract($args);
      printf('%s%s%s%s%s%s', $before_widget, $before_title, $this->options['title'], $after_title, $this->getCode(true), $after_widget);
    }
  }
  
  function widgetControl() {
    $this->_widgetControl();
  }
  
  function _widgetControl() {
    
    printf('<strong>%s</strong><br /><img src="%s/img/%d.gif" />%s<br />%s <a href="%s/wp-admin/options-general.php?page=%s/%s.php" target="_blank">%s</a>.',
      $this->options['title'],
      $this->plugin_url, 
      $this->options['layout'],
      $this->layouts[$this->options['layout']][4] ? '' : '<br /><strong>'. __('Note', $this->id). '</strong>: '. __('This layout will not appear in the sidebar but in a page corner!', $this->id). '<br />',
      __('Change title and layout on the', $this->id),
      get_bloginfo('wpurl'),
      $this->id,
      $this->id,
      __('option page', $this->id)
    );
	}
	
	function getLayoutsAsSelectbox($params = array('status' => array(0, 1), 'selected' => -1)) {

    foreach($this->layouts as $k => $single) {
      $data .= sprintf('<option value="%s"%s>%s</option>', $k, $single->id == $this->options['layout'] ? ' selected="selected"' : '', $single[2]);
    }
    
    return $data;
  }

  
  function optionMenu() {
    add_options_page($this->name, $this->name, 8, __FILE__, array(&$this, 'optionMenuPage'));
  }
  
  function optionMenuPage() {
    
    if(isset($_POST[$this->id])) {
      $this->updateOptions($_POST[$this->id]);
    }
        
    $fields = array(
      // key => type, title, extra
      'title'         => array('text',        __('Title', $this->id)),
      'layout'        => array('radiogroup',  __('Layout', $this->id), '', $this->layouts,  2),
      'show_always'   => array('yesnoradio',  __('Show only to not Firefox users?', $this->id), ''),
      'count_show'    => array('yesnoradio',  __('Show download count?', $this->id), ''),
      'count_timeout' => array('text',        __('Update download count after ', $this->id))
    );

?>
<div class="wrap">

<h2><?php echo $this->name; ?></h2> 

<h3><?php _e('Integration', $this->id); ?></h3>
<ul>
  <li><?php _e('If your Theme is widget ready, go to <a href="widgets.php">Themes -> Widgets</a> and activate the "GetFirefoxWidget".', $this->id); ?></li>
  <li><?php _e('Otherwise you have to past this code snippet "<code style="background-color:#ddd;">&lt;?php if(function_exists(\'get_firefox\'))get_firefox(); ?&gt;</code>" to the template file of your choice. Most likely <em>sidebar.php</em>.', $this->id); ?></li> 
</ul>

<h3><?php _e('Settings', $this->id); ?></h3>

<form method="post" action="">

<table class="form-table">
<?php
foreach($fields as $k => $v) {
  printf('<tr valign="top"><th scope="row">%s</th><td width="400">%s</td><td>%s</td></tr>', $v[1], $this->getFormfield($k, $v[0], $this->options[$k], '', $this->id, isset($v[3]) ? $v[3] : array()), $v[2]);
}
?>
</table>

<p class="submit" align="right">
  <input type="submit" value="<?php _e('save settings', $this->id); ?>" name="submit" />
</p>

</form>

</div>
<?php
  }
  
  function getCode($widget = false) {
    $locale = '';
    
    if(in_array($this->locale, $this->languages)) {
      $locale = ' '. $this->id. '-'. $this->options['layout']. '-'. $this->locale;
    }
    
    $count = '';
    $title = '';
    
    if($widget) {
      if($this->layouts[$this->options['layout']][4]) {
        if(intval($this->options['count_show']) == 1) {
          $count = '<p>'. number_format($this->options['count_value'], 0, '.', '.'). '</p>';
        }
        
        if($widget) {
          $title = '<h2>'. $this->options['title']. '</h2>';
        }
      }
    }
    
    return sprintf('<div id="%s" class="%s-%s%s%s">%s<div><a href="http://www.firefox-jetzt.de">Firefox Download</a></div>%s</div>', $this->id, $this->id, $this->options['layout'], $locale, $widget ? ' widget' : '', $title, $count);
  }
  
  function display($widget = false) {
    echo $this->getCode($widget);
  }

  function getCount() {
  	if($fh = @fopen('http://www.spreadfirefox.com/download_counter.php?ff=1', 'r')) {
  	 $data = '';
  
    	while($buffer = fread($fh, 4096)) {
    		$data .= $buffer;
    	}
    	
    	fclose($fh);

  	  if(($count = intval(preg_replace('|.*<description>(.*?)</description>.*|is', '$1', $data))) > 0) {
        $this->updateOptions(array('count_value' => $count));
      }
  	}
  }
  
  function getFormfield($name, $type, $value, $default = '', $prefix = '', $extra = array()) {
    if(!empty($prefix)) {
      $name = "{$prefix}[{$name}]";
    }
    
    switch($type) {
      case 'radiogroup':
        $data = '';
        foreach($extra as $k => $v) {
          $data .= sprintf(
            '<input type="radio" name="%s"%s value="%s" /><strong>%s</strong> - %s<br /><img src="%s/wp-content/plugins/%s/img/%d.gif" /><br /><br /><br />', 
            $name, 
            $value == $k ? ' checked="checked"' : '',
            $k,
            $v[2],
            $v[3],
            get_bloginfo('wpurl'),
            $this->id,
            $k
          );
        }
        return $data;
      case 'text':
        return sprintf('<input type="text" name="%s" value="%s"%s%s />', 
          $name, 
          empty($value) ? $default : $value,             
          array_key_exists('maxlength', $extra ) ? ' maxlength="'.$extra[ 'maxlength' ].'"' : '',
          array_key_exists('class', $extra) ? ' class="'.$extra[ 'class' ].'"' : '');
      case 'yesnoradio':
        return
          sprintf( '<input type="radio" name="%s"%s value="1" />%s <input type="radio" name="%s"%s value="0" />%s', 
            $name, 
            $value == 1 ? ' checked="checked"' : '',
            __('yes', $this->id),
            
            $name, 
            $value == 0 ? ' checked="checked"' : '',
            __('no', $this->id));
    }
  }
  
  function updateOptions($options) {
    foreach($this->options as $k => $v) {
      if(array_key_exists($k, $options)) {
        $this->options[$k] = $options[$k];
      }
    }

    unset($options);

    update_option($this->id, $this->options);
	}
}

function get_firefox() {
  global $GetFirefox;
  $GetFirefox->display(false);
}

add_action('plugins_loaded', create_function('$GetFirefox_a239eiq', 'global $GetFirefox; $GetFirefox = new GetFirefox();'));

?>