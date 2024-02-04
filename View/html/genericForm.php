<?php if(!empty($formPage)) { ?>
    <head>
        <meta charset="UTF-8">
        <title>TFG - <?php echo $formPage['title'] ?></title>

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

        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

        <?php if(isset($formPage['validationJS'])) { ?>
            <script src="/View/js/<?php echo $formPage['validationJS'] ?>"></script>
        <?php } ?>
        <script src="/View/js/global.js"></script>
        <?php foreach ($formPage['customJS'] as $customJS) { ?>
            <script src="/View/js/<?php echo $customJS ?>"></script>
        <?php } ?>
    </head>

    <body class="d-flex flex-column min-vh-100 <?php echo $_SESSION['theme'] ?>">
    <?php include_once(__DIR__ . '/header.php') ?>

    <div class="container form-container">
        <form class="form" action="<?php echo $formPage['action'] ?>" method="post"
              onsubmit="return <?php echo $formPage['onSubmit'] ?>" enctype="multipart/form-data">
            <p class="title"><?php echo $formPage['title'] ?></p>

            <?php if(isset($formPage['emptyOptionsHref'])) { ?>
                <a class="subtitle" href="<?php echo $formPage['emptyOptionsHref'] ?>">
                    <?php echo $formPage['subtitle'] ?>
                </a>
            <?php } else {
                if(isset($formPage['subtitle'])) { ?>
                    <p class="subtitle"><?php echo $formPage['subtitle'] ?></p>
                <?php } ?>
                <div class="container">
                    <p class="alert alert-danger" <?php echo isset($formPage['error'])? "": "hidden"; ?> id="error_msg">
                        <?php echo $formPage['error'] ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </p>
                </div>
                <?php foreach($formPage['fields'] as $field) {
                    if ($field['type'] === 'row') { ?>
                        <div class="form-row">
                            <?php foreach($field['fields'] as $formField) { ?>
                                <div class="form-row-item">
                                    <?php include __DIR__ . '/formField.php'; ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else {
                        $formField = $field;
                        include __DIR__ . '/formField.php';
                    }
                } ?>

                <div class = "groups">
                    <?php foreach ($item['buttons'] as $button) {?>
                        <button class="btn <?php echo $button['classes'] ?>" type="button" data-placement="top">
                            <?php if (isset($button['value'])){ echo $button['value'];}?>
                        </button>
                    <?php } ?>
                </div>

                <div id="form-buttons-container">
                    <?php foreach($formPage['extraOptions'] as $option) { ?>
                        <a href="<?php echo $option['href'] ?>" class="btn">
                            <?php echo $option['optionText'] ?>
                        </a>
                    <?php } ?>

                    <input type="submit" class="btn" value="<?php echo $formPage['submitText'] ?>">
                </div>
            <?php } ?>
        </form>
    </div>

    <?php include_once(__DIR__ . '/footer.html') ?>
    </body>
<?php } ?>