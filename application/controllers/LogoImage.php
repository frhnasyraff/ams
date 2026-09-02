<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LogoImage extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in()) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {
        $image_data = $this->db->select('image_path')
            ->from('logo_images')
            ->get()
            ->row();

        // Check if an image path is returned
        if ($image_data) {
            $image_path = $image_data->image_path;
            // Access the image_path property
        } else {
            $image_path = '';
            // If no image is found, set a default empty string
        }

        $this->load->view('header', ['title' => 'Logo Image', 'title2' => 'Logo Image', 'styles' => [], 'image_path' => $image_path]);
        $this->load->view('logo-image', ['image_path' => $image_path]);
        $this->load->view('footer', ['scripts' => []]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the file is uploaded
            if (isset($_FILES['logoImage']) && $_FILES['logoImage']['error'] == 0) {

                // Check if a logo image already exists in the database
                $query = $this->db->get('logo_images');  // Fetch all rows from logo_images
                $existingLogo = $query->row_array();  // Fetch the first row as an array

                // Get the file details
                $fileTmpPath = $_FILES['logoImage']['tmp_name'];
                $fileName = $_FILES['logoImage']['name'];
                $fileSize = $_FILES['logoImage']['size'];
                $fileType = $_FILES['logoImage']['type'];

                // Define upload directory
                $uploadDir = 'uploads/images/';
                $uploadFilePath = $uploadDir . basename($fileName);

                // Validate file type (optional)
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($fileType, $allowedTypes)) {
                    echo 'Invalid file type. Only JPEG, PNG, and GIF are allowed.';
                    return;
                }

                // Move the uploaded file to the target directory
                if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {

                    // If an existing logo exists, update it, otherwise insert a new one
                    if ($existingLogo) {
                        // Delete the old image file from the server
                        if (file_exists($existingLogo['image_path'])) {
                            unlink($existingLogo['image_path']);
                        }

                        // Update the existing record with new image data
                        $data = [
                            'image_name' => $fileName,
                            'image_path' => $uploadFilePath
                        ];
                        $this->db->update('logo_images', $data);

                        // Check if update was successful
                        if ($this->db->affected_rows() > 0) {
                            // Update the session to reflect the new logo
                            $_SESSION['logo_image_path'] = $uploadFilePath;
                            redirect('/LogoImage/index?message=Logo updated successfully!');
                        } else {
                            echo 'Error updating the image in the database.';
                        }
                    } else {
                        // No existing logo, insert a new record
                        $data = [
                            'image_name' => $fileName,
                            'image_path' => $uploadFilePath
                        ];
                        $this->db->insert('logo_images', $data);

                        // Check if insert was successful
                        if ($this->db->affected_rows() > 0) {
                            // Update the session to reflect the new logo
                            $_SESSION['logo_image_path'] = $uploadFilePath;
                            redirect('/LogoImage/index?message=Logo added successfully!');
                        } else {
                            echo 'Error inserting data into the database.';
                        }
                    }
                } else {
                    echo 'Error uploading the file.';
                }
            } else {
                echo 'No file uploaded or there was an upload error.';
            }
        }
    }



    public function update()
    {
        if ($this->input->post('asset_type_color_id')) {
            $this->db->where('id', $this->input->post('asset_type_color_id'));
            $this->db->update('asset_type_color', ['color' => $this->input->post('asset_type_color_edit')]);
            die(redirect('/AssetTypesColors?message= Asset Types Color updated successfully!'));
        }
    }

    public function delete()
    {
        // Get the image path from the POST request
        $image_path = $this->input->post('image_path');

        // If the image path is not provided, redirect with an error
        if (empty($image_path)) {
            $this->session->set_flashdata('error', 'No image selected for deletion.');
            redirect('LogoImage/index');
            return;
        }

        // Define the full path to the image file
        $image_file = FCPATH . $image_path; // FCPATH is the root folder of the application

        // Check if the image file exists
        if (file_exists($image_file)) {
            // Try to delete the image file from the server
            if (unlink($image_file)) {
                // Image file deleted successfully, now delete the database record
                $this->db->where('image_path', $image_path);
                $this->db->delete('logo_images');
                $_SESSION['logo_image_path'] = '';
                redirect('/LogoImage/index?message=Logo Deleted successfully!');

                // Set success message

            } else {
                // If file couldn't be deleted, set an error message
                $this->session->set_flashdata('error', 'Failed to delete the image file.');
            }
        } else {
            // If the file doesn't exist, set an error message
            $this->session->set_flashdata('error', 'Image file not found.');
        }

        // Redirect to the images list page (or wherever appropriate)
        redirect('LogoImage'); // Assuming 'index' shows the list of images
    }
}
