<?php

class AB_Nixwood {
    private $config = '{"title":"Banner settings","prefix":"banners_options_","domain":"nixwood","class_name":"AB_Nixwood","context":"normal","priority":"high","cpt":"abn_banners","fields":[{"type":"select","label":"Banner type","options":"plain: Plain\r\npopup:Popup","id":"advanced_options_is-popup"},{"type":"url","label":"Banner link","id":"banners_options_banner-link"},{"type":"select","label":"Open link in","options":"_parent: Same window\r\n_blank:New window","id":"advanced_options_link-target"},{"type":"number","label":"Popup display after N seconds","id":"banners_options_popup-display-secs"},{"type":"number","label":"Popup hide after clothing, days","id":"banners_options_popup-hide-days"},{"type":"number","label":"Add banner after N paragraph","id":"banners_options_add-banner-after-n-paragraph"}, {"type":"checkbox","label":"Enable advanced selector","id":"advanced_options_adv-selector"}, {"type":"text","label":"Selector classes or ids, comma-separated","id":"banners_options_adv-selector-id"}, {"type":"text","label":"Exclude posts by id(comma-separated)","id":"banners_options_exclude_posts"}, {"type":"textarea","label":"Banner block css ","id":"banners_options_bcss"},{"type":"select","label":"Display banner logic","options":"all: All posts\r\nchoose:Choose posts","id":"banners_options_display-banner-logic"},{"type":"hidden","label":"", "id":"banners_options_pid_pages"}, {"type":"hidden","label":"", "id":"banners_options_pids"}]}';

    public function __construct() {
        $this->config = json_decode( $this->config, true );
        $this->process_cpts();
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post', [ $this, 'save_post' ] );
    }

    public function process_cpts() {
        if ( !empty( $this->config['cpt'] ) ) {
            if ( empty( $this->config['post-type'] ) ) {
                $this->config['post-type'] = [];
            }
            $parts = explode( ',', $this->config['cpt'] );
            $parts = array_map( 'trim', $parts );
            $this->config['post-type'] = array_merge( $this->config['post-type'], $parts );
        }
    }

    public function add_meta_boxes() {
        foreach ( $this->config['post-type'] as $screen ) {
            add_meta_box(
                sanitize_title( $this->config['title'] ),
                $this->config['title'],
                [ $this, 'add_meta_box_callback' ],
                $screen,
                $this->config['context'],
                $this->config['priority']
            );
        }
    }

    public function save_post( $post_id ) {
        foreach ( $this->config['fields'] as $field ) {
            switch ( $field['type'] ) {
                case 'url':
                    if ( isset( $_POST[ $field['id'] ] ) ) {
                        $sanitized = esc_url_raw( $_POST[ $field['id'] ] );
                        update_post_meta( $post_id, $field['id'], $sanitized );
                    }
                    break;

                case 'checkbox':
                    update_post_meta( $post_id, $field['id'], isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : '' );
                    break;

                default:
                    if ( isset( $_POST[ $field['id'] ] ) ) {
                        $sanitized = sanitize_text_field( $_POST[ $field['id'] ] );
                        update_post_meta( $post_id, $field['id'], $sanitized );
                    }
            }
        }

    }

    public function add_meta_box_callback() {
        $this->fields_table();
    }

