<?php
/**
 * Plugin Name: Hale Drag Race Deploy
 * Description: Minimal REST endpoint to update the static /drag-race/ page. Writes ONLY drag-race/index.html — no other path, no code execution. Requires an administrator application password.
 * Version: 1.0
 * Author: Cameron Ehrlich (Thirty Seven, Inc.)
 */

if (!defined('ABSPATH')) exit;

add_action('rest_api_init', function () {
    register_rest_route('hale/v1', '/drag-race-deploy', array(
        'methods'  => 'POST',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
        'callback' => function (WP_REST_Request $req) {
            // Hardcoded target — this endpoint can write exactly one file, ever.
            $dir  = ABSPATH . 'drag-race';
            $file = $dir . '/index.html';

            $html = $req->get_body();
            if (strlen($html) < 1000 || strlen($html) > 2 * 1024 * 1024) {
                return new WP_Error('bad_size', 'Payload must be between 1KB and 2MB.', array('status' => 400));
            }
            // Sanity check: must look like our page, not arbitrary content.
            if (stripos($html, '<!DOCTYPE html') === false || stripos($html, '</html>') === false) {
                return new WP_Error('bad_content', 'Payload does not look like a complete HTML document.', array('status' => 400));
            }
            if (!is_dir($dir) && !wp_mkdir_p($dir)) {
                return new WP_Error('mkdir_failed', 'Could not create drag-race directory.', array('status' => 500));
            }
            // Keep one backup of the previous version.
            if (file_exists($file)) {
                copy($file, $dir . '/index.html.bak');
            }
            $written = file_put_contents($file, $html);
            if ($written === false) {
                return new WP_Error('write_failed', 'Could not write index.html.', array('status' => 500));
            }
            return array(
                'ok'      => true,
                'bytes'   => $written,
                'sha256'  => hash('sha256', $html),
                'backup'  => file_exists($dir . '/index.html.bak'),
            );
        },
    ));
});
