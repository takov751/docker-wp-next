<?php

/*
Plugin Name:  Web & Roll - Custom functions
Plugin URI:   https://webandroll.co.uk
Description:  Custom functionality
Version:      1.0.0
Author:       Web & Roll
Author URI:   https://webandroll.co.uk
License:      MIT License
*/

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use function Env\env;

// Basic security, prevents file from being loaded directly.
defined('ABSPATH') or die('Cheatin&#8217; uh?');

/**
 * Don't use gutenberg on pages & jobs
 */
add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
    if ($post_type === 'page' || $post_type === 'job') {
        return false;
    }
    return $use_block_editor;
}, 10, 2);

/**
 * Register job post type
 */
add_action('init', function () {
    $labels = [
        'name' => 'Jobs',
        'singular_name' => 'Job',
        'add_new' => 'Add job',
        'add_new_item' => 'Add job',
        'edit_item' => 'Edit job',
        'new_item' => 'Job',
        'view_item' => 'View job',
        'search_items' => 'Search jobs',
        'not_found' => 'No jobs found',
        'not_found_in_trash' => 'No jobs found in Trash',
        'all_items' => 'All jobs',
        'menu_name' => 'Jobs',
        'name_admin_bar' => 'Jobs',
    ];

    $args = [
        'labels' => $labels,
        'description' => 'Jobs',
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'jobs'],
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'show_in_rest' => true,
        'show_in_graphql' => true,
        'graphql_single_name' => 'job',
        'graphql_plural_name' => 'jobs',
        'rest_base' => 'jobs',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    ];

    register_post_type('job', $args);
});

/**
 * Register case study post type
 */
add_action('init', function () {
    $labels = [
        'name' => 'Case studies',
        'singular_name' => 'Case study',
        'add_new' => 'Add case study',
        'add_new_item' => 'Add case study',
        'edit_item' => 'Edit case study',
        'new_item' => 'Case study',
        'view_item' => 'View case study',
        'search_items' => 'Search case studies',
        'not_found' => 'No case studies found',
        'not_found_in_trash' => 'No case studies found in Trash',
        'all_items' => 'All case studies',
        'menu_name' => 'Case studies',
        'name_admin_bar' => 'Case studies',
    ];

    $args = [
        'labels' => $labels,
        'description' => 'Case studies',
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'case-studies'],
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'show_in_rest' => true,
        'show_in_graphql' => true,
        'graphql_single_name' => 'caseStudy',
        'graphql_plural_name' => 'caseStudies',
        'rest_base' => 'case-studies',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    ];

    register_post_type('case-study', $args);
});

/**
 * Create jobs page if it doesn't exist
 */
add_action('init', function () {
    global $wpdb;
    if (null === $wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'careers'")) {
        $page = [
            'post_title'  => __('Careers'),
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type'   => 'page'
        ];
        wp_insert_post($page);
    }
});

/**
 * Create case studies page if it doesn't exist
 */
add_action('init', function () {
    global $wpdb;
    if (null === $wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'case-studies'")) {
        $page = [
            'post_title'  => __('Case studies'),
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type'   => 'page'
        ];
        wp_insert_post($page);
    }
});

/**
 * Add message into admin for job page
 */
add_filter('display_post_states', function ($post_states, $post) {
    if ($post->post_name == 'careers') {
        $post_states[] = 'Jobs page';
    }
    return $post_states;
}, 10, 2);

/**
 * Add message into admin for case studies page
 */
add_filter('display_post_states', function ($post_states, $post) {
    if ($post->post_name == 'case-studies') {
        $post_states[] = 'Case studies page';
    }
    return $post_states;
}, 10, 2);

/**
 * Add message next to sticky case studies. Sticky is set differently to the regular post type using a ACF field field_64389ef25d77f (boolean called sticky)
 */
add_filter('display_post_states', function ($post_states, $post) {
    if (get_field('sticky', $post->ID)) {
        $post_states[] = 'Sticky';
    }
    return $post_states;
}, 10, 2);

/**
 * Disable comments and pings everywhere
 */
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);

/**
 * Hide comments and pings everywhere
 */
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

/**
 * Remove comments and pings from admin bar
 */
