<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.itscholarbd.com
 * @since      1.0.0
 *
 * @package    College_Management
 * @subpackage College_Management/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    College_Management
 * @subpackage College_Management/includes
 * @author     ITscholarBD <contact@itscholarbd.com>
 */
class College_Management_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        global $wpdb; 
        $db_table_name = $wpdb->prefix . 'subject';  
        $charset_collate = $wpdb->get_charset_collate();
       
         //Check to see if the table exists already, if not, then create it
        if($wpdb->get_var( "show tables like '$db_table_name'" ) != $db_table_name ) 
        {

               $sql = "CREATE TABLE $db_table_name (
                        id int(11) NOT NULL auto_increment,
                        name varchar(200) NOT NULL,
                        parent_id int(11) NOT NULL,
                        has_two_part tinyint(1) NOT NULL,
                        group_name varchar(200) NOT NULL,
                        mendatory tinyint(1) NOT NULL,
                        UNIQUE KEY id (id)
                ) $charset_collate;";

           require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
           //echo $sql; exit;
           dbDelta( $sql );
        }

        $db_table_name = $wpdb->prefix . 'student';  
        $charset_collate = $wpdb->get_charset_collate();
       
         //Check to see if the table exists already, if not, then create it
        if($wpdb->get_var( "show tables like '$db_table_name'" ) != $db_table_name ) 
        {

               $sql = "CREATE TABLE $db_table_name (
                        id int(11) NOT NULL auto_increment,
                        name varchar(200) NOT NULL,
                        roll int(11) NOT NULL,
                        session_start int(11) NOT NULL,
                        session_end int(11) NOT NULL,
                        group_name varchar(200) NOT NULL,
                        UNIQUE KEY id (id)
                ) $charset_collate;";

           require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
           //echo $sql; exit;
           dbDelta( $sql );
        }


        $db_table_name = $wpdb->prefix . 'student_subject';  
        $charset_collate = $wpdb->get_charset_collate();
       
         //Check to see if the table exists already, if not, then create it
        if($wpdb->get_var( "show tables like '$db_table_name'" ) != $db_table_name ) 
        {

               $sql = "CREATE TABLE $db_table_name (
                        id int(11) NOT NULL auto_increment,
                        student_id int(11) NOT NULL,
                        subject_id int(11) NOT NULL,
                        subject_type tinyint(1) NOT NULL,
                        UNIQUE KEY id (id)
                ) $charset_collate;";

           require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
           //echo $sql; exit;
           dbDelta( $sql );
        }

        $db_table_name = $wpdb->prefix . 'exam';  
        $charset_collate = $wpdb->get_charset_collate();
       
         //Check to see if the table exists already, if not, then create it
        if($wpdb->get_var( "show tables like '$db_table_name'" ) != $db_table_name ) 
        {

               $sql = "CREATE TABLE $db_table_name (
                        id int(11) NOT NULL auto_increment,
                        name varchar(200) NOT NULL,
                        session_start int(11) NOT NULL,
                        session_end int(11) NOT NULL,
                        status varchar(20) default 'draft',
                        UNIQUE KEY id (id)
                ) $charset_collate;";

           require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
           //echo $sql; exit;
           dbDelta( $sql );
        }

        $db_table_name = $wpdb->prefix . 'exam_configuration';  
        $charset_collate = $wpdb->get_charset_collate();
       
         //Check to see if the table exists already, if not, then create it
        if($wpdb->get_var( "show tables like '$db_table_name'" ) != $db_table_name ) 
        {

               $sql = "CREATE TABLE $db_table_name (
                        id int(11) NOT NULL auto_increment,
                        exam_id int(11) NOT NULL,
                        subject_id int(11) NOT NULL,
                        mcq_mark double NOT NULL,
                        mcq_pass_mark double NOT NULL,
                        written_mark double NOT NULL,
                        written_pass_mark double NOT NULL,
                        UNIQUE KEY id (id)
                ) $charset_collate;";

           require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
           //echo $sql; exit;
           dbDelta( $sql );
        }


        $db_table_name = $wpdb->prefix . 'student_result';  
        $charset_collate = $wpdb->get_charset_collate();
       
         //Check to see if the table exists already, if not, then create it
        if($wpdb->get_var( "show tables like '$db_table_name'" ) != $db_table_name ) 
        {
               $sql = "CREATE TABLE $db_table_name (
                        id int(11) NOT NULL auto_increment,
                        exam_id int(11) NOT NULL,
                        group_name varchar(200) NOT NULL,
                        student_id int(11) NOT NULL,
                        subject_id int(11) NOT NULL,
                        subject_parent_id int(11) NOT NULL,
                        subject_type tinyint(1) NOT NULL,
                        written_mark double NOT NULL,
                        mcq_mark double NOT NULL,
                        UNIQUE KEY id (id)
                ) $charset_collate;";

           require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
           //echo $sql; exit;
           dbDelta( $sql );
        }
        $db_table_name = $wpdb->prefix . 'processed_result';  
        $charset_collate = $wpdb->get_charset_collate();
       
         //Check to see if the table exists already, if not, then create it
        if($wpdb->get_var( "show tables like '$db_table_name'" ) != $db_table_name ) 
        {
               $sql = "CREATE TABLE $db_table_name (
                        id int(11) NOT NULL auto_increment,
                        exam_id int(11) NOT NULL,
                        group_name varchar(200) NOT NULL,
                        student_id int(11) NOT NULL,
                        subject_id int(11),
                        written_mark double NOT NULL,
                        mcq_mark double NOT NULL,
                        total_mark double NOT NULL,
                        percentage_mark double NOT NULL,
                        gpa double NOT NULL,
                        grade_name varchar(30) NOT NULL,
                        UNIQUE KEY id (id)
                ) $charset_collate;";

           require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
           //echo $sql; exit;
           dbDelta( $sql );
        }
       
        $sql = "ALTER TABLE `wp_subject` ADD `is_practical` TINYINT(1) NOT NULL DEFAULT '0' AFTER `mendatory`";
        $wpdb->query($wpdb->prepare($sql));
        
        $sql = "ALTER TABLE `wp_subject` ADD `has_practical` TINYINT(1) NOT NULL DEFAULT '0' AFTER `mendatory`";
        $wpdb->query($wpdb->prepare($sql));

        $sql = "ALTER TABLE `wp_exam_configuration` ADD `practical_mark` DOUBLE NOT NULL DEFAULT '0' AFTER `written_pass_mark`, ADD `practical_pass_mark` DOUBLE NOT NULL DEFAULT '0' AFTER `practical_mark`";
        $wpdb->query($wpdb->prepare($sql));

        $sql = "ALTER TABLE `wp_exam_configuration` CHANGE `mcq_mark` `mcq_mark` DOUBLE NOT NULL DEFAULT '0', CHANGE `mcq_pass_mark` `mcq_pass_mark` DOUBLE NOT NULL DEFAULT '0', CHANGE `written_mark` `written_mark` DOUBLE NOT NULL DEFAULT '0', CHANGE `written_pass_mark` `written_pass_mark` DOUBLE NOT NULL DEFAULT '0'";
        $wpdb->query($wpdb->prepare($sql));
     }

}
?>