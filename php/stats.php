<?php

class AuthorStats {
  function view() {
?>
	<div class="wrap" id="author-stats-wrap">
	<div id="author-stats-title">
		<h2>Author Stats</h2>
		<br/>
		<form method="GET" action="">
        <div class="ui-widget">
        	<label>Select Author: </label>
            <?php print $this->get_author_select(); ?>
            <input type="submit" id="display_stats" value="Display Stats"/>
            <input type="hidden" name="page" value="<?php echo $_GET['page']?>"/>
		</div>
		</form>
    
	</div>
	
	<style>
	.ui-button { margin-left: -1px; }
	.ui-button-icon-only .ui-button-text { padding: 0.35em; } 
	.ui-autocomplete-input { margin: 0; padding: 0.48em 0 0.47em 0.45em; width: 250px;}
	</style>
	<script>
	
    
	(function( $ ) {
		$.widget( "ui.combobox", {
			_create: function() {
				var self = this,
					select = this.element.hide(),
					selected = select.children( ":selected" ),
					value = selected.val() ? selected.text() : "";
				var input = this.input = $( "<input>" )
					.insertAfter( select )
					.val( value )
					.autocomplete({
						delay: 0,
						minLength: 3,
						source: function( request, response ) {
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
							response( select.children( "option" ).map(function() {
								var text = $( this ).text();
								if ( this.value && ( !request.term || matcher.test(text) ) )
									return {
										label: text.replace(
											new RegExp(
												"(?![^&;]+;)(?!<[^<>]*)(" +
												$.ui.autocomplete.escapeRegex(request.term) +
												")(?![^<>]*>)(?![^&;]+;)", "gi"
											), "<strong>$1</strong>" ),
										value: text,
										option: this
									};
							}) );
						},
						select: function( event, ui ) {
							ui.item.option.selected = true;
							self._trigger( "selected", event, {
								item: ui.item.option
							});
						},
						change: function( event, ui ) {
							if ( !ui.item ) {
								var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
									valid = false;
								select.children( "option" ).each(function() {
									if ( $( this ).text().match( matcher ) ) {
										this.selected = valid = true;
										return false;
									}
								});
								if ( !valid ) {
									// remove invalid value, as it didn't match anything
									$( this ).val( "" );
									select.val( "" );
									input.data( "autocomplete" ).term = "";
									return false;
								}
							}
						}
					})
					.addClass( "ui-widget ui-widget-content ui-corner-left" );

				input.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
				};
			},

			destroy: function() {
				this.input.remove();
				this.button.remove();
				this.element.show();
				$.Widget.prototype.destroy.call( this );
			}
		});
	})( jQuery );

