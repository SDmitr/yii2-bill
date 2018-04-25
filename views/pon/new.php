<?php

$posts = array(
    1 => array(
        'title' => 'Заголовок 1',
        'content' => 'Какой-то текст 1',
        'href' => 'ffdsfsdfs'
    ),
    2 => array(
        'title' => 'Заголовок 2',
        'content' => 'Какой-то текст 2',
        'href' => 'ffdsfsdfs'
    ),
    3 => array(
        'title' => 'Заголовок 3',
        'content' => 'Какой-то текст 3',
        'href' => 'ffdsfsdfs'
    ),
    4 => array(
        'title' => 'Заголовок 4',
        'content' => 'Какой-то текст 4',
        'href' => 'ffdsfsdfs'
    ),
    5 => array(
        'title' => 'Заголовок 5',
        'content' => 'Какой-то текст 5',
        'href' => 'ffdsfsdfs'
    ),
);

$k = count($posts);
?>

<div id="slider" class="carousel slide" data-ride="carousel" data-interval="5000">
    <ol class="carousel-indicators">
       <?php for($i=1; $i<=$k; $i++): ?>   
       <li data-target="#slider" data-slide-to="<?php echo $i ?>" class="<?php if ($i == 1) echo 'active' ?>"></li>
       <?php endfor; ?>
    </ol>
    <div class="carousel-inner" style="height: 300px; background-color: gray;">
        <?php $i=1; ?>
        <?php foreach ($posts as $post): ?>    
            <div class="item <?php if ($i == 1) echo 'active' ?>" style="height: 250px;">
                <center>
                    <h1>
                        <?php echo $post['title'] ?>
                    </h1>
                </center>
                <div style="margin-left: 10%; color: white; font-weight: bolder;">
                    <?php echo $post['content'] ?>
                </div>
                <div class="carousel-caption">
                    <h4>
                        <?php echo $post['title'] ?>
                    </h4>
                </div>
            </div>
            <?php $i = $i+1; ?>
        <?php endforeach; ?>
        
        <a class="carousel-control left" href="#slider" data-slide="prev">
            <div class="glyphicon glyphicon-chevron-left"></div>
        </a>
        <a class="carousel-control right" href="#slider" data-slide="next">
            <div class="glyphicon glyphicon-chevron-right"></div>
        </a>
    </div>
</div>