add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
});

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action('after_setup_theme', function () {
    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'main_menu' => 'Main menu',
        'footer_menu' => 'Footer menu',
    ]);

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support('customize-selective-refresh-widgets');
}, 20);

/**
 * Replace excerpt [...] with ...
 */
add_filter('excerpt_more', function () {
    return '...';
});

/**
 * Rename posts to news
 */
add_action('init', function () {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'News';
    $labels->singular_name = 'Article';
    $labels->add_new = 'Add news article';
    $labels->add_new_item = 'Add news article';
    $labels->edit_item = 'Edit news article';
    $labels->new_item = 'News article';
    $labels->view_item = 'View news article';
    $labels->search_items = 'Search news articles';
    $labels->not_found = 'No news articles found';
    $labels->not_found_in_trash = 'No news articles found in Trash';
    $labels->all_items = 'All news articles';
    $labels->menu_name = 'News';
    $labels->name_admin_bar = 'News';
});

/**
 * Disable things
 */
add_action('admin_init', function () {
    // pages
    remove_post_type_support('page', 'editor');
    remove_post_type_support('page', 'excerpt');
    remove_post_type_support('page', 'discussion');
    remove_post_type_support('page', 'comments');
    remove_post_type_support('page', 'author');
    remove_post_type_support('page', 'trackbacks');
    remove_post_type_support('page', 'custom-fields');
    // remove_post_type_support('page', 'revisions');
    remove_post_type_support('page', 'post-formats');
    remove_post_type_support('page', 'thumbnail');
    remove_post_type_support('page', 'tags');

    // posts
    remove_post_type_support('post', 'discussion');
    remove_post_type_support('post', 'comments');
    remove_post_type_support('post', 'custom-fields');
    remove_post_type_support('post', 'post-formats');
    remove_post_type_support('post', 'trackbacks');
    remove_post_type_support('post', 'tags');
});

/**
 * Saves acf to json files
 */
add_filter('acf/settings/save_json', function ($path) {
    return dirname(__FILE__, 2) . '/acf';
});
add_filter('acf/settings/load_json', function ($paths) {
    unset($paths[0]);
    $paths[] = dirname(__FILE__, 2) . '/acf';
    return $paths;
});

/**
 * Hide ACF menu item from admin menu
 */
if (wp_get_environment_type() === 'production') {

    // Only allow fields to be edited on development
    add_filter('acf/settings/show_admin', '__return_false');
}

/**
 * Add metabox to dashboard listing the shortcodes that can be used
 */
add_action('wp_dashboard_setup', function () {
    wp_add_dashboard_widget(
        'shortcodes_dashboard_widget',
        'Shortcodes',
        function () {
            echo '<p>You can use the following shortcodes within wysiwyg editors:</p>';
            echo '<ul>';
            echo '<li>[request_callback_form]</li>';
            echo '<li>[contact_form]</li>';
            echo '</ul>';
        }
    );
});

/**
 * Hide the default metaboxes on the dashboard page except at a glance and activity
 */
add_action('wp_dashboard_setup', function () {
    global $wp_meta_boxes;
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health']);
    // at a glance
    //   unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    // recent activity
    // unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
    // quick draft
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    // events etc
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
});

/**
 * Add metabox to dashboard to contain the vercel deployment status and a button to redeploy
 */
