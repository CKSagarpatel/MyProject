<?php 
/*
    Template Name: Import Excel File
*/

get_header();



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

                $cat = $data[4];
                $category_names = explode(',', $cat);
                
                $existing_category = get_term_by('name', $cat, 'squash-category');
                if (!$existing_category) {
                    // Create the category if it doesn't exist
                    wp_insert_term($cat, 'squash-category');
                }

                $category_ids = array();

                foreach ($category_names as $category_name) {
                    $category = get_term_by('name', trim($category_name), 'squash-category');
                    if ($category) {
                        $category_ids[] = $category->term_id;
                    }
                }                


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

                    //wp_insert_term($category_name, 'squash-category');
                    
                    // Assign the retrieved category IDs to the post                    
                    update_post_meta($existing_post_id, 'date', $date_value);
                    update_post_meta($existing_post_id, 'match_venue', $post_content);
                    update_post_meta($existing_post_id, 'match_manager', $post_title);
                    update_post_meta($existing_post_id, 'time', $time);
                    
                } else{
                  
                
                    $existing_post_id = wp_insert_post(array(
                        'post_title' => $post_title,
                        'post_content' => $post_content,
                        'post_type' => 'squash',
                        'post_status' => 'publish',
                    ));

                    //wp_insert_term($category_name, 'squash-category');

                    update_post_meta($existing_post_id, 'date', $date_value);
                    update_post_meta($existing_post_id, 'match_venue', $post_content);
                    update_post_meta($existing_post_id, 'match_manager', $post_title);
                    update_post_meta($existing_post_id, 'time', $time);
                }

                // Assign the retrieved category IDs to the post
                if (!empty($category_ids)) {
                    //wp_set_post_categories($existing_post_id, $category_ids);
                    wp_set_post_terms($existing_post_id, $category_ids, 'squash-category');
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