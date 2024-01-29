<?php
/**
 * Plugin Name: IP Location Catcher for Contact Form 7
 * Description: Captures user IP location when a Contact Form 7 form is submitted.
 * Version: 1.0
 * Author: Victor
 */

// Hook into Contact Form 7 form submission
add_action('wpcf7_before_send_mail', 'capture_user_ip_location');

function capture_user_ip_location($contact_form) {
    // Get the Contact Form 7 submission instance
    $submission = WPCF7_Submission::get_instance();

    // If submission exists, proceed
    if ($submission) {

        // Get user's IP address
        if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $user_ip = $_SERVER['REMOTE_ADDR'];
        }

        // Get user's IP location using
        $ip_info = json_decode(file_get_contents("https://ipinfo.io/{$user_ip}/json"));

        // Extract relevant location information
        $location = isset($ip_info->city) ? $ip_info->city . ', ' : '';
        $location .= isset($ip_info->region) ? $ip_info->region . ', ' : '';
        $location .= isset($ip_info->country) ? $ip_info->country : '';

        // Add the location to the Contact Form 7 email body
        $mail = $contact_form->prop('mail');
        $mail['body'] .= "\nUser Location: {$location}"; 

        // Update the mail properties
        $contact_form->set_properties(array('mail' => $mail));
    } 
}