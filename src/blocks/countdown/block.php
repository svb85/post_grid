<?php

function ub_render_countdown_block($attributes){
    //used to display initial rendering
    extract($attributes);

    $timeLeft = $endDate - time();
    $seconds = $timeLeft % 60;
    $minutes = (($timeLeft - $seconds) % 3600) / 60;
    $hours = (($timeLeft - $minutes * 60 - $seconds) % 86400) / 3600;
    $days = (($timeLeft - $hours * 3600 - $minutes * 60 - $seconds) % 604800) / 86400;
    $weeks = ($timeLeft - $days * 86400 - $hours * 3600 - $minutes * 60 - $seconds) / 604800;

    $defaultFormat = '<span class="ub_countdown_week">' . $weeks . '</span> ' . __( 'weeks', 'ultimate-blocks' )
        . ' <span class="ub_countdown_day">' . $days . '</span> ' . __('days', 'ultimate-blocks')
        . ' <span class="ub_countdown_hour">' . $hours . '</span> ' . __( 'hours', 'ultimate-blocks' )
        . ' <span class="ub_countdown_minute">' . $minutes . '</span> ' . __( 'minutes', 'ultimate-blocks' )
        . ' <span class="ub_countdown_second">' . $seconds . '</span> ' . __( 'seconds', 'ultimate-blocks' );

    if(!function_exists('ub_generateCircle')){
        function ub_generateCircle($label, $value, $limit, $color){
            $circlePath="M 50,50 m 0,-35 a 35,35 0 1 1 0,70 a 35,35 0 1 1 0,-70";
            $prefix="ub_countdown_circle_";
            return '<div class="'.$prefix.$label.'">
                        <svg height="70" width="70" viewBox="0 0 100 100">
                            <path class="'.$prefix.'trail" d="'.$circlePath.'" stroke-width="3" ></path>
                            <path class="'.$prefix.'path" d="'.$circlePath.'" stroke="'.$color.
                                '" stroke-width="3" style="stroke-dasharray: '.$value*219.911/$limit.'px, 219.911px;"></path>
                        </svg>
                        <div class="'.$prefix.'label ub_countdown_'.$label.'">'.$value.'</div>
                    </div>';
        }
    }
    
    $circularFormat = '<div class="ub_countdown_circular_container">
                        '.ub_generateCircle("week", $weeks, 52, $circleColor)
                        .ub_generateCircle("day", $days, 7, $circleColor)
                        .ub_generateCircle("hour", $hours, 24, $circleColor)
                        .ub_generateCircle("minute", $minutes, 60, $circleColor)
                        .ub_generateCircle("second", $seconds, 60, $circleColor).'
                        <p>'.__( 'Weeks', 'ultimate-blocks' ).'</p>
                        <p>'.__( 'Days', 'ultimate-blocks' ).'</p>
                        <p>'.__( 'Hours', 'ultimate-blocks' ).'</p>
                        <p>'.__( 'Minutes', 'ultimate-blocks' ).'</p>
                        <p>'.__( 'Seconds', 'ultimate-blocks' ).'</p>
                    </div>';

    $odometerSeparator = '<span class="ub-countdown-separator">:</span>';

    $emptySpan = '<span></span>';

    $odometerFormat = '<div class="ub-countdown-odometer-container">
                        <span>'.__( 'Weeks', 'ultimate-blocks' ).'</span>'.$emptySpan.'<span>'.__( 'Days', 'ultimate-blocks' ).'</span>'.$emptySpan.
                        '<span>'.__( 'Hours', 'ultimate-blocks' ).'</span>'.$emptySpan.'<span>'.__( 'Minutes', 'ultimate-blocks' ).'</span>'.$emptySpan.'<span>'.__( 'Seconds', 'ultimate-blocks' ).'</span>
                        <div class="ub-countdown-odometer ub_countdown_week">' . ($weeks < 0 ? $weeks : $weeks + pow(10, ($weeks > 0 ? floor(log10($weeks) + 1) : 1))).'</div> 
                        '. $odometerSeparator.' <div class="ub-countdown-odometer ub_countdown_day">' . ($days < 0 ? $days : $days + 10) . '</div>
                        '. $odometerSeparator.'<div class="ub-countdown-odometer ub_countdown_hour">' . ($hours < 0 ? $hours : $hours + 100) . '</div>
                        '. $odometerSeparator.'<div class="ub-countdown-odometer ub_countdown_minute">' . ($minutes < 0 ? $minutes : $minutes + 100) . '</div>
                        '. $odometerSeparator.'<div class="ub-countdown-odometer ub_countdown_second">' . ($seconds < 0 ? $seconds : $seconds + 100) . '</div></div>';

    $selctedFormat = $defaultFormat;
    
    if($style=='Regular'){
        $selectedFormat = $defaultFormat;
    }
    elseif ($style=='Circular') {
        $selectedFormat = $circularFormat;
    }
    else{
        $selectedFormat = $odometerFormat;
    }

    if($timeLeft > 0){
        return '<div '.($blockID==''?'': 'id="ub_countdown_'.$blockID.'"' ).'class="ub-countdown'.
                (isset($className)?' '.esc_attr($className):'').
                '" data-expirymessage="'.$expiryMessage.'" data-enddate="'.$endDate.'">
            '.$selectedFormat
            .'</div>';
    }
    else return '<div class="ub-countdown'.(isset($className) ? ' ' . esc_attr($className) : '').'" '.
        ($blockID==''?'style="text-align:'.$messageAlign.';' :'id="ub_countdown_'.$blockID.'"').'>'.$expiryMessage.'</div>';
}

function ub_register_countdown_block() {
	if( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
		register_block_type( 'ub/countdown', array(
            'attributes' => $defaultValues['ub/countdown']['attributes'],
            'render_callback' => 'ub_render_countdown_block'));
    }
}

add_action( 'init', 'ub_register_countdown_block' );

function ub_checkBlocks($block){
    static $currentBlocks = [];

    $current = $block;

    if( $block['blockName'] == 'core/block' ) {
        $current = parse_blocks( get_post_field( 'post_content', $block['attrs']['ref'] ) )[0];
    }
    if( $current['blockName'] == 'ub/countdown' ) {
        array_push( $currentBlocks, (array_key_exists('style', $current['attrs']) ?
                                    $current['attrs']['style'] : 'Odometer'));
        if( count( $current['innerBlocks'] ) > 0 ){
            foreach( $current['innerBlocks'] as $innerBlock ) {
                ub_checkBlocks( $innerBlock );
            }
        }
    }
    return $currentBlocks;
}

function ub_countdown_add_frontend_assets() {
    if ( has_block( 'ub/countdown')) {
        $blockList = [];
        foreach(parse_blocks( get_post()->post_content ) as $block){
            $blockList = ub_checkBlocks($block);
        }
        if(in_array('Odometer', $blockList)){
            wp_enqueue_script(
                'ultimate_blocks-countdown-odometer-script',
                plugins_url( 'countdown/odometer.js', dirname( __FILE__ ) ),
                array(  ),
                Ultimate_Blocks_Constants::plugin_version(),
                true
            );
        }

        wp_enqueue_script(
            'ultimate_blocks-countdown-script',
            plugins_url( 'countdown/front.build.js', dirname( __FILE__ ) ),
            array(  ),
            Ultimate_Blocks_Constants::plugin_version(),
            true
        );
    }
}

add_action( 'wp_enqueue_scripts', 'ub_countdown_add_frontend_assets' );