add_action('wp_dashboard_setup', function () {
    wp_add_dashboard_widget(
        'vercel_dashboard_widget',
        'Vercel',
        function () {
?>
        <p>This website is a static site hosted on Vercel. When updating galleries, FAQs, case studies, news, jobs or pages, you won't need to do anything. However, if you create new pages, jobs, case studies, news, update any settings or alter the menu's, you will need to redeploy the site.</p>
        <p>This will take a few minutes as we need to regenerate everything, but you can check the status below.</p>
        <button id="getPreviousDeployments" class="button-primary">Get previous deployments</button>
        <div id="previousDeployments"></div>
        <button id="redeploy" class="button-primary" style="margin-top: 10px;">Redeploy</button>
        <div id="deploymentStatus" style="margin-top: 10px; padding: 10px; background: black; color: white; font-family: monospace; display: none; flex-direction: column-reverse; max-height: 500px; overflow: scroll;"></div>
        <script>
            async function getPreviousDeployments() {
                const req = new XMLHttpRequest();
                await req.open('GET', `https://api.vercel.com/v6/deployments?teamId=<?= env('VERCEL_TEAM_ID') ?>&state=READY&projectId=<?= env('VERCEL_PROJECT_ID') ?>&limit=10`, true);
                req.setRequestHeader('Authorization', `Bearer <?= env('VERCEL_API_TOKEN') ?>`);
                req.send();

                req.onreadystatechange = function() {
                    if (req.readyState === 4) {
                        if (req.status === 200) {
                            const result = JSON.parse(req.responseText);
                            const deployments = result.deployments;
                            let html = '<ul>';
                            for (let i = 0; i < deployments.length; i++) {
                                html += `<li>
                <strong>Created at: </strong>
                <span>${new Date(deployments[i].createdAt).toLocaleString()}</span>
                <br>
                <strong>URL: </strong>
                <a href="${deployments[i].url}" target="_blank">
                ${deployments[i].url}
                </a>
                </li>`;
                            }
                            html += '</ul>';
                            document.getElementById('previousDeployments').innerHTML = html;
                        } else {
                            console.log('error');
                        }
                    }
                }
            }

            async function redeploy() {
                const req2 = new XMLHttpRequest();
                await req2.open('POST', `https://api.vercel.com/v13/deployments?teamId=<?= env('VERCEL_TEAM_ID') ?>`, true);
                req2.setRequestHeader('Authorization', `Bearer <?= env('VERCEL_API_TOKEN') ?>`);
                req2.setRequestHeader('Content-Type', 'application/json');
                req2.send(JSON.stringify({
                    "name": "<?= env('VERCEL_PROJECT_ID') ?>",
                    "gitSource": {
                        type: "github",
                        ref: "master",
                        repoId: "<?= env('GITHUB_REPO_ID') ?>"
                    },
                    target: "production"
                }));

                req2.onreadystatechange = async function() {
                    if (req2.readyState === 4) {
                        if (req2.status === 200) {
                            const result = JSON.parse(req2.responseText);
                            document.getElementById('deploymentStatus').innerHTML = `<p><strong>Deployment status</strong>: ${result.status}</p>`;
                            document.getElementById('deploymentStatus').style.display = 'flex';

                            // watch for deployment events
                            const deploymentId = result.id;
                            let done = false;
                            while (!done) {
                                await new Promise(r => setTimeout(r, 1000));
                                const req3 = new XMLHttpRequest();
                                await req3.open('GET', `https://api.vercel.com/v2/deployments/${deploymentId}/events?teamId=<?= env('VERCEL_TEAM_ID') ?>`, true);
                                req3.setRequestHeader('Authorization', `Bearer <?= env('VERCEL_API_TOKEN') ?>`);
                                req3.setRequestHeader('Content-Type', 'application/json');
                                req3.send();

                                req3.onreadystatechange = function() {
                                    if (req3.readyState === 4) {
                                        if (req3.status === 200) {
                                            const result = JSON.parse(req3.responseText);
                                            result.forEach(event => {
                                                document.getElementById('deploymentStatus').innerHTML = `<p>${JSON.stringify(event.payload.text, null, 2)}</p>`;
                                            });
                                            done = result.some(
                                                event =>
                                                event.type === "deployment-state" &&
                                                event.payload.info.readyState === "READY"
                                            )
                                        } else {
                                            console.log('error');
                                            done = true;
                                        }
                                    }
                                    if (done) {
                                        document.getElementById('deploymentStatus').innerHTML = `<p><strong>Deployment completed</strong></p>`;
                                    }
                                }
                            }
                        } else {
                            console.log('error');
                        }
                    }
                }
            }

            document.getElementById('getPreviousDeployments').addEventListener('click', getPreviousDeployments);
            document.getElementById('redeploy').addEventListener('click', redeploy);
        </script>
    <?php
        }
    );
});

/**
 * For wpgraphql, need to trigger revalidation on post or page update, create or delete
 */
