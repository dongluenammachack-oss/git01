<?php
/**
 * Translation Constants for ICT Control Center
 * Converting Lao text to English
 */

// Define translation constants
define('TRANSLATIONS', [
    // Login Form
    'secure_login' => 'Secure Login',
    'username_email' => 'Username / Email',
    'password' => 'Password',
    'login' => 'Log in',
    'forgot_password' => 'Forgot Password',
    'create_account' => 'Create Account',
    'reset_password' => 'Reset Password',
    
    // Registration Form
    'scan_qr_code' => 'Scan QR Code',
    'enter_otp_code' => 'Enter OTP Code',
    'full_name' => 'Full Name',
    'email' => 'Email',
    'confirm_password' => 'Confirm Password',
    'register' => 'Register',
    
    // QR Code Setup
    'if_cannot_scan' => 'If you cannot scan, enter the key manually',
    'download_authenticator' => 'Download <strong>Google Authenticator</strong> or <strong>Microsoft Authenticator</strong>',
    'tap_plus_add_account' => 'Tap <strong>"+"</strong> to add a new account',
    'select_scan_qr' => 'Select <strong>"Scan QR Code"</strong> and scan the QR code above',
    'tap_next_enter_code' => 'Tap <strong>"Next"</strong> and enter the 6-digit code',
    'scan_complete_next' => 'Scan Complete — Next',
    
    // OTP Verification
    'enter_6_digit_code' => 'Enter the <strong>6-digit code</strong> from <strong>Google/Microsoft Authenticator</strong> for <strong>ICT HALO Laos</strong>',
    'new_code_in_seconds' => 'New code in',
    'seconds' => 'seconds',
    'verify_complete_registration' => 'Verify and Complete Registration',
    'scan_qr_code_again' => 'Scan QR Code Again',
    
    // Password Reset
    'minimum_6_characters' => 'Minimum 6 characters',
    'password_match' => 'Passwords match',
    'password_no_match' => 'Passwords do not match',
    
    // Error Messages
    'please_fill_all_fields' => 'Please fill in all required fields',
    'passwords_not_match' => 'Passwords do not match',
    'password_min_6_chars' => 'Password must be at least 6 characters',
    'processing' => 'Processing...',
    'error_occurred' => 'An error occurred',
    'connection_failed' => 'Connection failed',
    'please_enter_complete_otp' => 'Please enter the complete 6-digit OTP code',
    'registration_successful' => 'Registration Successful! 🎉',
    'account_created_successfully' => '✅ Account created successfully! Redirecting to login...',
    'registration_complete_login' => 'Registration complete! Please log in with your email',
    'invalid_code' => 'Invalid code',
    'wait_for_new_code' => 'Wait for a new code in the app and try again',
    
    // Success Messages
    'login_successful' => 'Login successful',
    'password_reset_successful' => 'Password reset successful',
    'account_verified' => 'Account verified successfully',
    
    // Form Labels
    'required' => '*',
    'optional' => '(optional)',
    
    // Buttons
    'submit' => 'Submit',
    'cancel' => 'Cancel',
    'back' => 'Back',
    'next' => 'Next',
    'continue' => 'Continue',
    'close' => 'Close',
    'copy_key' => 'Copy Key',
    
    // Placeholders
    'enter_username_email' => 'admin or registered Email',
    'enter_password' => 'Enter your password',
    'enter_full_name' => 'Enter your full name',
    'enter_email' => 'Enter your email address',
    'confirm_your_password' => 'Confirm your password',
    
    // Validation Messages
    'field_required' => 'This field is required',
    'invalid_email' => 'Please enter a valid email address',
    'password_too_short' => 'Password is too short',
    'passwords_must_match' => 'Passwords must match',
    
    // Login Attempts
    'login_failed_3_times' => 'Incorrect username or password more than 3 times. Please reset your password.',
    'account_not_verified' => 'Account not verified with Authenticator',
    'incorrect_credentials' => 'Incorrect username or password!',
    
    // Additional form elements
    'full_name_placeholder' => 'full name and last name',
    'email_placeholder' => 'example@halotrust.org',
    'account_information' => 'Account Information',
    'register_to_ict_system' => 'Register to ICT System',
    
    // Additional validation
    'email_already_in_use' => 'This email is already in use',
    'invalid_email_format' => 'Please enter a valid email address',
    'password_too_weak' => 'Password is too weak',
]);

/**
 * Get translation for a given key
 * @param string $key Translation key
 * @param string $default Default value if key not found
 * @return string Translated text
 */
function t($key, $default = '') {
    return TRANSLATIONS[$key] ?? $default;
}

/**
 * Echo translation for a given key
 * @param string $key Translation key
 * @param string $default Default value if key not found
 */
function _t($key, $default = '') {
    echo t($key, $default);
}
?>