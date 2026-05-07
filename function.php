<?php
// ══════════════════════════════════════════════════════
//  Fancybox Gallery — enqueue assets
// ══════════════════════════════════════════════════════

add_action('wp_enqueue_scripts', function () {

    wp_enqueue_script(
        'fancybox-js',
        'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.umd.js',
        [],
        null,
        true
    );

    wp_enqueue_style(
        'fancybox-style',
        'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css'
    );

    wp_enqueue_style(
        'fgal-style',
        get_template_directory_uri() . '/css/fancybox-gallery.css'
    );

    wp_enqueue_script(
        'fgal-navigate',
        get_template_directory_uri() . '/js/navigate.js',
        ['fancybox-js'],
        null,
        true
    );

});

// ══════════════════════════════════════════════════════
//  SHORTCODE [fgal_gallery]
// ══════════════════════════════════════════════════════

add_shortcode('fgal_gallery', function ($atts) {

    // ── Dependency check ────────────────────────
    if (!function_exists('get_field')) {
        return '<!-- fgal_gallery: ACF plugin not active -->';
    }

    // ── Post ID ─────────────────────────────────
    $post_id = get_the_ID();

    if (!$post_id) {
        return '<!-- fgal_gallery: no post ID found -->';
    }

    // ── Parameters ──────────────────────────────
    $atts = shortcode_atts([
        'field'   => 'gallery',
        'layout'  => 'grid',
        'columns' => '',
        'cols'    => '4',
    ], $atts, 'fgal_gallery');

    $field_name   = sanitize_key($atts['field']);
    $layout       = in_array($atts['layout'], ['grid', 'featured'], true) ? $atts['layout'] : 'grid';
    $columns      = (int) $atts['columns'];
    $cols_per_row = (int) $atts['cols'];

    $visible_count = ($columns > 0) ? $columns : (($layout === 'featured') ? 5 : 3);
    $group         = 'fgal-' . $post_id . '-' . $field_name;

    // ── Get & Validate Images ───────────────────
    $raw = get_field($field_name, $post_id);

    if (empty($raw) || !is_array($raw)) {
        return '<!-- fgal_gallery: field "' . esc_html($field_name) . '" is empty or not an array -->';
    }

    $images = array_values(array_filter(array_map(function ($img) {

        if (!is_array($img) || empty($img['url']) || (int) ($img['ID'] ?? 0) < 1) {
            return null;
        }

        $id = (int) $img['ID'];

        return [
            'id'      => $id,
            'url'     => $img['url'],
            'alt'     => $img['alt'] ?: ($img['caption'] ?: get_the_title($id)),
            'caption' => $img['caption'] ?? '',
        ];

    }, $raw)));

    if (empty($images)) {
        return '<!-- fgal_gallery: no valid image URLs found -->';
    }

    // ── Split Visible / Hidden ──────────────────
    $visible       = array_slice($images, 0, $visible_count);
    $hidden        = array_slice($images, $visible_count);
    $extra_desktop = (int) count($hidden);
    $extra_mobile  = (int) count($images) - 1;
    $last_index    = count($visible) - 1;

    // ── Custom Columns ──────────────────────────
    $actual_cols = ($columns > 0) ? min($columns, count($visible)) : 0;
    $gallery_id  = 'fgal-' . $post_id . '-' . $field_name;

    $col_attr = $actual_cols > 0
        ? '--fgal-cols:' . $actual_cols . '; --fgal-cols-row:' . $cols_per_row . ';'
        : '';

    ob_start(); ?>

    <div id="<?= esc_attr($gallery_id) ?>"
         class="fgal-gallery fgal-gallery--<?= esc_attr($layout) ?>"
         style="<?= esc_attr($col_attr) ?>">

        <?php foreach ($visible as $i => $img):
            $is_first = ($i === 0);
            $is_last  = ($i === $last_index);
            $classes  = $is_last ? 'fgal-gallery__last' : '';
            $full_url = wp_get_attachment_image_url($img['id'], 'full') ?: $img['url'];
        ?>

            <a  class="<?= esc_attr($classes) ?>"
                data-fancybox="<?= esc_attr($group) ?>"
                data-caption="<?= esc_attr($img['caption']) ?>"
                href="<?= esc_url($full_url) ?>">

                <?= wp_get_attachment_image(
                    $img['id'],
                    'large',
                    false,
                    [
                        'loading'  => $is_first ? 'eager' : 'lazy',
                        'decoding' => 'async',
                        'alt'      => $img['alt'],
                    ]
                ) ?>

                <?php if ($is_last && $extra_desktop > 0): ?>
                    <span class="fgal-btn fgal-btn--desktop">
                        Gallery <?= $extra_desktop ?> +
                    </span>
                <?php endif; ?>

                <?php if ($is_first && $extra_mobile > 0): ?>
                    <span class="fgal-btn fgal-btn--mobile">
                        Gallery <?= $extra_mobile ?> +
                    </span>
                <?php endif; ?>

            </a>

        <?php endforeach; ?>

        <?php foreach ($hidden as $img):
            $full_url = wp_get_attachment_image_url($img['id'], 'full') ?: $img['url'];
        ?>

            <a  data-fancybox="<?= esc_attr($group) ?>"
                data-caption="<?= esc_attr($img['caption']) ?>"
                href="<?= esc_url($full_url) ?>"
                aria-hidden="true"
                tabindex="-1"
                style="display:none;">
            </a>

        <?php endforeach; ?>

    </div>

    <?php return ob_get_clean();

});
