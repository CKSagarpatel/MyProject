<?php 
/*
    Template Name: Import Excel File
*/

get_header();


// Import CSV
// if(isset($_POST['butimport'])){

//     $file = $_FILES['import_file'];

//     // Check for allowed file types and handle the uploaded file
//     $upload_dir = wp_upload_dir(); // Get the WordPress uploads directory path
//     $csv_file = trailingslashit($upload_dir['path']) . basename($file['name']);

//     // echo '<pre>';
//     // print_r($target_file);
//     // echo '</pre>';

//     // Set the path to your CSV file
//     //$csv_file = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

//     // $target_file = pathinfo($_FILES['import_file']['name']);

//     // echo '<pre>';
//     // print_r($csv_file);
//     // echo '</pre>';

//     //fgetcsv($csv_file);

//     //$csv_file = 'F:\Plugin\PaulSquash22.csv';
    

//     // Get the CSV data
//     $csv_data = array_map( 'str_getcsv', file( $csv_file ) );
  
//     // Loop through the rows and create custom posts
//     foreach ( $csv_data as $row ) {
//         // Modify this to match your custom post type slug
//         $post_type = 'squash';

//         // Extract data from the CSV columns
//         $date = $row[0]; // Assuming the second column contains the post content
//         $title = $row[2]; // Assuming the first column contains the post title
//         $content = $row[1]; // Assuming the second column contains the post content
//         $time = $row[3]; // Assuming the second column contains the post content

//         $existing_post = get_page_by_title($title, OBJECT, 'squash');
//         // echo '<pre>';
//         // print_r($existing_post);
//         // echo '</pre>';
//         // die;

//         if ($existing_post) {
//             $post_id = $existing_post->ID;

//             // Update post content
//             $post_data = array(
//                 'ID'           => $post_id,
//                 'post_title'  => $title,
//             );
//             wp_update_post($post_data);

//             // Update custom fields
//             update_post_meta($post_id, 'date', $date);
//             update_post_meta($post_id, 'match_venue', $content);
//             update_post_meta($post_id, 'match_manager', $title);
//             update_post_meta($post_id, 'time', $time);

//         } else{
            
//             // Create custom post
//             $new_post_data = 
//             array(
//                 'post_type'   => $post_type,
//                 'post_title'  => $title,
//                 'post_content' => $content,
//                 'post_status' => 'publish',
//             );

//             $new_post_id = wp_insert_post($new_post_data);

//             // Set custom fields for the new post
//             add_post_meta($new_post_id, 'date', $date);
//             add_post_meta($new_post_id, 'match_venue', $content);
//             add_post_meta($new_post_id, 'match_manager', $title);
//             add_post_meta($new_post_id, 'time', $time);
//         }

//     }



//     // foreach ( $csv_data as $row ) {
//     //     $existing_post = get_page_by_title($data['title'], OBJECT, 'squash');

//     //     // Modify this to match your custom post type slug
//     //     $post_type = 'squash';

//     //     // Extract data from the CSV columns
//     //     $title = $row[2]; 

       
//     //     if ($existing_post) {
//     //         // Post already exists, update it
//     //         $post_id = $existing_post->ID;
    
//     //         // Update custom fields
//     //         update_post_meta($post_id, 'date', $data['date']);
//     //         update_post_meta($post_id, 'match_venue', $data['match_venue']);
//     //         update_post_meta($post_id, 'match_manager', $data['match_manager']);
//     //         update_post_meta($post_id, 'time', $data['time']);
    
//     //         // Continue updating other fields as needed
//     //     }
//     //     else {

//     //         // Post doesn't exist, create a new post
//     //         $new_post_data = array(
//     //             'post_title'   => $title,
//     //             'post_type'    => $post_type,
//     //             'post_status'  => 'publish',
//     //         );
//     //         $new_post_id = wp_insert_post($new_post_data);

//     //         // Set custom fields for the new post
//     //         add_post_meta($new_post_id, 'date', $data['date']);
//     //         add_post_meta($new_post_id, 'match_venue', $data['match_venue']);
//     //         add_post_meta($new_post_id, 'match_manager', $data['match_manager']);
//     //         add_post_meta($new_post_id, 'time', $data['time']);
//     //     }

//     // }





// }



if (isset($_POST['submit'])) {
    if ($_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        if (($handle = fopen($file, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $post_title = sanitize_text_field($data[1]);
                $post_content = wp_kses_post($data[2]);
                $time = sanitize_text_field($data[3]);
                $date_value = sanitize_text_field($data[0]);
                $existing_post_id = post_exists( $post_title, "", "", "");

                //$existing_post = get_page_by_title($post_title, OBJECT, 'squash');
                //$existing_post = get_page_by_title($data[2]);
                //$existing_post_id = post_exists($post_content);
                //$existing_post_id = post_exists($post_title, '', '', 'squash');
                if ($existing_post_id) {

                    $my_post = array(
                        'ID'           => $existing_post_id,
                        'post_title' => $post_title,
                        'post_content' => $post_content,
                        'post_type' => 'squash',
                        'post_status' => 'publish',
                    );
                  
                  // Update the post into the database
                    wp_update_post( $my_post );
                    update_post_meta($existing_post_id, 'date', $date_value);
                    update_post_meta($existing_post_id, 'match_venue', $post_content);
                    update_post_meta($existing_post_id, 'match_manager', $post_title);
                    update_post_meta($existing_post_id, 'time', $time);
                    
                } else{
                  
                
                    $post_id = wp_insert_post(array(
                        'post_title' => $post_title,
                        'post_content' => $post_content,
                        'post_type' => 'squash',
                        'post_status' => 'publish',
                    ));

                    update_post_meta($post_id, 'date', $date_value);
                    update_post_meta($post_id, 'match_venue', $post_content);
                    update_post_meta($post_id, 'match_manager', $post_title);
                    update_post_meta($post_id, 'time', $time);
                }
            }
            fclose($handle);
        }
    }
}
?>
<!-- Form -->
<form method="post" enctype="multipart/form-data">
    <input type="file" name="csv_file" accept=".csv">
    <input type="submit" name="submit" value="Upload and Import">
</form>
<?php get_footer(); ?>