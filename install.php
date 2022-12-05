<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once('install/licences.php');
require_once('install/licence_activity.php');
require_once('install/licence_items.php');
require_once('install/licence_members.php');



$CI->db->query("
INSERT INTO `tblemailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES
('licence', 'licence-send-to-client', 'english', 'Send licence to Customer', 'licence # {licence_number} created', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /><br /><span style=\"font-size: 12pt;\">Please find the attached licence <strong># {licence_number}</strong></span><br /><br /><span style=\"font-size: 12pt;\"><strong>licence status:</strong> {licence_status}</span><br /><br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /><br /><span style=\"font-size: 12pt;\">We look forward to your communication.</span><br /><br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}<br /></span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-already-send', 'english', 'licence Already Sent to Customer', 'licence # {licence_number} ', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /> <br /><span style=\"font-size: 12pt;\">Thank you for your licence request.</span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">Please contact us for more information.</span><br /> <br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-declined-to-staff', 'english', 'licence Declined (Sent to Staff)', 'Customer Declined licence', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) declined licence with number <strong># {licence_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-accepted-to-staff', 'english', 'licence Accepted (Sent to Staff)', 'Customer Accepted licence', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) accepted licence with number <strong># {licence_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-thank-you-to-customer', 'english', 'Thank You Email (Sent to Customer After Accept)', 'Thank for you accepting licence', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /> <br /><span style=\"font-size: 12pt;\">Thank for for accepting the licence.</span><br /> <br /><span style=\"font-size: 12pt;\">We look forward to doing business with you.</span><br /> <br /><span style=\"font-size: 12pt;\">We will contact you as soon as possible.</span><br /> <br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-expiry-reminder', 'english', 'licence Expiration Reminder', 'licence Expiration Reminder', '<p><span style=\"font-size: 12pt;\">Hello {contact_firstname} {contact_lastname}</span><br /><br /><span style=\"font-size: 12pt;\">The licence with <strong># {licence_number}</strong> will expire on <strong>{licence_duedate}</strong></span><br /><br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /><br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span></p>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-send-to-client', 'english', 'Send licence to Customer', 'licence # {licence_number} created', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /><br /><span style=\"font-size: 12pt;\">Please find the attached licence <strong># {licence_number}</strong></span><br /><br /><span style=\"font-size: 12pt;\"><strong>licence status:</strong> {licence_status}</span><br /><br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /><br /><span style=\"font-size: 12pt;\">We look forward to your communication.</span><br /><br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}<br /></span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-already-send', 'english', 'licence Already Sent to Customer', 'licence # {licence_number} ', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /> <br /><span style=\"font-size: 12pt;\">Thank you for your licence request.</span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">Please contact us for more information.</span><br /> <br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-declined-to-staff', 'english', 'licence Declined (Sent to Staff)', 'Customer Declined licence', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) declined licence with number <strong># {licence_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-accepted-to-staff', 'english', 'licence Accepted (Sent to Staff)', 'Customer Accepted licence', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) accepted licence with number <strong># {licence_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'staff-added-as-project-member', 'english', 'Staff Added as Project Member', 'New project assigned to you', '<p>Hi <br /><br />New licence has been assigned to you.<br /><br />You can view the licence on the following link <a href=\"{licence_link}\">licence__number</a><br /><br />{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-accepted-to-staff', 'english', 'licence Accepted (Sent to Staff)', 'Customer Accepted licence', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) accepted licence with number <strong># {licence_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0);
");
/*
 *
 */

// Add options for licences
add_option('delete_only_on_last_licence', 1);
add_option('licence_prefix', 'LCE-');
add_option('next_licence_number', 1);
add_option('default_licence_assigned', 9);
add_option('licence_number_decrement_on_delete', 0);
add_option('licence_number_format', 4);
add_option('licence_year', date('Y'));
add_option('exclude_licence_from_client_area_with_draft_status', 1);
add_option('predefined_clientnote_licence', '- Staf diatas untuk melakukan riksa uji pada peralatan tersebut.
- Staf diatas untuk membuat dokumentasi riksa uji sesuai kebutuhan.');
add_option('predefined_terms_licence', '- Pelaksanaan riksa uji harus mengikuti prosedur yang ditetapkan perusahaan pemilik alat.
- Dilarang membuat dokumentasi tanpa seizin perusahaan pemilik alat.
- Dokumen ini diterbitkan dari sistem CRM, tidak memerlukan tanda tangan dari PT. Cipta Mas Jaya');
add_option('licence_due_after', 1);
add_option('allow_staff_view_licences_assigned', 1);
add_option('show_assigned_on_licences', 1);
add_option('require_client_logged_in_to_view_licence', 0);

add_option('show_project_on_licence', 1);
add_option('licences_pipeline_limit', 1);
add_option('default_licences_pipeline_sort', 1);
add_option('licence_accept_identity_confirmation', 1);
add_option('licence_qrcode_size', '160');
add_option('licence_send_telegram_message', 0);


/*

DROP TABLE `tbllicences`;
DROP TABLE `tbllicence_activity`, `tbllicence_items`, `tbllicence_members`;
delete FROM `tbloptions` WHERE `name` LIKE '%licence%';
DELETE FROM `tblemailtemplates` WHERE `type` LIKE 'licence';



*/