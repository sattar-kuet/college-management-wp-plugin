<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.itscholarbd.com
 * @since             1.0.0
 * @package           College_Management
 *
 * @wordpress-plugin
 * Plugin Name:       College Management
 * Plugin URI:        https://www.itscholarbd.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            ITscholarBD
 * Author URI:        https://www.itscholarbd.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       college-management
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'COLLEGE_MANAGEMENT_VERSION', '1.0.7' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-college-management-activator.php
 */
function activate_college_management() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-college-management-activator.php';
	College_Management_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-college-management-deactivator.php
 */
function deactivate_college_management() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-college-management-deactivator.php';
	College_Management_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_college_management' );
register_deactivation_hook( __FILE__, 'deactivate_college_management' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
 require plugin_dir_path( __FILE__ ) . 'includes/class-college-management.php';
 require plugin_dir_path( __FILE__ ) . 'includes/student-list.php';
 require plugin_dir_path( __FILE__ ) . 'includes/subject-list.php';
 require plugin_dir_path( __FILE__ ) . 'includes/student-subject-list.php';
 require plugin_dir_path( __FILE__ ) . 'includes/exam_list.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_college_management() {

	$plugin = new College_Management();
	$plugin->run();

}
run_college_management();

add_action('admin_menu', 'madp_portal_admin_pages');

function madp_portal_admin_pages() {
    add_menu_page(
        'Institute Manage', 
        'Students', 
        'manage_options', 
        'itscholarbd_institute_manage', 
        'itscholarbd_institute_manage_action',
        'dashicons-bank',
        3
    );
    
    add_submenu_page(
        'itscholarbd_institute_manage', 
        'Subjects', 
        'Subjects', 
        'manage_options', 
        'itscholarbd_subject', 
        'itscholarbd_subject_action'
    );
    
 add_submenu_page(
        'itscholarbd_institute_manage', 
        'Student Subjects', 
        'Student Subjects', 
        'manage_options', 
        'itscholarbd_student_subject', 
        'itscholarbd_student_subject_action'
    );
 add_submenu_page(
        'itscholarbd_institute_manage', 
        'Exam & Result', 
        'Exam & Result', 
        'manage_options', 
        'itscholarbd_exam_configuration', 
        'itscholarbd_exam_configuration_action'
    );
    
}


function itscholarbd_institute_manage_action() {
    if(!isset($_GET['action'])){
        show_student_list();
    } 
    else{
        if($_GET['action'] == 'add'){
            require plugin_dir_path( __FILE__ ) . 'includes/add-student.php';
        }
         if($_GET['action'] == 'edit'){
            require plugin_dir_path( __FILE__ ) . 'includes/edit-student.php';
        }
    }    
}

function itscholarbd_subject_action() {
    if(!isset($_GET['action'])){
        show_subject_list();
    } 
    else{
        if($_GET['action'] == 'add'){
            require plugin_dir_path( __FILE__ ) . 'includes/add-subject.php';
        }
         if($_GET['action'] == 'edit'){
            require plugin_dir_path( __FILE__ ) . 'includes/edit-subject.php';
        }
    }    
}

function itscholarbd_student_subject_action() {
    if(!isset($_GET['action'])){
        show_student_subject_list();
    } 
    else{
        if($_GET['action'] == 'config'){
            require plugin_dir_path( __FILE__ ) . 'includes/student-subject-config.php';
        }
         if($_GET['action'] == 'edit'){
            require plugin_dir_path( __FILE__ ) . 'includes/edit-student-subject.php';
        }
    }    
}
function itscholarbd_exam_configuration_action() {
    if(!isset($_GET['action'])){
        show_exam_list();
    } 
    else{
        if($_GET['action'] == 'add'){
            require plugin_dir_path( __FILE__ ) . 'includes/add-exam-config.php';
        }
         if($_GET['action'] == 'edit'){
            require plugin_dir_path( __FILE__ ) . 'includes/edit-exam-config.php';
        }
        if($_GET['action'] == 'config'){
            require plugin_dir_path( __FILE__ ) . 'includes/exam-config.php';
        } 
        if($_GET['action'] == 'result_input'){
            require plugin_dir_path( __FILE__ ) . 'includes/exam-result-input.php';
        }
        if($_GET['action'] == 'result_show'){
            require plugin_dir_path( __FILE__ ) . 'includes/exam-result-show.php';
        }
        if($_GET['action'] == 'result_publish'){
            require plugin_dir_path( __FILE__ ) . 'includes/utility.php';
            global $wpdb;
            $table_name = $wpdb->prefix . 'exam';
            $data = $_POST;
            extract($data);
            $exam_id = $_GET['id'];
            process_result($exam_id);
            $sql = "UPDATE $table_name SET status='published' WHERE id=$exam_id";
           // echo $sql; exit;
            $wpdb->query($wpdb->prepare($sql));
                show_exam_list();
            
        }
    }    
}
?>