    private function fields_table() {
        ?>
        <div class="banner_settings">
            <table class="form-table  banner-stable" role="presentation">
                <tbody><?php
                foreach ( $this->config['fields'] as $field ) {

                    ?><tr class="tr-<?php echo $field['id']; ?>">
                    <th scope="row"><?php $this->label( $field ); ?></th>
                    <td><?php $this->field( $field ); ?></td>
                    </tr><?php
                }

                $abn_posts_select_args = [
                    'post_type' => 'post',
                    'orderby' => 'date ',
                    'post_status' => 'publish',
                    'order' => 'DESC',
                    'posts_per_page' => -1,
                ];

                $abn_posts_select_query = new WP_Query($abn_posts_select_args);

                $abn_pages_select_args = [
                    'post_type' => 'page',
                    'orderby' => 'date ',
                    'post_status' => 'publish',
                    'order' => 'DESC',
                    'posts_per_page' => -1,
                ];

                $abn_pages_select_query = new WP_Query($abn_pages_select_args);

                ?>
                <tr><th scope="row"><label class="abn_posts_select" style="display:none;"><?php _e('Choose posts', 'nixwood'); ?></label></th>
                    <td>
                        <div style="overflow-y: scroll; max-height:400px;padding:0 10px 0 0;display:none;margin-bottom:50px;" class="abn_posts_select" <?php if(get_post_meta( get_the_ID(), 'banners_options_display-banner-logic', true ) == "choose") {} else { echo 'style="display:none"';} ?>>
                            <?php

                            $pmeta_ids = get_post_meta( get_the_ID(), 'banners_options_pids', true );
                            if($pmeta_ids) {
                                $post_ids = explode(",", $pmeta_ids);
                                $post_ids_input_value = implode(",", $post_ids);
                            } else {
                                $post_ids = [];
                                $post_ids_input_value = '';
                            }
                            foreach($abn_posts_select_query->posts as $select_post) {

                                if(in_array($select_post->ID, $post_ids)) {
                                    $nowActive = 'nowActive';
                                } else {
                                    $nowActive = '';
                                }

                                echo '<div data-id="'.$select_post->ID.'" class="select_post_single '.$nowActive.'">'.$select_post->post_title.'</div>';
                            }

                            ?>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>
        <input type='hidden' name='banners_options_pids' id='banners_options_pids' class='banners_options_pid' value=''>
        <style>
            #wpfooter {display:none;}
            .select_post_single {padding: 4px 4px 4px 10px; margin:10px 0; display:block; border:2px solid grey;cursor:pointer;}
            .nowActive {border:2px solid green !important; color: green !important;padding-left: 30px !important; background: url(<?php
echo AB_NIXWOOD . 'assets/img/checked.png'?>) no-repeat 5px; background-size:20px;}

        </style>


        <table class="form-table" style="margin-top: -50px;">
            <tbody>
            <tr><th scope="row"><label class=""><?php _e('Choose pages', 'nixwood'); ?></label></th>
                <td>
                    <div style="overflow-y: scroll; max-height:400px;padding:0 10px 0 0;">
                        <?php

                        $page_meta_ids = get_post_meta( get_the_ID(), 'banners_options_pid_pages', true );
                        if($page_meta_ids) {
                            $page_ids = explode(",", $page_meta_ids);
                            $page_ids_input_value = implode(",", $page_ids);
                        } else {
                            $page_ids = [];
                            $page_ids_input_value = '';
                        }
                        foreach($abn_pages_select_query->posts as $select_page) {

                            if(in_array($select_page->ID, $page_ids)) {
                                $nowActive = 'nowActive';
                            } else {
                                $nowActive = '';
                            }

                            echo '<div data-id="'.$select_page->ID.'" class="select_page_single '.$nowActive.'">'.$select_page->post_title.'</div>';
                        }

                        ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <input type='hidden' name='banners_options_pid_pages' id='banners_options_pid_pages' class='banners_options_pid_pages' value=''>

        <script>
            jQuery(document).ready( function() {

                jQuery("#advanced_options_adv-selector").click(function() {
                    if(jQuery(this).attr("checked") === 'checked') {
                        jQuery(this).attr("checked", false);
                        jQuery("#banners_options_adv-selector-id").val("");
                        jQuery(".tr-banners_options_adv-selector-id").hide();
                    } else {
                        jQuery(this).attr("checked", true);
                        jQuery(".tr-banners_options_adv-selector-id").show();
                    }

                });

                jQuery("#advanced_options_is-popup").change(function() {
                    if(jQuery("#advanced_options_adv-selector").attr("checked") === 'checked') {
                        jQuery("#advanced_options_adv-selector").attr("checked", false);
                        jQuery("#banners_options_adv-selector-id").val("");
                    } else {

                    }

                    if(jQuery("#advanced_options_is-popup").val() == 'popup') {

                        jQuery(".tr-banners_options_add-banner-after-n-paragraph, .tr-advanced_options_adv-selector, .tr-banners_options_adv-selector-id").hide();
                        jQuery(".tr-banners_options_popup-display-secs, .tr-banners_options_popup-hide-days").show();


                    } else {
                        jQuery(".tr-banners_options_add-banner-after-n-paragraph, .tr-advanced_options_adv-selector").show();
                        jQuery(".tr-banners_options_popup-display-secs, .tr-banners_options_popup-hide-days").hide();

                    }
                });


                if(jQuery("#advanced_options_adv-selector").attr("checked") === 'checked') {

                    jQuery(".tr-banners_options_adv-selector-id").show();
                } else {
                    jQuery(".tr-banners_options_adv-selector-id").hide();

                }

                jQuery(".banners_options_pid").val("<?php echo $post_ids_input_value; ?>");
                jQuery(".banners_options_pid_pages").val("<?php echo $page_ids_input_value; ?>");
                if(jQuery("#banners_options_display-banner-logic").val() == "choose") {
                    jQuery(".abn_posts_select").show();
                }
            });

            jQuery(".select_page_single").each( function() {
                var sps_pageid = jQuery(this).data("id");
                jQuery(this).click( function() {
                    var page_val = jQuery(".banners_options_pid_pages").val();


                    if(jQuery(this).hasClass("nowActive")) {
                        jQuery(this).removeClass("nowActive");

                        if (page_val.search(sps_pageid + ",") != -1) {
                            var newval = jQuery(".banners_options_pid_pages").val().replace(sps_pageid + ",", '');
                            jQuery(".banners_options_pid_pages").val(newval);
                        }
                        console.log(jQuery(".banners_options_pid_pages").val());
                    } else {
                        jQuery(this).addClass("nowActive");

                        jQuery(".banners_options_pid_pages").val(jQuery(".banners_options_pid_pages").val() + sps_pageid + "," );
                    }
                });
            });

            jQuery("#banners_options_display-banner-logic").change( function() {
                if(jQuery(this).val() == "choose") {
                    jQuery(".abn_posts_select").show();
                } else {
                    jQuery(".abn_posts_select").hide();
                    jQuery(".banners_options_pid").val("");
                    jQuery(".select_post_single").each( function() {
                        jQuery(this).removeClass("nowActive");
                    });
                }
            });

            jQuery(".select_post_single").each( function() {
                var sps_id = jQuery(this).data("id");
                jQuery(this).click( function() {
                    var post_val = jQuery(".banners_options_pid").val();


                    if(jQuery(this).hasClass("nowActive")) {
                        jQuery(this).removeClass("nowActive");
                        //jQuery(".banners_options_pid").remove();
                        if (post_val.search(sps_id + ",") != -1) {
                            var newval = jQuery(".banners_options_pid").val().replace(sps_id + ",", '');
                            jQuery(".banners_options_pid").val(newval);
                        }
                        console.log(jQuery(".banners_options_pid").val());
                    } else {
                        jQuery(this).addClass("nowActive");
                        //jQuery(".abn_posts_select").after("<input type='hidden' name='banners_options_pids' id='banners_options_pids' class='banners_options_pid" + sps_id + "' value='" +sps_id +"'>");
                        jQuery(".banners_options_pid").val(jQuery(".banners_options_pid").val() + sps_id + "," );
                    }
                });
            });

        </script>
        <?php
    }