add_action('post_updated', function ($post_id, $post_after, $post_before) {
    if (wp_is_post_revision($post_id)) {
        return;
    }
    if ($post_after->post_type === 'post' || $post_after->post_type === 'page' || $post_after->post_type === 'job' || $post_after->post_type === 'case_study') {
        // get the post slugs
        $slug = $post_after->post_name;
        $before_slug = $post_before->post_name;

        // get page parent slug to construct the path
        $parent_slug = '';
        if ($post_after->post_parent) {
            $parent_slug = get_post($post_after->post_parent)->post_name . '/';
        }

        // if the slug has changed need to redeploy. Otherwise just revalidate
        if ($slug === $before_slug) {
            // ping the url
            $url = env('HEADLESS_MODE_CLIENT_URL') . "/api/isr-revalidate?secret=" . env('ISR_REVALIDATE_SECRET') . "&paths=/" . ($post_after->post_type === 'post' ? 'case-studies/' . $slug : $parent_slug . $slug);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
        }
    }
}, 10, 3);

/**
 * Trigger a revalidation of the pages and posts in the menu when a menu is updated
 *
 * TODO - SEE IF CAN CONVERT TO APP FOLDER & USE SERVER COMPONENTS
 */
// add_action('wp_update_nav_menu', function ($menu_id, $menu_data) {
//   // get all the menu items
//   $menu_items = wp_get_nav_menu_items($menu_id);

//   // get the menu items slugs
//   $slugs = array_map(function ($item) {
//     // remove the home url from the url
//     return $item->url = str_replace(get_home_url(), '', $item->url);
//   }, $menu_items);

//   // create a comma separated list of slugs
//   $slugs = implode(',', $slugs);

//   // ping the url
//   $url = HEADLESS_MODE_CLIENT_URL . "/api/isr-revalidate?secret=" . ISR_REVALIDATE_SECRET . "&paths=/" . $slugs;

//   $ch = curl_init();
//   curl_setopt($ch, CURLOPT_URL, $url);
//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
//   $headers = array();
//   $headers[] = 'Content-Type: application/json';
//   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//   $result = curl_exec($ch);
//   if (curl_errno($ch)) {
//     echo 'Error:' . curl_error($ch);
//   }
//   curl_close($ch);
// }, 10, 2);

/**
 * For ACF field with field_id - field_63fe7e6e8cbb8
 * Need to display a title tag length character counter with js
 */
add_action('acf/render_field/key=field_63fe77cfcb59f', function ($field) {
    ?>
    <div class="title-tag-counter" style="margin-top:5px">
        <span class="title-tag-counter__count">0</span>
        <span class="title-tag-counter__max">/ 60</span>

        <script>
            const titleTagCounter = document.querySelector('.title-tag-counter');
            const titleTagCount = document.querySelector('.title-tag-counter__count');
            const titleTagInput = document.querySelector('#acf-field_63fe77cfcb59f');
            titleTagInput.addEventListener('input', function(e) {
                titleTagCount.innerHTML = e.target.value.length;
                if (e.target.value.length < 50 || e.target.value.length > 60) {
                    titleTagCounter.style.color = 'red';
                } else {
                    titleTagCounter.style.color = 'green';
                }
            });

            // also populate the counter on page load
            titleTagCount.innerHTML = titleTagInput.value.length;
        </script>
    </div>
<?php
});

/**
 * Do the same for the meta desc - field_63fe77e0cb5a0
 */
add_action('acf/render_field/key=field_63fe77e0cb5a0', function ($field) {
?>
    <div class="meta-desc-counter" style="margin-top:5px">
        <span class="meta-desc-counter__count">0</span>
        <span class="meta-desc-counter__max">/ 160</span>

        <script>
            const metaDescCounter = document.querySelector('.meta-desc-counter');
            const metaDescCount = document.querySelector('.meta-desc-counter__count');
            const metaDescInput = document.querySelector('#acf-field_63fe77e0cb5a0');
            metaDescInput.addEventListener('input', function(e) {
                metaDescCount.innerHTML = e.target.value.length;
                if (e.target.value.length < 150 || e.target.value.length > 160) {
                    metaDescCounter.style.color = 'red';
                } else {
                    metaDescCounter.style.color = 'green';
                }
            });

            // also populate the counter on page load
            metaDescCount.innerHTML = metaDescInput.value.length;
        </script>
    </div>
<?php
});

