<?php

/**
 * Enqueue frontend script for table fo contents block
 *
 * @return void
 */

function ub_render_content_filter_entry_block($attributes, $content){
    extract($attributes);
    
    return '<div class="ub-content-filter-panel'.(isset($className) ? ' ' . esc_attr($className) : '').
        ($initiallyShow ? '' : ' ub-hide').'" data-selectedFilters="'.json_encode($selectedFilters).
        '" data-initiallyShow="'.json_encode($initiallyShow).'">'.
        $content.'</div>';
}

function ub_register_content_filter_entry_block(){
    if ( function_exists( 'register_block_type' ) ) {
        register_block_type( 'ub/content-filter-entry-block', array(
            'attributes' => array(
                'availableFilters' => array(
                    'type' => 'array',
                    'default' => array()//get list of filters from parent block
                ),
                /*'selectedFilters' => array(
                    'type' => 'array',
                    'default' => array()
                ),*/
                'buttonColor' => array(
                    'type' => 'string',
                    'default' => '#aaaaaa'
                ),
                'buttonTextColor' => array(
                    'type' => 'string',
                    'default' => '#000000'
                ),
                'initiallyShow' => array(
                    'type' => 'boolean',
                    'default' => true
                )
            ),
                'render_callback' => 'ub_render_content_filter_entry_block'));
        }
}

function ub_render_content_filter_block($attributes, $content){
    extract($attributes);

    $newFilterArray = json_decode(json_encode($filterArray), true);

    $filterList = '';

    foreach((array)$newFilterArray as $key1 => $filterGroup){
        $filterList .= '<div class="ub-content-filter-category"
        data-canUseMultiple="'.json_encode($filterGroup['canUseMultiple']).'">
        <div class="ub-content-filter-category-name">'.$filterGroup['category'].'</div>';

        foreach($filterGroup['filters'] as $key2 => $tag){
            $filterList .= '<div data-tagIsSelected="false" data-categoryNumber="'.$key1.'"
            data-filterNumber="'.$key2.'" '.($blockID == '' ? 'data-normalColor="'.$buttonColor.'" data-normalTextColor="'.$buttonTextColor.
            '" data-activeColor="'.$activeButtonColor.'" data-activeTextColor="'.$activeButtonTextColor.
            '"style="background-color: '.$buttonColor.'; color: '.$buttonTextColor.'"' :'').' class="ub-content-filter-tag">'.
            $tag.'</div>';
        }
        $filterList .= '</div>';
    }

$currentSelection = array_map(function($category){
                        return ($category['canUseMultiple'] ?
                                array_fill(0, count($category['filters']), false) :
                                -1);
                    }, (array)$filterArray);

return '<div class="wp-block-ub-content-filter'.(isset($className) ? ' ' . esc_attr($className) : '').
        '"'. ($blockID =='' ? : ' id="ub-content-filter-'.$blockID.'"') .
        ' data-currentSelection="'.json_encode($currentSelection).
        '" data-initiallyShowAll="'.json_encode($initiallyShowAll).'">'. 
    $filterList.$content.'</div>';
}

function ub_register_content_filter_block(){
    if ( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type( 'ub/content-filter-block', array(
            'attributes' => $defaultValues['ub/content-filter-block']['attributes'],
                'render_callback' => 'ub_render_content_filter_block'));
        
    }

}

function ub_content_filter_add_frontend_assets() {
    if ( has_block( 'ub/content-filter' ) or has_block('ub/content-filter-block') ) {
        wp_enqueue_script(
            'ultimate_blocks-content-filter-front-script',
            plugins_url( 'content-filter/front.build.js', dirname( __FILE__ ) ),
            array( ),
            Ultimate_Blocks_Constants::plugin_version(),
            true
        );
    }
}

add_action( 'wp_enqueue_scripts', 'ub_content_filter_add_frontend_assets' );
add_action('init', 'ub_register_content_filter_entry_block');
add_action('init', 'ub_register_content_filter_block');