    private function label( $field ) {
        switch ( $field['type'] ) {
            default:
                printf(
                    '<label class="" for="%s">%s</label>',
                    $field['id'], $field['label']
                );
        }
    }

    private function textarea( $field ) {
        printf(
            '<textarea class="regular-text" id="%s" name="%s" rows="%d">%s</textarea>',
            $field['id'], $field['id'],
            isset( $field['rows'] ) ? $field['rows'] : 5,
            $this->value( $field )
        );
    }

    private function field( $field ) {
        switch ( $field['type'] ) {
            case 'number':
                $this->input_minmax( $field );
                break;
            case 'select':
                $this->select( $field );
                break;
            case 'checkbox':
                $this->checkbox( $field );
                break;
            case 'textarea':
                $this->textarea( $field );
                break;
            default:
                $this->input( $field );
        }
    }

    private function checkbox( $field ) {
        printf(
            '<label class="rwp-checkbox-label"><input %s id="%s" name="%s" type="checkbox"> %s</label>',
            $this->checked( $field ),
            $field['id'], $field['id'],
            isset( $field['description'] ) ? $field['description'] : ''
        );
    }

    private function checked( $field ) {
        global $post;
        if ( metadata_exists( 'post', $post->ID, $field['id'] ) ) {
            $value = get_post_meta( $post->ID, $field['id'], true );
            if ( $value === 'on' ) {
                return 'checked';
            }
            return '';
        } else if ( isset( $field['checked'] ) ) {
            return 'checked';
        }
        return '';
    }

    private function input( $field ) {
        printf(
            '<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s">',
            isset( $field['class'] ) ? $field['class'] : '',
            $field['id'], $field['id'],
            isset( $field['pattern'] ) ? "pattern='{$field['pattern']}'" : '',
            $field['type'],
            $this->value( $field )
        );
    }

    private function input_minmax( $field ) {
        printf(
            '<input class="regular-text" id="%s" %s %s name="%s" %s type="%s" value="%s">',
            $field['id'],
            isset( $field['max'] ) ? "max='{$field['max']}'" : '',
            isset( $field['min'] ) ? "min='{$field['min']}'" : '',
            $field['id'],
            isset( $field['step'] ) ? "step='{$field['step']}'" : '',
            $field['type'],
            $this->value( $field )
        );
    }

    private function select( $field ) {
        printf(
            '<select id="%s" name="%s">%s</select>',
            $field['id'], $field['id'],
            $this->select_options( $field )
        );
    }

    private function select_selected( $field, $current ) {
        $value = $this->value( $field );
        if ( $value === $current ) {
            return 'selected';
        }
        return '';
    }

    private function select_options( $field ) {
        $output = [];
        $options = explode( "\r\n", $field['options'] );
        $i = 0;
        foreach ( $options as $option ) {
            $pair = explode( ':', $option );
            $pair = array_map( 'trim', $pair );
            $output[] = sprintf(
                '<option %s value="%s"> %s</option>',
                $this->select_selected( $field, $pair[0] ),
                $pair[0], $pair[1]
            );
            $i++;
        }
        return implode( '<br>', $output );
    }

    private function value( $field ) {
        global $post;
        if ( metadata_exists( 'post', $post->ID, $field['id'] ) ) {
            $value = get_post_meta( $post->ID, $field['id'], true );
        } else if ( isset( $field['default'] ) ) {
            $value = $field['default'];
        } else {
            return '';
        }
        return str_replace( '\u0027', "'", $value );
    }

}
new AB_Nixwood;