/**
 * Provide default colours to ACF colour picker
 */
add_action('acf/input/admin_footer', function () {
?>
    <script>
        (function($) {
            acf.add_filter('color_picker_args', function(args, $field) {
                args.palettes = ['#2a255b', '#8cd503', '#212121', '#ffffff', '#f8fafc', '#f1f5f9', '#e2e8f0', '#cbd5e1', '#94a3b8', '#64748b', '#475569', '#334155', '#1e293b', '#0f172a'];
                return args;
            });
        })(jQuery);
    </script>
<?php
});

/**
 * Have to group clone fields to apply conditional logic in ACF. This in turn makes it unavailable to graphql
 * So just do it with a bit of JS!
 *
 * If (.acf-field-63fe846fd48e2 .acf-checkbox-list input[value="button"]) is checked, show the button fields (.acf-field-63fe791b9e624)
 */
add_action('acf/input/admin_footer', function () {
?>
    <script>
        const acfFields = document.querySelectorAll('.acf-fields');

        if (acfFields) {
            acfFields.forEach(function(acfField) {
                const checkbox = acfField.querySelector('.acf-field-63fe846fd48e2 .acf-checkbox-list input[value="button"]');
                const buttons = acfField.querySelector('.acf-field-63fe791b9e624');

                if (checkbox && buttons) {
                    // set on initial load
                    if (checkbox.checked) {
                        buttons.style.display = 'block';
                    } else {
                        buttons.style.display = 'none';
                    }

                    // then listen for changes
                    checkbox.addEventListener('change', function(e) {
                        if (e.target.checked) {
                            buttons.style.display = 'block';
                        } else {
                            buttons.style.display = 'none';
                        }
                    });
                }
            });
        }
    </script>
<?php
});

/**
 * Add ACF options pages
 */
add_action('acf/init', function () {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title' => 'Galleries',
            'menu_title' => 'Galleries',
            'menu_slug' => 'galleries',
            'capability' => 'edit_posts',
            'redirect' => false,
            'position' => 2,
            'icon_url' => 'dashicons-format-gallery',
            'show_in_graphql' => true,
        ]);

        acf_add_options_page([
            'page_title' => 'FAQs',
            'menu_title' => 'FAQs',
            'menu_slug' => 'faqs',
            'capability' => 'edit_posts',
            'redirect' => false,
            'position' => 3,
            'icon_url' => 'dashicons-testimonial',
            'show_in_graphql' => true,
        ]);

        acf_add_options_sub_page([
            'page_title' => 'Business Information',
            'menu_title' => 'Business Information',
            'parent_slug' => 'options-general.php',
            'show_in_graphql' => true,
        ]);
    }
});

/**
 * Pass faqs from repeater field field_6406b23f86df0 to field_63feec0b86c48
 */
add_filter('acf/load_field/key=field_63feec0b86c48', function ($field) {
    $field['choices'] = [];
    $faqs = get_field('field_6406b23f86df0', 'option');

    if (!empty($faqs)) {
        $i = 1;
        foreach ($faqs as $faq) {
            $field['choices'][$i] = $faq['question'];
            $i++;
        }
    }
    return $field;
});

/**
 * Populate the predefined section selections
 *
 * Get all child pages with a page_summary_image (field_640719a6159e5) and a page_summary_short_description (field_640719c0159e6)
 * Get the parent page and provide new options to a select field called predefined (field_63fe84ddd48e4)
 * Name the select field by the parent page title and append "ChildPageSummaries"
 */
add_filter('acf/load_field/key=field_63fe84ddd48e4', function ($field) {
    $field['choices'] = [];

    // add default choices
    $field['choices']['googleReviews'] = 'Google Reviews';
    $field['choices']['recentCaseStudies'] = 'Recent Case Studies';
    $field['choices']['galleries'] = 'Galleries';
    $field['choices']['contactPage'] = 'Contact Page';

    $pages = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => 'page_summary_image',
                'compare' => 'EXISTS',
            ],
            [
                'key' => 'page_summary_short_description',
                'compare' => 'EXISTS',
            ],
        ],
    ]);

    if (!empty($pages)) {
        $parentPages = [];
        foreach ($pages as $page) {
            if ($page->post_parent === 0) continue;
            $parentPages[$page->post_parent][] = $page;
        }

        foreach ($parentPages as $parentPageId => $childPage) {
            $parentPage = get_post($parentPageId);
            $field['choices']['childPageSummary-slider-' . $parentPageId] = $parentPage->post_title . ' child page summaries - Slider';
            $field['choices']['childPageSummary-grid-' . $parentPageId] = $parentPage->post_title . ' child page summaries - Grid';
        }
    }

    return $field;
});

