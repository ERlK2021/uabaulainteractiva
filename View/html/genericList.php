<?php if(!empty($listPage)) { ?>
    <!DOCTYPE html>
    <html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>TFG - <?php echo $listPage['title'] ?></title>

    <link rel="shortcut icon" href="/View/images/favicon.png">
    <link rel="stylesheet" href="/View/css/external/bootstrap.min.css">
    <link rel="stylesheet" href="/View/css/external/bootstrap-toggle.min.css">
    <link rel="stylesheet" href="/View/css/external/all.css">
    <link rel="stylesheet" href="/View/css/style.css"/>
    <link rel="stylesheet" href="/View/css/forms.css"/>

    <script src="/View/js/external/jquery.min.js"></script>
    <script src="/View/js/external/popper.min.js"></script>
    <script src="/View/js/external/bootstrap.min.js"></script>
    <script src="/View/js/external/bootstrap-toggle.min.js"></script>
    <script src="/View/js/external/all.min.js"></script>
    <script src="/View/js/global.js"></script>
    <?php if(isset($listPage['customJS'])) { ?>
        <script src="/View/js/<?php echo $listPage['customJS'] ?>"></script>
    <?php } ?>
</head>

<?php function generateListItem($item) { ?>
    <div id="<?php echo $item['id'] ?>" class="card">
        <div class="card-body"<?php  $color = null;
        foreach ($item['buttons'] as $button){
            if($button['image'] == 'activated' || $button['image'] == 'edit' ){ $color = "red";}
            else if($button['image'] == 'deactivated' || $button['image'] == 'no-edit'){$color = "green";}
            //else{;}
        }
        if($color == "green"){?> style="background-color:#FF6666 ; border-radius: 10px;" <?php }
        if($color == "red"){?> style="background-color:darkseagreen; border-radius: 10px;" <?php } ?>
        >
            <div>
                <?php if (isset($item['href'])) { ?>
                    <a href="<?php echo $item['href'] ?>">
                        <h5><?php echo $item['title'] ?></h5>
                    </a>
                <?php } else { ?>
                    <h5 class="card-title"> <?php echo $item['title'] ?> </h5>
                    <p class="card-text"> <?php echo $item['description'] ?></p>
                <?php } ?>
            </div>

            <div>
                <?php foreach ($item['buttons'] as $button) {
                    if($button['type'] === 'a') { ?>
                        <a href="<?php echo $button['href'] ?>" class="btn <?php echo $button['classes'] ?>">
                    <?php } else if ($button['type'] == 'modalToggle') { ?>
                        <button class="btn" type="button" data-placement="top"
                        title="<?php echo $button['title'] ?>" data-toggle="modal"
                        data-target="#<?php echo $button['target'] ?>">
                    <?php } else { ?>
                        <button <?php echo isset($button['id'])? 'id='.$button['id']: '' ?>
                        class="btn <?php echo $button['classes'] ?>" type="button" data-placement="top"
                        title="<?php echo $button['title'] ?>"
                        <?php if(isset($button['parameter'])){
                            echo 'onclick='.$button['onClick'].'(\''. $button['parameter'].'\')';//For cases when we need to send string to onclick.
                        }
                        else{
                            echo isset($button['onClick'])? 'onclick='.$button['onClick']: '' ?>
                        <?php } ?>>


                    <?php } ?>
                    <img class="icon" src="/View/images/<?php echo $button['image'] ?>.png"
                         alt="<?php echo $button['alt'] ?>" title="<?php echo $button['alt'] ?>">
                    <?php if($button['type'] === 'a') { ?>
                        </a>
                    <?php } else { ?>
                        </button>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<body class="d-flex flex-column min-vh-100 <?php echo $_SESSION['theme'] ?>">
<?php include_once(__DIR__ . "/header.php") ?>

<div class="container">
    <?php if (isset($listPage['errorMessage'])) { ?>
        <p class="alert alert-danger" id="error_msg">
            <?php echo $listPage['errorMessage'] ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </p>
    <?php } ?>
    <?php if (isset($listPage['infoMessage'])) { ?>
        <p class="alert alert-success" id="info_msg">
            <?php echo $listPage['infoMessage'] ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </p>
    <?php } ?>

    <div class="content-header">
        <h1 class="text-capitalize"><?php echo $listPage['title'] ?></h1>
        <div class="header-buttons">
            <?php foreach ($listPage['headerButtons'] as $headerButton) { ?>
                <a class="btn <?php echo $headerButton['classes'] ?>" href="<?php echo $headerButton['href'] ?>">
                    <img class='icon' src="/View/images/<?php echo $headerButton['img'] ?>.png"
                         alt="<?php echo $headerButton['alt'] ?>" title="<?php echo $headerButton['alt'] ?>">
                </a>
            <?php } ?>
        </div>
    </div>

    <?php if (isset($listPage['groups'])) {
        foreach ($listPage['groups'] as $group => $items) { ?>
            <button class="collapsible"><h5><?php echo $group ?></h5></button>
            <div class="content">
                <?php foreach ($items as $item) {
                    generateListItem($item);
                } ?>
            </div>
        <?php }
    } else { ?>
        <div class="card-container">
            <?php
            if (empty($listPage['items'])) { ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"> La llista està buida </h5>
                    </div>
                </div>
            <?php } else {
                foreach ($listPage['items'] as $item) {
                    generateListItem($item);
                }
            } ?>
        </div>
    <?php } ?>
</div>

<?php foreach ($listPage['modals'] as $modal) { ?>
    <div class="modal fade" id="<?php echo $modal['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $modal['title'] ?></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <?php if (isset($modal['field'])) {
                        $formField = $modal['field'];
                        include __DIR__ . '/formField.php';
                    } else { ?>
                        <p><?php echo $modal['content'] ?></p>
                    <?php } ?>
                    <div class="modal-buttons">
                        <?php if (isset($modal['dismissButtonText'])) { ?>
                            <button type="button" class="btn" data-dismiss="modal">
                                <?php echo $modal['dismissButtonText'] ?>
                            </button>
                        <?php } ?>
                        <button class="btn" type="button" data-placement="top"
                                title="<?php echo $modal['buttonTitle'] ?>"
                                onclick="<?php echo $modal['buttonOnClick'] ?>">
                            <?php echo $modal['buttonText'] ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php include_once(__DIR__ . "/footer.html") ?>
</body>
<?php } ?>