	jQuery(function() {
		jQuery( "#combobox" ).combobox();
		jQuery( "#toggle" ).click(function() {
			jQuery( "#combobox" ).toggle();
		});
		jQuery( "#display_stats" ).click(function() {
		    if (jQuery("#combobox").val() == "") {
		        jQuery("input.ui-autocomplete-input").focus();
		        return false;
		    }
		});
		
		<?php
		if ($_GET['author']) {
		?>
		jQuery("#combobox").val(<?php echo $_GET['author'] ?>);
		jQuery("input.ui-autocomplete-input").val(jQuery("#combobox option:selected").text())
		<?php } ?>
		jQuery("input.ui-autocomplete-input").focus(function() {
            jQuery('input.ui-autocomplete-input').val('');
		});
	});
	</script>
	
<?php
    if ($_GET['author']) {
        include_once(AUTHOR_STATS_ROOT . '/php/display_stats.php');
    }
  }
  
  function get_author_select() {
      $args = array('role' => 'adminsistrator', 'fields' => array('ID','display_name'));
      $admins = get_users($args);
      $args = array('role' => 'editor', 'fields' => array('ID','display_name'));
      $editors = get_users($args);
      $args = array('role' => 'author', 'fields' => array('ID','display_name'));
      $authors = get_users($args);
      $args = array('role' => 'contributor', 'fields' => array('ID','display_name'));
      $contributors = get_users($args);
            
      $text = '<select id="combobox" name="author">';
      $text .= '<option value>Select one..</option>';
      foreach ($admins as $user) {
          $text .= '<option value="'. $user->ID .'">' . $user->display_name . '</option>';
      }
      foreach ($editors as $user) {
          $text .= '<option value="'. $user->ID .'">' . $user->display_name . '</option>';
      }
      foreach ($authors as $user) {
          $text .= '<option value="'. $user->ID .'">' . $user->display_name . '</option>';
      }
      foreach ($contributors as $user) {
          $text .= '<option value="'. $user->ID .'">' . $user->display_name . '</option>';
      }

      $text .= '</select>';
      return $text;
  }
  
  function get_post_stats($author, $status, $time) {
      global $wpdb;
      $sql = "SELECT count(ID) as c FROM ". $wpdb->posts . " WHERE post_author=". $author;
      $sql .= " and post_status='" . $status . "'";
      if ($time) {
          $sql .= " and post_date BETWEEN DATE_SUB(NOW(), INTERVAL ". $time ." DAY) AND NOW();";
      }
      return $wpdb->get_row($sql);
  }
  
  function get_comment_stats($author, $time) {
      global $wpdb;
      $sql = "SELECT ID, post_title, comment_count as c FROM " . $wpdb->posts . " WHERE post_author=" . $author;
      $sql .= " and post_status='publish'";
      $sql .= " AND comment_count > 0";
      if ($time) {
          $sql .= " and post_date BETWEEN DATE_SUB(NOW(), INTERVAL ". $time . " DAY) AND NOW() ";
          $sql .= "ORDER BY comment_count DESC LIMIT 1;";
      }
      return $wpdb->get_row($sql);
  }
  
  function get_comment_avg($author, $time) {
     global $wpdb;
     $sql = "SELECT format(avg(comment_count), 1) as c FROM " . $wpdb->posts . " WHERE post_author=" . $author;
     $sql .= " and post_status='publish'";
     if ($time) {
         $sql .= " and post_date BETWEEN DATE_SUB(NOW(), INTERVAL " . $time . " DAY) AND NOW()";
     }
     return $wpdb->get_row($sql);
  }
  
  function get_word_count_avg($author, $time) {
      global $wpdb;
      $sql = "SELECT ID, post_content FROM " . $wpdb->posts . " WHERE post_author=" . $author;
      $sql .= " and post_status='publish'";
      if ($time) {
          $sql .= " and post_date BETWEEN DATE_SUB(NOW(), INTERVAL " . $time . " DAY) AND NOW()";
      }
      $rows = $wpdb->get_results($sql);
      $c = array();
      $d = array();
      foreach ($rows as $row) {
          $result = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', 
              strip_tags($row->post_content), -1, PREG_SPLIT_NO_EMPTY);
          array_push($c, count($result));
          array_push($d, array('id' => $row->ID, 'c' => count($result)));
      }
      if (count($c)) {
        return intval(array_sum($c)/count($c));
      } else {
        return 0;
      }
  }
  
  function get_inch_count_avg($author, $time) {
      global $wpdb;
      $sql = "SELECT post_content as c FROM " . $wpdb->posts . " WHERE post_author=" . $author;
      $sql .= " and post_status='publish'";
      if ($time) {
          $sql .= " and post_date BETWEEN DATE_SUB(NOW(), INTERVAL " . $time . " DAY) AND NOW()";
      }
      $r = $wpdb->get_results($sql);
      
      $options = get_option( COLUMN_INCHES_OPTION );
      $column_inches = $options['words_inch'];
      $words = $r->c;

      $num_counts = count($column_inches);
      $v = array();
      $c = 0;
      foreach ($r as $row) { 
          $c++;
          $post_plaintext = strip_tags( $row->c );
  		  $words = str_word_count( $post_plaintext );
  		
          // Display column inches
          for ($i = 0; $i < $num_counts; $i++) {
    	      $column_inch = $column_inches[$i];
    	      $name = $column_inch['name'];
    	      $inches = ceil( $words / $column_inch['count'] );
    	      $v[$name] += $inches;
          }		
      }
      
      $rv = array();
      foreach (array_keys($v) as $r) {
          $v[$r] = intval($v[$r]/$c);
          array_push($rv, $v[$r]);
      }
      
      return implode(" / ", $rv);
  }
}
?>