/**
 * When saving the ACF galleries option, save some post meta:
 * - gallery_titles - Used in graphql to query media items in a gallery so we can use the pagination stuff from wpgraphql
 * - gallery_show_ons - CURRENTLY NOT USED FOR ANYTHING as I'm querying the show ons from the gallery itself in graphql
 * - gallery_orders - Save the order of each image in each gallery like [gallery_title => order] on the image meta
 *
 * field_63ff34c95bfc2 - flexible content wrapper
 */
add_action('acf/save_post', function ($post_id) {
    if ($post_id !== 'options') return;

    foreach ($_POST['acf'] as $galleriesKey => $galleries) {
        if ('field_63ff34c95bfc2' === $galleriesKey && is_array($galleries)) {

            // first clear out the gallery_titles, gallery_show_ons and gallery_orders from all media items
            $mediaItems = get_posts([
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'posts_per_page' => -1,
            ]);

            foreach ($mediaItems as $mediaItem) {
                delete_post_meta($mediaItem->ID, 'acf_gallery_titles');
                delete_post_meta($mediaItem->ID, 'acf_gallery_show_ons');

                // delete all post meta that starts with acf_gallery_orders_
                global $wpdb;
                $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s", $mediaItem->ID, 'acf_gallery_order_%'));
            }

            foreach ($galleries as $gallery) {
                /**
                 * Now inside individual gallery rows we have the title, array of showOn ID's (the page urls where the gallery should be shown on), and the image ID's
                 *
                 * field_63ff34e85bfc3 - gallery title
                 * field_63fffccdd9b0b - array of gallery showOn's
                 * field_63ff35025bfc4 - array of gallery images
                 */
                $galleryTitle = $gallery['field_63ff34e85bfc3'];
                $galleryShowOn = $gallery['field_63fffccdd9b0b'];
                $galleryImages = $gallery['field_63ff35025bfc4'];

                foreach ($galleryImages as $imageIndex => $imageId) {
                    $galleryTitles = get_post_meta($imageId, 'acf_gallery_titles', true);
                    $galleryShowOns = get_post_meta($imageId, 'acf_gallery_show_ons', true);

                    if (is_array($galleryTitles)) {
                        $galleryTitles[] = $galleryTitle;
                    } else {
                        $galleryTitles = [$galleryTitle];
                    }

                    if (is_array($galleryShowOns)) {
                        $galleryShowOns[] = $galleryShowOn;
                    } else {
                        $galleryShowOns = [$galleryShowOn];
                    }

                    update_post_meta($imageId, 'acf_gallery_titles', $galleryTitles);
                    update_post_meta($imageId, 'acf_gallery_show_ons', $galleryShowOns);
                    update_post_meta($imageId, 'acf_gallery_order_' . sanitize_title($galleryTitle), $imageIndex);
                }
            }
        }
    }
}, 20);

/**
 * Register the placeholderDataURI field
 */
add_action('graphql_register_types', function () {
    register_graphql_field('MediaItem', 'placeholderDataURI', [
        'description' => __('Encoded base64 placeholder', 'default'),
        'type' => 'String',
        'resolve' => function (\WPGraphQL\Model\Post $source, $args, AppContext $context, ResolveInfo $info) {
            $file_local_abs_path = wp_get_attachment_image_url($source->databaseId);

            if (!isset($file_local_abs_path)) {
                return null;
            }

            $file_relative_path = parse_url($file_local_abs_path)['path'];
            $file_path = get_site_url() . $file_relative_path;
            $mime_type = pathinfo($file_path, PATHINFO_EXTENSION);
            $image = new Imagick($file_path);
            $image->resizeImage(50, 50, Imagick::FILTER_CATROM, 1);
            $image->blurImage(100, 20);
            $image->stripImage();

            return 'data:image/' . $mime_type . ';base64,' . base64_encode($image);
        }
    ]);
});

