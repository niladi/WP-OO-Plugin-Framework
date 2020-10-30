<?php


namespace WPPluginCore\Domain\Entity\Attribute\Implementation;

use WPPluginCore\Exception\AttributeException;
use WPPluginCore\Domain\Entity\Attribute\Abstraction\Attribute;

defined('ABSPATH') || exit;

// class File extends Attribute
// {
//     private string $inputKey;

//     // todo wird eh noch nicht verwendet

//     public function __construct(string $key,string  $label)
//     {
//         parent::__construct($key, $label);
//         $this->inputKey = $key . '_set';
//     }

//     public function getAdminHTML(): String
//     {
//         $file = wp_get_attachment_url($this->post_id);
//         $html = '';
//         if ($file != '') {
//             $html .= '<input type="text"  name="' . $this->inputKey . '" value="' . $file . '"/>';
//             $html .= '<a href="' . $file . '">' . __('Runterladen', 'wp-plugin-core') . '</a>';
//         } else {
//             $html .= '<p class="description">';
//             $html .= __('PDF Hochladen', 'wp-plugin-core');
//             $html .= '</p>';
//             $html .= '<input type="file" id="' . $this->key . '" name="' . $this->key . '" value="" size="25">';
//         }

//         return $this->createTableInput($html);
//     }

//     public function loadFromPost() : void
//     {
//         if (! empty($_FILES[ $this->key ]['name'])) {
//             $supported_types = array( 'application/pdf' );
//             $arr_file_type   = wp_check_filetype(basename($_FILES[ $this->key ]['name']));
//             $uploaded_type   = $arr_file_type['type'];
//             if (in_array($uploaded_type, $supported_types)) {
//                 if (! $this->attachmentFormUpload($_FILES[ $this->key ])) {
//                     wp_die('Something went wrong on upload!');
//                 }
//             } else {
//                 throw new AttributeException("The file type that you've uploaded is not a PDF.");
//             }
//         } elseif (isset($_POST[ $this->inputKey ])) {
//             $this->setValue($_POST[ $this->inputKey ]);
//         }
//     }

//     public function validateValue($value): bool
//     {
//         return is_numeric($value);
//     }

//     /**
//      * @return true
//      */
//     public function attachmentUpload($filename, $type, $url): bool
//     {
//         include_once(constant('ABSPATH') . 'wp-admin/includes/image.php');

//         $attachment = array(
//             'post_mime_type' => $type,
//             'post_title'     => $this->post_id,
//             'post_content'   => '',
//             'post_status'    => 'inherit',
//             'guid'           => $url
//         );

//         $attachment_id = wp_insert_attachment($attachment, $url);

//         $attachment_data = wp_generate_attachment_metadata($attachment_id, $filename);

//         wp_update_attachment_metadata($attachment_id, $attachment_data);

//         /* UPDATE ATTACHMENT BELOW*/
//         $this->setValue($attachment_id);

//         return true;
//     }

//     /**
//      * @param \ArrayAccess|array $file
//      *
//      * @return bool
//      */
//     private function attachmentFormUpload($file = array()): bool
//     {
//         $file_return = wp_handle_upload($file, array( 'test_form' => false ));

//         if (isset($file_return['error']) || isset($file_return['upload_error_handler'])) {
//             return false;
//         } else {
//             return $this->attachmentUpload($file_return['file'], $file_return['type'], $file_return['url']);
//         }
//     }

//     /**
//      * @inheritDoc
//      *
//      * @return int
//      */
//     protected function getDefault()
//     {
//         return 0;
//     }

//     /**
//      * @inheritDoc
//      */
//     public function getDBSetup(): string
//     {
//         return 'TEXT'; // todo
//     }
// }
