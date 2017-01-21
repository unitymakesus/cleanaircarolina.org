<?php
namespace feedthemsocial;
/**
 * Class FTS_Mashup_Feed
 * @package feedthemsocial
 */
class FTS_Mashup_Feed extends feed_them_social_functions
{
    /**
     * Construct
     *
     * Mashup Feed Constructor
     *
     * @since 1.0.0
     */
    function __construct()
    {
        add_shortcode('fts_mashup', array($this, 'fts_mashup_func'));
        add_action('wp_enqueue_scripts', array($this, 'fts_mashup_head'));
    }

    /**
     * FTS Mashup Head
     *
     * @since 1.0.0
     */
    function fts_mashup_head()
    {
        wp_enqueue_style('fts-feeds', plugins_url('feed-them-social/feeds/css/styles.css'));
        wp_enqueue_script('fts-global', plugins_url('feed-them-social/feeds/js/fts-global.js'), array('jquery'));
        wp_enqueue_script('fts-masonry-pkgd', plugins_url('feed-them-social/feeds/js/masonry.pkgd.min.js'), array('jquery'));
    }
    /**
     * FTS Custom Trim Words
     *
     * This function is a duplicate of fb trim words and is used for all feeds except fb, which uses it's original function that also filters tags which we don't need.
     *
     * @param $text
     * @param int $num_words
     * @param $more
     * @return mixed
     * @since 1.0.2
     */
    function fts_custom_trim_words($text, $num_words = 45, $more) {
        !empty($num_words) && $num_words !== 0 ? $more = __('...') : '';
        $text = nl2br($text);
        $text = strip_shortcodes($text);
        // Add tags that you don't want stripped
        $text = strip_tags($text, '<strong><br><em><i><a>');
        $words_array = preg_split("/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY);
        $sep = ' ';
        if (count($words_array) > $num_words) {
            array_pop($words_array);
            $text = implode($sep, $words_array);
            $text = $text . $more;
        } else {
            $text = implode($sep, $words_array);
        }
        return wpautop($text);
    }
    /**
     * FTS Date Sort
     *
     * @since 1.0.0
     */
    // Date sort option for multiple feeds in a shortcode
    function fts_dateSort($a, $b)
    {
        ////////////////////////////////////
        /////////////// dateA /////////////
        ///////////////////////////////////
        // facebook and instagram timestamp
        if (isset($a->created_time)) {
            // instagram timestamp is an epoch date stamp like 1477250244
            if (is_numeric($a->created_time)) {
                $dateA = strtotime(date("Y-m-d H:i:s", substr($a->created_time, 0, 10)));
            } // faceboook timestamp
            else {
                $dateA = strtotime($a->created_time);
            }
        }
        // twitter and pinterest timestamp
        if (isset($a->created_at)) {
            $dateA = strtotime($a->created_at);
        }
        // youtube timestap
        if (isset($a->snippet->publishedAt)) {
            $dateA = strtotime($a->snippet->publishedAt);
        }
        ////////////////////////////////////
        /////////////// dateB /////////////
        ///////////////////////////////////
        // facebook and instagram timestamp
        if (isset($b->created_time)) {
            // instagram timestamp is an epoch date stamp like 1477250244
            if (is_numeric($b->created_time)) {
                $dateB = strtotime(date("Y-m-d H:i:s", substr($b->created_time, 0, 10)));
            } // faceboook timestamp
            else {
                $dateB = strtotime($b->created_time);
            }
        }
        // twitter and pinterest timestamp
        if (isset($b->created_at)) {
            $dateB = strtotime($b->created_at);
        }
        // youtube timestap
        if (isset($b->snippet->publishedAt)) {
            $dateB = strtotime($b->snippet->publishedAt);
        }

        return ($dateB - $dateA);
    }
    /**
     * FTS Mashup Function
     *
     * Display Mashup Feed.
     *
     * @param $atts
     * @return mixed
     * @since 1.0.0
     */
    function fts_mashup_func($atts)
    {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        extract(shortcode_atts(array(
            // number of overall posts to show
            'posts' => '',
            // increase this to load more posts from each social network before date sorting them.
            'social_network_posts' => '',
            'words' => '',
            'stack_animation' => '',
            'center_container' => '',
            'space_between_posts' => '',
            'column_width' => '',
            'popup' => 'no',
            'grid' => '',
            'hide_thumbnail' => '',
            'show_social_icon' => '',
            'height' => '',
            'facebook_name' => '',
            'twitter_name' => '',
            'instagram_name' => '',
            'pinterest_name' => '',
            'pinterest_type' => '',
            'board_id' => '',
            'youtube_name' => '',
            'playlist_id' => '',
            'channel_id' => '',
            'background_color' => '',
            'padding' => '',

        ), $atts));

        $FB_Shortcode = shortcode_atts(array(
            'id' => $facebook_name,
            'type' => 'page',
            'posts' => '',
            'posts_displayed' => 'page_only',
            'words' => $words,

        ), $atts);

        ob_start();
        // start shortcode stuff...

        if ($popup == 'yes') {
            // it's ok if these styles & scripts load at the bottom of the page
            $fts_fix_magnific = get_option('fts_fix_magnific') ? get_option('fts_fix_magnific') : '';
            if (isset($fts_fix_magnific) && $fts_fix_magnific !== '1') {
                wp_enqueue_style('fts-popup', plugins_url('feed-them-social/feeds/css/magnific-popup.css'));
            }
            wp_enqueue_script('fts-popup-js', plugins_url('feed-them-social/feeds/js/magnific-popup.js'));
        }


        $mashup_functions = new feed_them_social_functions();

        $data_cache = 'fts_mashup_data_cache_' . $mashup_functions->feed_them_social_rand_string(10);
        // For facebook Likes and Comments only
        $data_cache_likes_comments_count = 'fts_mashup_likes_comments_' . $mashup_functions->feed_them_social_rand_string(10) . '';
        // Cache for Instagram User Info
        $data_cache_pinterest_user_info = 'fts_mashup_pinterest_user_info_' . $mashup_functions->feed_them_social_rand_string(10) . '';
        // Cache for Pinterest User Info
        $data_cache_instagram_user_info = 'fts_mashup_instagram_user_info_' . $mashup_functions->feed_them_social_rand_string(10) . '';


        $fts_instagram_username = $instagram_name; // 28902942 id is for gopro
        $fts_instagram_access_token = get_option('fts_instagram_custom_api_token');
        $fts_facebook_username = $facebook_name;
        $fts_facebook_custom_api_token = get_option('fts_facebook_custom_api_token');
        $access_token = $fts_facebook_custom_api_token;
        $fts_youtube_username = $youtube_name;
        $name = $twitter_name;
        $API_Token = get_option('fts_pinterest_custom_api_token');

        //Check Cache
        if (false !== ($transient_exists = $mashup_functions->fts_check_feed_cache_exists($data_cache))) {
            $feed_data = $mashup_functions->fts_get_feed_cache($data_cache);
            $response_post_array = $mashup_functions->fts_get_feed_cache($data_cache_likes_comments_count);
            $data_cache_pinterest_user_info = $mashup_functions->fts_get_feed_cache($data_cache_pinterest_user_info);
            $data_cache_instagram_user_info = $mashup_functions->fts_get_feed_cache($data_cache_instagram_user_info);
            $cache_used = true;
        } else {


            if (!empty($instagram_name)) {
                // INSTAGRAM
                $instagram_data_array['data'] = 'https://api.instagram.com/v1/users/' . $fts_instagram_username . '/media/recent/?count=' . $social_network_posts . '&access_token=' . $fts_instagram_access_token;
                $response_instagram = $this->fts_get_feed_json($instagram_data_array);
                $response_instagram = json_decode($response_instagram['data'], false);


                //Error Check
                if (isset($response_instagram->meta->error_message)) {
                    //Add Feed Type to post array
                    $instagram_feed = array(
                        (object)array(
                            'error_message' => '<div class="fts-mashup-error-notice"><strong>' . __('Admin Notice:', 'feed-them-social-combined-streams') . '</strong> ' . __('Error, ' . $response_instagram->meta->error_message . '. Please double check your user ID is correct and your access token on the Instagram Options page is working.', 'feed-them-social-combined-streams') . '</div>',
                            'created_time' => date("Y-m-d H:i:s"),
                            'fts_feed_type' => 'instagram',
                        )
                    );

                    $addData['data'] = $instagram_feed;
                    $feed_data = (object)$addData;
                    // foreach($feed_data->data as $post_array){
                    //     print $post_array->created_time;
                    // }

                } else {
                    //Add Feed Type to post array
                    $instagram_feed = $response_instagram->data;
                    foreach ($response_instagram->data as $post_array) {
                        $post_array->fts_feed_type = 'instagram';
                    }
                }
                // Here is the next url for use in our loadmore button
                // $pagination_instagram = $response_instagram->pagination->next_url;
                // Testing purposes
                //   print '<h1>Instagram</h1><br/> <strong>Next URL:</strong> ';
                //   print $pagination_instagram;
                //   print '<pre>';
                //       print_r($feed_data);
                //  print '</pre>';
            } else {
                // we send an empty array if no name found so the array merge still works
                $instagram_feed = array();
            }
            if (!empty($facebook_name)) {
                // FACEBOOK
                $facebook_class = new FTS_Facebook_Feed_Post_Types();
                $language = $facebook_class->get_language();
                $facebook_data_array['feed_data'] = 'https://graph.facebook.com/' . $fts_facebook_username . '/posts?fields=id,caption,created_time,description,from,icon,link,message,name,object_id,picture,full_picture,place,shares,source,status_type,story,to,with_tags,type&limit=' . $social_network_posts . '&access_token=' . $fts_facebook_custom_api_token . $language;
                $response_facebook = $this->fts_get_feed_json($facebook_data_array);
                $response_facebook = json_decode($response_facebook['feed_data']);


                $feed_data = $response_facebook;
                //  print '<pre>';
                //  print_r($feed_data);
                //  print '</pre>';

                //Error Check
                if (!empty($feed_data->error->message)) {


                    //Error Check
                    $fts_error_check = new fts_error_handler();
                    $fts_error_check_complete = $fts_error_check->facebook_error_check($FB_Shortcode, $feed_data);
                    if (is_array($fts_error_check_complete) && $fts_error_check_complete[0] == true) {
                        // return array(false, $fts_error_check_complete[1]);
                    }

                    // Feed Error Notice
                    //Add Feed Type to post array
                    $facebook_feed = array(
                        (object)array(
                            'error_message' => '<div class="fts-mashup-error-notice"><strong>' . __('Admin Notice:', 'feed-them-social-combined-streams') . '</strong> ' . __('Error, ' . $feed_data->error->message . 'Please double check your user ID is correct and your access token on the Facebook Options page is working.', 'feed-them-social-combined-streams') . '</div>',
                            //created time so this post always shows before others so admin see it right away and can fix any issues
                            'created_time' => date("Y-m-d H:i:s"),
                            'fts_feed_type' => 'facebook',
                        )
                    );

                    $addData['data'] = $facebook_feed;
                    $feed_data = (object)$addData;
                    // foreach($feed_data->data as $post_array){
                    //     print $post_array->created_time;
                    // }
                } else {

                    // Likes Comments Shares
                    $response_post_array = $facebook_class->get_post_info($feed_data, $FB_Shortcode, $access_token, $language);

                    // Here is the next url for use in our loadmore button
                    $pagination_facebook = isset($response_facebook->paging->next) ? $response_facebook->paging->next : '';
                    $facebook_feed = $feed_data->data;
                    //Add Feed Type to post array
                    $feed_data_type = isset($feed_data->data) ? $feed_data->data : '';
                    foreach ($facebook_feed as $post_array) {
                        $post_array->fts_feed_type = 'facebook';
                    }
                }
                // Testing purposes
                //   print '<h1>Facebook</h1><br/> <strong>Next URL:</strong> ';
                //   print $pagination_facebook;

                //  print '<pre>';
                //  print_r($feed_data);
                //  print '</pre>';

            } else {
                // we send an empty array if no name found so the array merge still works
                $facebook_feed = array();
            }

            if (!empty($pinterest_name)) {
                // PINTEREST
                // The username is figured out by the api token being used
                $API_Points = '&fields=id%2Clink%2Cnote%2Curl%2Cattribution%2Cmetadata%2Cboard%2Ccounts%2Ccreated_at%2Ccreator%2Cimage%2Cmedia%2Coriginal_link';
                $pins_data['pins'] = empty($board_id) ? 'https://api.pinterest.com/v1/me/pins/?limit=' . $social_network_posts . '&access_token=' . $API_Token . $API_Points : 'https://api.pinterest.com/v1/boards/' . $pinterest_name . '/' . $board_id . '/pins/?limit=' . $social_network_posts . '&access_token=' . $API_Token . $API_Points . '';

                //  $pins_data['pins'] = 'https://api.pinterest.com/v1/me/pins/?limit=' . $social_network_posts . '&access_token=' . $API_Token . $API_Points;
                $response_pinterest = $this->fts_get_feed_json($pins_data);
                $response_pinterest = json_decode($response_pinterest['pins']);

                //    print '<pre>';
                //    print_r($response_pinterest);
                //    print '</pre>';

                foreach ($response_pinterest->data as $post_array) {
                    if (isset($board_id)) {
                        $post_array->fts_feed_type = 'pinterest';
                        $post_array->fts_pinterest_feed_type = 'pins_from_board';

                    } else {

                        $post_array->fts_feed_type = 'pinterest';
                    }
                }

                if (!empty($response_pinterest->status) || isset($response_pinterest->message) && $response_pinterest->message == 'Board not found.') {
                    //Add Feed Type to post array
                    $pinterest_feed = array(
                        (object)array(
                            'error_message' => '<div class="fts-mashup-error-notice"><strong>' . __('Admin Notice:', 'feed-them-social-combined-streams') . '</strong> ' . __('Error, ' . $response_pinterest->message . ' Please double check your user ID or Board name is correct and your access token on the Pinterest Options page is working.', 'feed-them-social-combined-streams') . '</div>',
                            'created_time' => date("Y-m-d H:i:s"),
                            'fts_feed_type' => 'pinterest',
                        )
                    );

                    //   $addData['data'] = $pinterest_feed;
                    //   $feed_data = (object)$addData;
                    // foreach($feed_data->data as $post_array){
                    //     print $post_array->created_time;
                    // }

                } else {
                    // Here is the next url for use in our loadmore button
                    $pagination_pinterest = $response_pinterest->page->next;
                    //Add Feed Type to post array
                    foreach ($response_pinterest->data as $post_array) {
                        $post_array->fts_feed_type = 'pinterest';
                    }
                    //  echo '<pre>';
                    //  print_r(array_slice($error_check3->data, 0, 3));
                    //  echo '</pre>';


                    $pinterest_feed = $response_pinterest->data;
                    // Testing purposes
                    //   print '<h1>Pinterest</h1><br/> <strong>Next URL:</strong> ';
                    //   print $pagination_pinterest;
                    //   print '<pre>';
                    //   print_r($response_pinterest);
                    //   print '</pre>';

                }
            } else {
                // we send an empty array if no name found so the array merge still works
                $pinterest_feed = array();
            }
            if (!empty($youtube_name) || !empty($playlist_id) || !empty($channel_id)) {


                //  print '<pre>';
                //  print_r($userIDfinal->pageInfo);
                //  print '</pre>';

                $youtubeAPIkey = get_option('youtube_custom_api_token');
                if ($youtube_name !== '' && empty($userIDfinal->error->message)) {
                    // here we are getting the users channel ID for their uploaded videos
                    $youtube_userID_data['items'] = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=' . $fts_youtube_username . '&key=' . $youtubeAPIkey;
                    $userID_returned = $this->fts_get_feed_json($youtube_userID_data);

                    $userIDfinal = json_decode($userID_returned['items']);

                    if (isset($youtubeAPIkey) && $youtubeAPIkey !== '') {
                        // now we parse the users uploaded vids ID and create the playlist
                        foreach ($userIDfinal->items as $userID) {
                            $video_data['videos'] = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=' . $social_network_posts . '&playlistId=' . $userID->contentDetails->relatedPlaylists->uploads . '&order=date&key=' . $youtubeAPIkey;
                        }
                    }
                    $videos_returned = $this->fts_get_feed_json($video_data);
                    $videos = $videos_returned['videos'];
                    $response_youtube = json_decode($videos);
                } elseif ($channel_id !== '' && $playlist_id == '') {
                    $youtube_channelID_data['items'] = 'https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=' . $channel_id . '&order=date&maxResults=' . $social_network_posts . '&key=' . $youtubeAPIkey;
                    $userChannel_returned = $this->fts_get_feed_json($youtube_channelID_data);
                    $videos = $userChannel_returned['items'];
                    $response_youtube = json_decode($videos);
                } elseif ($playlist_id !== '' || $playlist_id !== '' && $channel_id !== '') {
                    $youtube_playlistID_data = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=' . $social_network_posts . '&playlistId=' . $playlist_id . '&order=date&key=' . $youtubeAPIkey;
                    $video_data['videos'] = $youtube_playlistID_data;
                    $videos_returned = $this->fts_get_feed_json($video_data);
                    $videos = $videos_returned['videos'];
                    $response_youtube = json_decode($videos);
                }


                //Error Check
                //  if (isset($userIDfinal->error->message) || $userIDfinal->pageInfo->totalResults == '0') {
                if ($response_youtube->pageInfo->totalResults == '0') {
                    //Add Feed Type to post array
                    $youtube_feed = array(
                        (object)array(
                            'error_message' => '<div class="fts-mashup-error-notice"><strong>' . __('Admin Notice:', 'feed-them-social-combined-streams') . '</strong> ' . __('Error, ' . (isset($userIDfinal->error->errors[0]->reason) ? $userIDfinal->error->errors[0]->reason : '') . ' Please double check your user ID is correct and your access token on the Youtube Options page is working.', 'feed-them-social-combined-streams') . '</div>',
                            'created_time' => date("Y-m-d H:i:s"),
                            'fts_feed_type' => 'youtube',
                        )
                    );

                    // $addData['data'] = $youtube_feed;
                    // $youtube_feed = (object)$addData;
                    // $youtube_feed = (object)$addData;
                    // foreach($feed_data->data as $post_array){
                    //     print $post_array->created_time;
                    // }

                } else {
                    // $videos = $videos_returned['videos'];

                    //  print '<pre>';
                    //   print_r($response_youtube);
                    //   print '</pre>';
                    //Add Feed Type to post array
                    // Here is the next url for use in our loadmore button
                    //    $pagination_youtube = $response_youtube->nextPageToken;
                    //Add Feed Type to post array
                    foreach ($response_youtube->items as $post_array) {
                        $post_array->fts_feed_type = 'youtube';
                    }
                    $youtube_feed = $response_youtube->items;

                }

                // Testing purposes
                //   print '<h1>Youtube</h1><br/> <strong>Next URL:</strong> ';
                //   print $pagination_youtube;

            } else {
                // we send an empty array if no name found so the array merge still works
                $youtube_feed = array();
            }
            if (!empty($twitter_name)) {
                // TWITTER
                include_once WP_CONTENT_DIR . '/plugins/feed-them-social/feeds/twitter/twitteroauth/twitteroauth.php';
                $fts_twitter_custom_consumer_key = get_option('fts_twitter_custom_consumer_key');
                $fts_twitter_custom_consumer_secret = get_option('fts_twitter_custom_consumer_secret');
                $fts_twitter_custom_access_token = get_option('fts_twitter_custom_access_token');
                $fts_twitter_custom_access_token_secret = get_option('fts_twitter_custom_access_token_secret');
                //Use custom api info
                if (!empty($fts_twitter_custom_consumer_key) && !empty($fts_twitter_custom_consumer_secret) && !empty($fts_twitter_custom_access_token) && !empty($fts_twitter_custom_access_token_secret)) {
                    $connection = new TwitterOAuthFTS(
                    //Consumer Key
                        $fts_twitter_custom_consumer_key,
                        //Consumer Secret
                        $fts_twitter_custom_consumer_secret,
                        //Access Token
                        $fts_twitter_custom_access_token,
                        //Access Token Secret
                        $fts_twitter_custom_access_token_secret
                    );
                }
                // $videosDecode = 'https://api.twitter.com/1.1/statuses/oembed.json?id=507185938620219395';
                // If excluding replies, we need to fetch more than requested as the
                // total is fetched first, and then replies removed.
                $show_retweets = true;
                $excludeReplies = true;
                $totalToFetch = ($excludeReplies) ? max(50, $social_network_posts * 3) : $social_network_posts;
                $description_image = !empty($description_image) ? $description_image : "";
                $show_retweets = !empty($show_retweets) ? $show_retweets : "1";
                if (!empty($show_retweets) && $show_retweets == 'yes') {
                    $show_retweets = '1';
                }
                if (!empty($show_retweets) && $show_retweets == 'no') {
                    $show_retweets = '0';
                }
                // $url_of_status = !empty($url_of_status) ? $url_of_status : "";
                // $widget_type_for_videos = !empty($widget_type_for_videos) ? $widget_type_for_videos : "";
                if (!empty($search)) {
                    $fetchedTweets = $connection->get(
                        'search/tweets',
                        array(
                            'q' => $search,
                            'count' => $totalToFetch,//
                            'result_type' => 'recent',
                            'include_rts' => $show_retweets,
                            'tweet_mode' => 'extended',
                        )
                    );
                } else {
                    $fetchedTweets = $connection->get(
                        'statuses/user_timeline',
                        array(
                            'tweet_mode' => 'extended',
                            'screen_name' => $name,
                            'count' => $social_network_posts,
                            'exclude_replies' => $excludeReplies,
                            'images' => $description_image,
                            'include_rts' => $show_retweets,
                        )
                    );
                }

                //   $convert_Array1['data'] = $fetchedTweets;
                //   $fetchedTweets = (object) $convert_Array1;

                //  print '<pre>';
                //    print_r($fetchedTweets);
                //    print '</pre>';

                if (isset($fetchedTweets->errors)) {
                    //  $error_check = __('Oops, Somethings wrong. ', 'feed-them-social') . $fetchedTweets->errors[0]->message;
                    if ($fetchedTweets->errors[0]->code == 32) {
                        $error_check = __(' Please check that you have entered your Twitter API token information correctly on the Twitter Options page of Feed Them Social.', 'feed-them-social');
                    }
                    if ($fetchedTweets->errors[0]->code == 34) {
                        $error_check = __(' Please check the Twitter Username you have entered is correct in your shortcode for Feed Them Social.', 'feed-them-social');
                    }
                } elseif (empty($fetchedTweets) && !isset($fetchedTweets->errors)) {
                    $error_check = __(' This account has no tweets. Please Tweet to see this feed. Feed Them Social.', 'feed-them-social');
                } //IS RATE LIMIT REACHED?
                elseif (isset($fetchedTweets->errors) && $fetchedTweets->errors[0]->code !== 32 && $fetchedTweets->errors[0]->code !== 34) {
                    $error_check = __(' Rate Limited Exceeded. Please go to the Feed Them Social Plugin then the Twitter Options page and follow the instructions under the header Twitter API Token.', 'feed-them-social');
                }
                //IS RATE LIMIT REACHED?
                // Did the fetch fail?
                if (isset($error_check)) {
                    //Add Feed Type to post array
                    // print $error_check;
                    // echo $error_check;
                    $twitter_feed = array(
                        (object)array(
                            'error_message' => '<div class="fts-mashup-error-notice"><strong>' . __('Admin Notice:', 'feed-them-social-combined-streams') . '</strong> ' . __('Error, ' . $error_check . '', 'feed-them-social-combined-streams') . '</div>',
                            'created_time' => date("Y-m-d H:i:s"),
                            'fts_feed_type' => 'twitter',
                        )
                    );

                    $fetchedTweets = $twitter_feed;
                    //  print '<pre>';
                    //    print_r($fetchedTweets);
                    //    print '</pre>';

                }//END IF
                else {

                    //Add Feed Type to post array
                    foreach ($fetchedTweets as $post_array) {
                        $post_array->fts_feed_type = 'twitter';
                    }

                    if (!empty($search)) {
                        $fetchedTweets = $fetchedTweets->statuses;
                    } else {
                        $fetchedTweets = $fetchedTweets;
                    }

                    // No real pagination it appears so far
                    // Testing purposes
                    // print '<h1>Twitter</h1><br/> <strong>Next URL:</strong> ';
                    // print $pagination_twitter;
                }

            } else {
                // we send an empty array if no name found so the array merge still works
                $fetchedTweets = array();
            }

            $fts_list_arrays = array_merge($facebook_feed, $instagram_feed, $pinterest_feed, $youtube_feed, $fetchedTweets);
            // var_dump( $fts_list_arrays);

            // Sort the array using the call back function
            usort($fts_list_arrays, array($this, "fts_dateSort"));
            $merged_Array['data'] = $fts_list_arrays;
            array_slice($merged_Array['data'], 1, 2);
            $feed_data = (object)$merged_Array;

            //   print '<pre>';
            //   print_r($feed_data);
            //   print '</pre>';

        }
        // Get random string so we can create more than one feed without clashing js etc
        $feed_name_type = 'fts_mashup';
        $feed_name_rand_string = trim($mashup_functions->feed_them_social_rand_string(10) . '_' . $feed_name_type);

        // For Instagram
        $instagram_data_array['user_info'] = 'https://api.instagram.com/v1/users/' . $fts_instagram_username . '?access_token=' . $fts_instagram_access_token;
        $instagram_response_user = $this->fts_get_feed_json($instagram_data_array);
        $instagram_user_info = !empty($instagram_response_user['user_info']) ? json_decode($instagram_response_user['user_info']) : '';

        //user info so we can show the user thumbnail
        $API_Points = '&fields=first_name%2Clast_name%2Cimage';
        $pins_data['pins'] = 'https://api.pinterest.com/v1/me/?access_token=' . $API_Token . $API_Points;
        $response_pinterest_user = $this->fts_get_feed_json($pins_data);
        $response_pinterest_user = json_decode($response_pinterest_user['pins']);

        //Cache It
        if (!isset($cache_used)) {
            //All social feeds array info
            if (empty($feed_data->error->message)) {
                $mashup_functions->fts_create_feed_cache($data_cache, $feed_data);
            }

            if (isset($facebook_name) && $facebook_name !== '' && empty($feed_data->error->message)) {
                //FB Likes And Comments Count
                $mashup_functions->fts_create_feed_cache($data_cache_likes_comments_count, isset($response_post_array));
            }

            if (isset($instagram_name) && $instagram_name !== '' && empty($feed_data->error->message)) {
                //Instagram User Info Cache
                $mashup_functions->fts_create_feed_cache($data_cache_instagram_user_info, $instagram_user_info);
            }

            if (isset($pinterest_name) && $pinterest_name !== '' && empty($feed_data->error->message)) {
                //Pinterest User Info Cache
                $mashup_functions->fts_create_feed_cache($data_cache_pinterest_user_info, $response_pinterest_user);
            }
        }
        //*********************
        // Post Information
        //*********************
        //  print '<pre>';
        // print_r($feed_data);
        // print '</pre>';
        if (isset($grid) && $grid == 'yes') {
            $output = '<script>';
            $output .= 'jQuery(window).load(function(){';
            $output .= 'jQuery(".' . $feed_name_rand_string . '").masonry({';
            $output .= 'itemSelector: ".fts-mashup-post-wrap"';
            $output .= '});';
            $output .= '});';
            $output .= '</script>';
        }

        //Main Wrap
        $stack_animation = 'no';
        $padding = isset($padding) ? 'padding:' . $padding . ' !important;' : '';
        $background_color = isset($background_color) ? 'background:' . $background_color . ' !important;' : '';
        $mashup_height = isset($height) && $height !== '' ? 'overflow:auto;height:' . $height . '' : '';

        if (isset($grid) && $grid == 'yes') {
            $output .= '<div class="fts-mashup ' . $feed_name_rand_string . ' masonry js-masonry"';
            if (isset($center_container) && $center_container == 'yes' && isset($grid) and $grid == 'yes') {
                $output .= 'data-masonry-options=\'{ "isFitWidth": ' . ($center_container == 'no' ? 'false' : 'true') . ' ' . ($stack_animation == 'no' ? ', "transitionDuration": 0' : '') . '}\' style="margin:auto;"';
            }
            $output .= '>';
        } else {
            $output = '<div class="fts-mashup ' . $feed_name_rand_string . '" style="' . $padding . $background_color . $mashup_height . '">';
        }


        // For Pinterest
        // $special_image_user = '60x60';
        // $special_image_user = isset($response_pinterest_user->data->image->$special_image_user->url) ? $response_pinterest_user->data->image->$special_image_user->url : '';

        //instagram user name for profile photo
        $instagram_user = isset($instagram_user_info->data->username) ? $instagram_user_info->data->username : '';
        // $instagram_profile_picture = isset($instagram_user_info->data->profile_picture) ? $instagram_user_info->data->profile_picture : '';
        $full_name = isset($instagram_user_info->data->full_name) ? $instagram_user_info->data->full_name : '';

        $count = 1;
        foreach ($feed_data->data as $post_data) {
            if ($count <= $posts) {
                //Set VARARIABLES
                $fts_feed_type = $post_data->fts_feed_type;
                Switch ($fts_feed_type) {
                    case 'facebook':
                        //Description fts_instagram_description
                        if (!isset($post_data->error_message)) {
                            $feed_name = 'Facebook';
                            //User Thumb
                            // $user_thumb_href = 'http://facebook.com/' . $post_data->from->id;
                        } else {
                            $facebook_description = $post_data->error_message;
                        }
                        break;
                    case 'twitter':
                        if (!isset($post_data->error_message)) {
                            // functions we call to in the twitter-feed.php
                            $fts_twitter_feed_class = new FTS_Twitter_Feed;
                            $feed_name = 'Twitter';
                            //User Thumb
                            //  $user_thumb_href = 'https://twitter.com/' . $post_data->user->screen_name;
                            //  $user_thumb_src = $post_data->user->profile_image_url_https;
                            //  $user_thumb_alt = $post_data->user->screen_name;
                            //  $user_thumb_img_class = 'twitter-image';
                            //User Name
                            $user_name_href = 'https://twitter.com/' . $post_data->user->screen_name;
                            $user_name = $post_data->user->name;
                            // Reply, Favorites and Retweets
                            $posts_replies = $fts_twitter_feed_class->fts_twitter_permalink($post_data);
                            $posts_favorite = $fts_twitter_feed_class->fts_twitter_favorite($post_data);
                            $posts_retweet = $fts_twitter_feed_class->fts_twitter_retweet($post_data);
                            $posts_description = $fts_twitter_feed_class->fts_twitter_description($post_data);

                            $twitter_video_reg = isset($post_data->extended_entities->media[0]->type) && $post_data->extended_entities->media[0]->type == 'video';
                            $twitter_video_extended = isset($post_data->extended_entities->media[0]->type) && $post_data->extended_entities->media[0]->type == 'video';
                            $twitter_video_retweeted = isset($post_data->retweeted_status->extended_entities->media[0]->type) && $post_data->retweeted_status->extended_entities->media[0]->type == 'video';
                            if ($twitter_video_reg || $twitter_video_extended || $twitter_video_retweeted) {
                                //Print our video if one is available
                                $posts_media_url = $fts_twitter_feed_class->fts_twitter_load_videos($post_data);
                            } else {
                                //Print our video if one is available
                                $posts_media_url = $fts_twitter_feed_class->fts_twitter_image($post_data, $popup);
                            }
                            //Created Time
                            $created_time = $post_data->created_at;
                        } else {
                            $posts_description = $post_data->error_message;
                        }
                        break;
                    case 'instagram':
                        //Description fts_instagram_description
                        if (!isset($post_data->error_message)) {
                            // functions we call to in the instagram-feed.php
                            $fts_instagram_feed_class = new FTS_Instagram_Feed;
                            $feed_name = 'Instagram';
                            //User Thumb
                            //  $user_thumb_href = 'https://www.instagram.com/' . $instagram_user;
                            //  $user_thumb_src = $instagram_profile_picture;
                            //  $user_thumb_title = $instagram_user;
                            //User Name
                            $user_name_href = 'http://instagram.com/' . $instagram_user;
                            $user_name = $full_name;
                            //Created Time
                            $created_time = $post_data->created_time;
                            $instagram_description = $fts_instagram_feed_class->fts_instagram_description($post_data);
                            //Image Link
                            $instagram_image_link = $fts_instagram_feed_class->fts_instagram_image_link($post_data);
                            //Video Link
                            $instagram_video_link = $fts_instagram_feed_class->fts_instagram_video_link($post_data);
                            //Post Link
                            $instagram_post_link = $fts_instagram_feed_class->fts_view_on_instagram_url($post_data);

                            $instagram_likes_count = $fts_instagram_feed_class->fts_instagram_likes_count($post_data);
                            $instagram_comments_count = $fts_instagram_feed_class->fts_instagram_comments_count($post_data);
                            $instagram_comment_likes_wrap = '<ul class="slicker-heart-comments-wrap"><li class="slicker-instagram-image-likes">' . $instagram_likes_count . ' </li><li class="slicker-instagram-image-comments"> <span class="fts-comment-instagram"></span> ' . $instagram_comments_count . '</li></ul>';

                            if (isset($post_data->type) && $post_data->type == 'video') {
                                //Instagram Video
                                $instagram_posts_media = '<div class="fts-fluid-videoWrapper-html5"><video controls="" poster="' . $instagram_image_link . '" width="100%;" style="max-width:100%;"><source src="' . $instagram_video_link . '" type="video/mp4"></video></div>';
                            } else {
                                //Instagram Image
                                $instagram_posts_media = '<a href="' . $instagram_post_link . '" target="_blank"><img class="fts-mashup-instagram-photo" src="' . $instagram_image_link . '"/></a>';
                            }
                        } else {
                            $instagram_description = $post_data->error_message;
                        }
                        break;
                    case 'pinterest':
                        if (!isset($post_data->error_message)) {
                            // functions we call to in the pinterest-feed.php
                            $fts_pinterest_feed_class = new FTS_Pinterest_Feed;
                            $feed_name = 'Pinterest';
                            //User Thumb
                            // $user_thumb_href = $post_data->creator->url;
                            // $user_thumb_src = $special_image_user;
                            //User Name
                            $user_name_href = $post_data->creator->url;
                            $user_name = $post_data->creator->first_name . ' ' . $post_data->creator->last_name;
                            //Created Time
                            $created_time = $post_data->created_at;

                            $pinterest_link = $fts_pinterest_feed_class->fts_view_on_pinterest_link($post_data);
                            $pinterest_image = '<a href="' . $pinterest_link . '" target="_blank"><img class="fts-mashup-pinterest-photo" src="' . $fts_pinterest_feed_class->fts_pinterest_image_url($post_data) . '"/></a>';
                            $pinterest_description = $fts_pinterest_feed_class->fts_pinterest_description($post_data);
                            $pinterest_repins_likes_wrap = $fts_pinterest_feed_class->fts_pinterest_repins_likes_wrap($post_data);
                            $pinterest_repins_likes_wrap = '<a href="' . $pinterest_link . '" target="_blank">' . $pinterest_repins_likes_wrap . '</a>';
                        } else {
                            $pinterest_description = $post_data->error_message;
                        }

                        break;
                    case 'youtube':
                        if (!isset($post_data->error_message)) {
                            $feed_name = 'Youtube';
                            //User Thumb ( not possible through api call )
                            //  $user_thumb_href = 'https://www.youtube.com/' . $youtube_name;
                            //  $user_thumb_src = 'https://yt3.ggpht.com/-Hd7qgktordw/AAAAAAAAAAI/AAAAAAAAAAA/DXefU6L9BAM/s100-c-k-no-mo-rj-c0xffffff/photo.jpg';
                            //User Name
                            $user_name = $post_data->snippet->channelTitle;
                            //Created Time
                            $created_time = $post_data->snippet->publishedAt;
                            $username = $youtube_name;
                            $youtube_video = $this->fts_youtube_video_and_wrap($post_data, $username, $playlist_id);
                            $youtube_title = '<div class="fts-youtube-title">' . $this->fts_youtube_title($post_data) . '</div>';
                            $youtube_description = $this->fts_youtube_description($post_data);
                            $youtube_description = $this->fts_youtube_link_filter($youtube_description);
                        } else {
                            $youtube_description = $post_data->error_message;
                        }

                        break;
                }

                if(isset($words) && !empty($words)){
                    $fts_facebook_feed_class = new FTS_Facebook_Feed();
                    $more = isset($more) ? $more : "";
                    $instagram_description = $this->fts_custom_trim_words(isset($instagram_description) ? $instagram_description : '', $words, $more);
                    $posts_description = $this->fts_custom_trim_words(isset($posts_description) ? $posts_description : '', $words, $more);
                    $pinterest_description = $this->fts_custom_trim_words(isset($pinterest_description) ? $pinterest_description : '', $words, $more);
                    $youtube_description = $this->fts_custom_trim_words(isset($youtube_description) ? $youtube_description : '', $words, $more);
                }

                $show_social_icon_right = isset($show_social_icon) && $show_social_icon == 'right' ? 'fts-mashup-icon-wrap-right' : '';
                $show_social_icon_left = isset($show_social_icon) && $show_social_icon == 'left' ? 'fts-mashup-icon-wrap-left' : '';

                //START MASHABLE POST
                //Post Wrap
                $output .= '<div class="fts-mashup-post-wrap fts-feed-type-' . $post_data->fts_feed_type  . (isset($show_social_icon) && $show_social_icon == 'left' ? ' fts-mashup-icon-left' : '') . '" ' . (isset($grid) && $grid == 'yes' ? 'style="' . $padding . $background_color . 'width:' . $column_width . '!important; margin:' . $space_between_posts . '!important"' : '') . '>';

                //Name Wrap.. leaving the user thunbnails out for the time being. To many issues with youtube at the moment.
                //  $output .= '<div class="fts-mashup-type-name">';
                //  $output .= '<div class="fts-jal-fb-post-time fts-mashup-'.$post_data->fts_feed_type.'-post">';
                //  $output .= '<span>'.$feed_name.'</span>';
                //  $output .= '</div>'; //Name Wrap
                //  $output .= '</div>'; //Name Wrap

                $output .= '<div class="fts-mashup-type-icon-spacer"></div>';
                $user_thumb_href = isset($user_thumb_href) ? $user_thumb_href : '';
                $user_name_href = isset($user_name_href) ? $user_name_href : '';
                //No Thumbnail and show icon on the right
                if ($show_social_icon == 'right') {
                    $output .= '<div class="fts-mashup-icon-wrap-right fts-mashup-' . $post_data->fts_feed_type . '-icon"><a href="' . $user_thumb_href . '" target="_blank"></a></div>';
                }
                if ($post_data->fts_feed_type !== 'facebook') {
                    //show icon
                    if ($show_social_icon == 'left') {
                        $output .= '<div class="fts-mashup-icon-wrap-left fts-mashup-' . $post_data->fts_feed_type . '-icon"><a href="' . $user_thumb_href . '" target="_blank"></a></div>';
                    }
                }

                if ($post_data->fts_feed_type !== 'facebook' && !isset($post_data->error_message)) {


                    if ($hide_thumbnail == 'no' && !isset($post_data->fts_feed_type) && $post_data->fts_feed_type !== 'youtube') {
                        $output .= '<div class="fts-jal-fb-user-thumb"><a href="' . $user_thumb_href . '" target="_blank"><img border="0" ' . ($class = isset($user_thumb_img_class) ? 'class="' . $user_thumb_img_class . '" ' : '') . ($alt = isset($user_thumb_alt) ? 'alt="' . $user_thumb_alt . '" ' : '') . ($title = isset($user_thumb_title) ? 'alt="' . $user_thumb_title . '" ' : '') . 'src="' . $user_thumb_src . '"/></a></div>';
                    }
                    //show icon
                    if ($show_social_icon == 'yes' && $hide_thumbnail == 'yes') {
                        $output .= '<div class="fts-mashup-icon-wrap fts-mashup-' . $post_data->fts_feed_type . '-icon"><a href="' . $user_thumb_href . '" target="_blank"></a></div>';
                    }
                    //User Name
                    $output .= '<span class="fts-jal-fb-user-name">';
                    $output .= '<a href="' . $user_name_href . '" target="_blank">' . $user_name . '</a>';
                    $output .= '</span>'; //User Thumb
                    $feed_type = isset($post_data->fts_feed_type) ? $post_data->fts_feed_type : '';
                    //Created Time
                    $output .= '<span class="fts-jal-fb-post-time">';
                    $output .= $this->fts_custom_date($created_time, $feed_type);
                    $output .= '</span>';
                }

                $post_types = new FTS_Facebook_Feed_Post_Types();
                $FBtype = isset($post_data->type) ? $post_data->type : "";
                $set_zero = 0;
                // we are not using single event posts in mashup soo varialbe is empty
                $single_event_array_response = '';


                if ($post_data->fts_feed_type == 'facebook' && isset($post_data->error_message)) {
                    $output .= '<div class="fts-mashup-description-wrap">';
                    $output .= $facebook_description;
                    $output .= '</div>';// fts-description-count-wrap
                }

                // Start the content for each post
                if ($post_data->fts_feed_type !== 'facebook') {
                    $output .= '<div class="fts-mashup-description-wrap">';
                    if ($fts_feed_type == 'twitter') {
                        //Twitter description
                        $output .= $posts_description;
                    }
                    if ($fts_feed_type == 'instagram') {
                        //Twitter description
                        $output .= $instagram_description;
                    }
                    if ($fts_feed_type == 'pinterest') {
                        //Pinterest description
                        $output .= $pinterest_description;
                    }
                    if ($fts_feed_type == 'youtube') {
                        //Youtube description
                        $output .= $youtube_description;
                    }
                    $output .= '</div>';// fts-description-count-wrap


                    $output .= '<div class="fts-mashup-image-and-video-wrap popup-gallery-twitter">';
                    if ($fts_feed_type == 'twitter' && !isset($post_data->error_message)) {
                        //Twitter Media
                        $output .= $posts_media_url;
                    }
                    if ($fts_feed_type == 'instagram' && !isset($post_data->error_message)) {
                        //Instagram Media
                        $output .= $instagram_posts_media;
                    }
                    if ($fts_feed_type == 'pinterest' && !isset($post_data->error_message)) {
                        //Pinterest Media
                        $output .= $pinterest_image;
                    }
                    if ($fts_feed_type == 'youtube' && !isset($post_data->error_message)) {
                        //Youtube Media
                        $output .= $youtube_video;
                        $output .= $youtube_title;
                    }
                    $output .= '</div>';// fts-mashup-count-wrap


                    $output .= '<div class="fts-mashup-count-wrap">';
                    if ($fts_feed_type == 'twitter' && !isset($post_data->error_message)) {
                        //Twitter Favorite Icon and Count
                        $output .= $posts_replies;
                        $output .= $posts_favorite;
                        $output .= $posts_retweet;
                    }
                    if ($fts_feed_type == 'instagram' && !isset($post_data->error_message)) {
                        //Instagram Favorite Icon and Count
                        $output .= '<a href="' . $instagram_post_link . '" target="_blank">';
                        $output .= $instagram_comment_likes_wrap;
                        $output .= '</a>';
                    }
                    if ($fts_feed_type == 'pinterest' && !isset($post_data->error_message)) {
                        //Pinterest Favorite Icon and Count
                        $output .= $pinterest_repins_likes_wrap;
                    }
                    $output .= '</div>';// fts-mashup-count-wrap
                }

                if ($fts_feed_type == 'facebook' && !isset($post_data->error_message)) {
                    $output .= $post_types->feed_post_types($set_zero, $FBtype, $post_data, $FB_Shortcode, $response_post_array, $single_event_array_response);
                }
                $output .= '</div>';//Post Wrap
            }
            $count++;

        }
        $output .= '</div>';//Main Wrap

        return $output;

        return ob_get_clean();
    }

}//fts_mashup_func END CLASS
new FTS_Mashup_Feed();
?>