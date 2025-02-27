<?php
function smtpGmail($file_path) {
    $credenciales = json_decode(file_get_contents($file_path));
    return (object)[
        'Host' => 'smtp.gmail.com',
        'Port' => 587,
        'SMTPSecure' => 'tls',
        'Username' => $credenciales->UsernameGmail,
        'Password' => $credenciales->PasswordGmail
    ];
}

function smtpOutlook($file_path) {
    $credenciales = json_decode(file_get_contents($file_path));
    return (object)[
        'Host' => 'smtp.office365.com',
        'Port' => 587,
        'SMTPSecure' => 'tls',
        'Username' => $credenciales->UsernameOutlook,
        'Password' => $credenciales->PasswordOutlook
    ];
}
?>