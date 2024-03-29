<?php
/*
Plugin Name: Auto Parts Plugin
Description: A WordPress plugin for managing auto parts.
Version: 1.0
Author: Rayhan Marrero, Alfredo Medina, Luis Delgado
*/

// Register REST API endpoint
add_action( 'rest_api_init', 'car_parts_search_register_rest_endpoint' );

function car_parts_search_register_rest_endpoint() {
    register_rest_route( 'car-parts-search/v1', '/search', array(
        'methods'  => 'GET',
        'callback' => 'car_parts_search_api_callback',
    ) );
}

function car_parts_search_api_callback( $request ) {
    // Retrieve search parameters from request
    $query = $request->get_param( 'query' );

    // Validate and sanitize input
    $query = sanitize_text_field( $query );

    // Connect to the database (replace with your database credentials)
    $host = 'localhost';
    $username = 'your_database_username';
    $password = 'your_database_password';
    $dbname = 'your_database_name';

    $conn = new mysqli( $host, $username, $password, $dbname );

    // Check connection
    if ( $conn->connect_error ) {
        return new WP_Error( 'database_error', 'Database connection failed: ' . $conn->connect_error, array( 'status' => 500 ) );
    }

    // Perform database query
    $sql = "SELECT * FROM car_parts WHERE part_name LIKE '%$query%' OR part_number LIKE '%$query%'";
    $result = $conn->query( $sql );

    // Check if query was successful
    if ( $result === false ) {
        return new WP_Error( 'database_error', 'Database query failed: ' . $conn->error, array( 'status' => 500 ) );
    }

    // Fetch and format results
    $parts = array();
    while ( $row = $result->fetch_assoc() ) {
        $parts[] = array(
            'part_name'     => $row['part_name'],
            'part_number'   => $row['part_number'],
            'manufacturer'  => $row['manufacturer'],
            // Add more fields as needed
        );
    }

    // Close database connection
    $conn->close();

    // Return search results
    return rest_ensure_response( $parts );
}

?>

<!-- Outline for our plugin code! -->