// JavaScript Translation Constants for ICT Control Center
const TRANSLATIONS = {
    // Form validation messages
    'please_fill_all_fields': 'Please fill in all required fields',
    'passwords_not_match': 'Passwords do not match',
    'password_min_6_chars': 'Password must be at least 6 characters',
    'processing': 'Processing...',
    'verifying': 'Verifying...',
    
    // Registration messages
    'verify_complete_registration': 'Verify and Complete Registration',
    'registration_successful': 'Registration Successful! 🎉',
    'account_created_successfully': '✅ Account created successfully! Redirecting to login...',
    'registration_complete_login': 'Registration complete! Please log in with your email',
    
    // OTP messages
    'please_enter_complete_otp': 'Please enter the complete 6-digit OTP code',
    'invalid_code_wait': 'Invalid code — Wait for a new code in the app and try again',
    
    // Error messages
    'error_occurred': 'An error occurred',
    'connection_failed': 'Connection failed',
    'try_again': 'Try again',
    
    // Timer messages
    'new_code_in': 'New code in',
    'seconds': 'seconds'
};

// Translation function for JavaScript
function t(key, defaultValue = '') {
    return TRANSLATIONS[key] || defaultValue;
}