<?php

function enqueue_event_category_search_script($hook) {
    global $post_type;

    // Load script only on "Event" post edit/add page
    if ($post_type == 'event' && ($hook == 'post.php' || $hook == 'post-new.php')) {
        wp_enqueue_script('jquery'); // Ensure jQuery is loaded

        // Add inline JavaScript directly in functions.php
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function ($) {
                // Insert search box above the category list
                var searchBox = $("<input>", {
                    type: "text",
                    id: "event-category-search",
                    placeholder: "Search Categories...",
                    style: "width: 100%; margin-bottom: 10px; padding: 5px;"
                });

                $("#event-categorydiv .inside").prepend(searchBox);

                // Function to filter checkboxes
                $("#event-category-search").on("keyup", function () {
                    var searchTerm = $(this).val().toLowerCase();

                    $("#event-categorydiv .categorychecklist label").each(function () {
                        var categoryLabel = $(this).text().toLowerCase();

                        if (categoryLabel.includes(searchTerm)) {
                            $(this).closest("li").show();
                        } else {
                            $(this).closest("li").hide();
                        }
                    });
                });
            });
        ');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_event_category_search_script');
