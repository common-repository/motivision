<?php

/**
 * Created by PhpStorm.
 * User: carlosperezsanchez
 * Date: 26/2/17
 * Time: 10:42
 */
class Motivision extends WP_Query
{
    private $wpdb;
    private $settings;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        $this->settings = get_option('motivision_setting', '');
    }

    private function visibleRoles()
    {
        $roles = ['editor', 'author'];

        if (!empty($this->settings['roles'])) {
            $roles = array();

            foreach ($this->settings['roles'] as $key => $value) {
                $roles[] = $key;
            }
        }

        return $roles;
    }

    public function checkBadgePopular($views)
    {
        $out = '';

        if ($views > $this->settings['badge_popular']) {
            $out = '<span class="label label-warning">Popular Post</span>';
        }

        return $out;

    }

    public function getTopUsers()
    {
        $topUsers = array();

        $roles = $this->visibleRoles();

        $users = new WP_User_Query(array('role__in' => $roles));

        foreach ($users->get_results() as $user) {
            $topUsers[$user->data->ID]['display_name'] = $user->data->display_name;

            $topUsers[$user->data->ID]['views'] = 0;

            $args = array(
                'posts_per_page' => -1,
                'author' => $user->data->ID,
                'orderby' => 'post_date',
                'order' => 'ASC',

                // Using the date_query to filter posts from last week
                'date_query' => array(
                    array(
                        'after' => '1 ' . $this->settings['display_time'] . ' ago'
                    )
                )
            );

            $posts = get_posts($args);

            foreach ($posts as $post) {

                $views = get_post_meta($post->ID, 'post_views_count', true);

                if (!empty($views) && isset($views)) {
                    $topUsers[$user->data->ID]['views'] = $topUsers[$user->data->ID]['views'] + $views;
                }
            }

        }

        $orderUsers = array();

        foreach ($topUsers as $user) {
            $orderUsers[$user['display_name']] = $user['views'];
        }

        arsort($orderUsers);

        return $orderUsers;
    }

    public function getCountUserPosts()
    {
        $topUsers = array();

        $roles = $this->visibleRoles();

        $users = new WP_User_Query(array('role__in' => $roles));

        foreach ($users->get_results() as $user) {
            $topUsers[$user->data->ID]['display_name'] = $user->data->display_name;

            $topUsers[$user->data->ID]['total'] = 0;

            $args = array(
                'posts_per_page' => -1,
                'author' => $user->data->ID,
                'orderby' => 'post_date',
                'order' => 'ASC',

                // Using the date_query to filter posts from last week
                'date_query' => array(
                    array(
                        'after' => '1 ' . $this->settings['display_time'] . ' ago'
                    )
                )
            );

            $posts = get_posts($args);

            $topUsers[$user->data->ID]['total'] = count($posts);

        }

        $orderUsers = array();

        foreach ($topUsers as $user) {
            $orderUsers[$user['display_name']] = $user['total'];
        }

        return $orderUsers;
    }

    public function getPercent($total)
    {
        $percent = ($total / $this->settings['goal_period']) * 100;

        if ($percent < 25) {
            return 25;
        } else if ($percent > 100) {
            return 100;
        } else {
            return $percent;
        }
    }

    public function getTotalPercent($total)
    {
        $percent = ($total / ($this->getCountUsers() * $this->settings['goal_period'])) * 100;

        if ($percent > 100) {
            return 100;
        } else {
            return round($percent);
        }

        return $percent;

    }

    public function getTotalPages()
    {
        $args = array(
            'posts_per_page' => -1,
            'post_status' => 'publish',
            // Using the date_query to filter posts from last week
            'date_query' => array(
                array(
                    'after' => '1 ' . $this->settings['display_time'] . ' ago'
                )
            )
        );

        $posts = get_posts($args);

        return ceil(count($posts)/MOTIVISION_PER_PAGE);
    }

    public function getPosts($offset = 0)
    {
        $args = array(
            'posts_per_page' => MOTIVISION_PER_PAGE,
            'offset' => MOTIVISION_PER_PAGE * $offset,
            'orderby' => 'post_date',
            'order' => 'DESC',
            'post_status' => 'publish',
            // Using the date_query to filter posts from last week
            'date_query' => array(
                array(
                    'after' => '1 ' . $this->settings['display_time'] . ' ago'
                )
            )
        );

        $posts = get_posts($args);

        $activities = array();

        foreach ($posts as $post) {
            $activity = array();

            $activity['id'] = $post->ID;
            $activity['title'] = $post->post_title;
            $activity['date'] = $post->post_date;
            $activity['url'] = $post->guid;
            $activity['author'] = get_user_by('id', $post->post_author)->display_name;
            $activity['views'] = get_post_meta($post->ID, 'post_views_count', true);
            $activity['image'] = get_the_post_thumbnail($post->ID, array(100, 100));



            $activities[] = $activity;
        }

        return $activities;
    }

    private function getCountUsers()
    {
        $roles = $this->visibleRoles();

        $users = new WP_User_Query(array('role__in' => $roles));

        return count($users->get_results());
    }
}