/**
 * Sticky posts
 *
 * Based on https://www.wpgraphql.com/2018/11/16/querying-sticky-posts-with-graphql - Except just registering 1 field where I can get either sticky or not sticky posts
 */
add_action('graphql_register_types', function () {
    register_graphql_field('RootQueryToPostConnectionWhereArgs', 'sticky', [
        'type' => 'Boolean',
        'description' => __('Whether to only include sticky posts', 'default'),
    ]);

    register_graphql_field('RootQueryToCaseStudyConnectionWhereArgs', 'sticky', [
        'type' => 'Boolean',
        'description' => __('Whether to only include sticky case studies', 'default'),
    ]);
});

add_filter('graphql_post_object_connection_query_args', function ($query_args, $source, $args, $context, $info) {
    if (isset($args['where']['sticky'])) {
        $sticky_ids = [];
        if (in_array('post', $query_args['post_type'])) {
            $sticky_ids = get_option('sticky_posts');
        } else if (in_array('case-study', $query_args['post_type'])) {
            $caseStudies = get_posts([
                'post_type' => 'case-study',
                'posts_per_page' => -1
            ]);
            foreach ($caseStudies as $caseStudy) {
                if (get_field('sticky', $caseStudy->ID)) {
                    $sticky_ids[] = $caseStudy->ID;
                }
            }
        }
        if ($args['where']['sticky']) {
            $query_args['posts_per_page'] = count($sticky_ids);
            $query_args['post__in'] = $sticky_ids;
        } else {
            $query_args['post__not_in'] = $sticky_ids;
        }
    }
    return $query_args;
}, 10, 5);

/**
 * Allow to filter by gallery title when querying media items
 */
add_action('graphql_register_types', function () {
    register_graphql_field('RootQueryToMediaItemConnectionWhereArgs', 'galleryTitle', [
        'type' => 'String',
        'description' => __('The gallery title to filter by', 'wp-graphql')
    ]);
});

// note to self - was thinking it would be something like graphql_media_item_object_connection_query_args but after looking on https://www.wpgraphql.com/filters, there isn't a filter for it... Using the post_object syntax works for this as expected. This article (https://stackoverflow.com/questions/65461514/how-to-add-custom-filters-on-graphql-wp-advanced-custom-fields), helped me figure it out!
add_filter('graphql_post_object_connection_query_args', function ($query_args, $source, $args, $context, $info) {
    if (isset($args['where']['galleryTitle'])) {
        $query_args['meta_query'] = [
            [
                'key' => 'acf_gallery_titles',
                'value' => $args['where']['galleryTitle'],
                'compare' => 'LIKE'
            ]
        ];
    }
    return $query_args;
}, 10, 5);

/**
 * Add a new order by enum to order by ACF gallery order
 *
 * @see https://gist.github.com/jasonbahl/da87dbccb58f1323a324a9b3e8952d6c
 * @see https://github.com/wp-graphql/wp-graphql-acf/issues/28#issuecomment-590569363
 */
add_filter('graphql_PostObjectsConnectionOrderbyEnum_values', function ($values) {
    $values['ACF_GALLERY_ORDER'] = [
        'value' => 'acf_gallery_order',
        'description' => __('Order by the ACF gallery order', 'wp-graphql')
    ];
    return $values;
});

add_filter('graphql_post_object_connection_query_args', function ($query_args, $source, $input) {
    if (isset($input['where']['orderby']) && is_array($input['where']['orderby']) && !empty($input['where']['galleryTitle'])) {

        foreach ($input['where']['orderby'] as $orderby) {
            if (!isset($orderby['field']) || 'acf_gallery_order' !== $orderby['field']) {
                continue;
            }

            $query_args['meta_type'] = 'NUMERIC';
            $query_args['meta_key'] = 'acf_gallery_order_' . sanitize_title($input['where']['galleryTitle']);
            $query_args['orderby']['meta_value_num'] = $orderby['order'];
        }
    }

    return $query_args;
}, 10, 3);
