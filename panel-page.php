<?php
$motivision = new Motivision;

$userPosts = $motivision->getCountUserPosts();

$writtenPosts = 0;

foreach ($userPosts as $key => $value) {
    $writtenPosts = $writtenPosts + $value;
}

$totalPercent = $motivision->getTotalPercent($writtenPosts);

$totalPages = $motivision->getTotalPages();

$pagination = 0;

if(isset($_GET['pagination']))
{
    $pagination = $_GET['pagination'];
}

?>

<div class="container">
    <h1>Motivision Panel</h1>
    <div class="row">
        <div class="col-md-8">
            <h2>Post Activity</h2>
            <div class="progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="<?= $totalPercent; ?>" aria-valuemin="0"
                     aria-valuemax="100" style="width: <?= $totalPercent; ?>%;">
                    <?= $totalPercent . '%'; ?>
                </div>
            </div>
            <?php
            $maxViews = 0;

            foreach ($motivision->getPosts($pagination) as $post) { ?>
                <div class="list-group-item">
                    <div class="row">
                        <div class="col-md-2">
                            <?= $post['image']; ?>
                        </div>
                        <div class="col-md-8">
                            <h4 class="list-group-item-heading"><a href="<?= $post['url']; ?>" alt="<?= $post['title']; ?>"><?= $post['title']; ?></a></h4>
                            <div class="list-group-item-text">
                                <span class="badge"><?= $post['author']; ?></span>
                                <b><?= date("d M Y", strtotime($post['date'])); ?></b> - <?= $post['views']; ?> views
                            </div>
                            <div>
                                <?=$motivision->checkBadgePopular($post['views']); ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <iframe src="https://www.facebook.com/plugins/share_button.php?href=<?= $post['url']; ?>&layout=button&size=small&mobile_iframe=true&appId=202877159735810&width=59&height=20" width="59" height="20" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
                            <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?= $post['url']; ?>">Tweet</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li <?php if($pagination-1 < 0) { echo 'class="disabled"'; } ?>>
                        <a href="<?php
                        if($pagination-1 < 0) {
                            echo '#';
                        } else {
                            echo menu_page_url('motivision',false).'&pagination='.($pagination-1);
                        }
                        ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for($i=0;$i<$totalPages;$i++) { ?>
                    <li><a href="<?php echo menu_page_url('motivision',false).'&pagination='.$i;?>"><?=($i+1);?></a></li>
                    <?php } ?>
                    <li>
                        <a href="<?php
                        if($pagination+1 > ($totalPages-1)) {
                            echo '#';
                        } else {
                            echo menu_page_url('motivision',false).'&pagination='.($pagination+1);
                        }
                        ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>

        </div>
        <div class="col-md-4">
            <div>
                <h2>Top writers</h2>
                <?php foreach ($motivision->getTopUsers() as $key => $value) { ?>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <span class="badge"><?= $value; ?> views</span>
                            <?php if ($i < 3) { ?>
                                <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                            <?php } $i++; ?>
                            <?= $key; ?>
                        </li>
                    </ul>
                <?php } ?>
            </div>
            <div>
                <h2>Number of posts</h2>
                <?php foreach ($userPosts as $key => $value) { ?>
                    <?php $percent = $motivision->getPercent($value); ?>
                    <h4><?= $key; ?></h4>
                    <div class="progress">
                        <div class="progress-bar <?php if ($value == 0) {
                            echo 'progress-bar-danger';
                        } ?>" role="progressbar" aria-valuenow="<?= $percent; ?>" aria-valuemin="0" aria-valuemax="100"
                             style="width: <?= $percent; ?>%;">
                            <?= $value . ' posts'; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>

</div>