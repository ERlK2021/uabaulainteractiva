<header class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="/" title="TFG"><img id="logo" src="/View/images/favicon.png" alt="TFG"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-collapse-fragment"
            aria-controls="navbar-collapse-fragment" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>


    <div class="collapse navbar-collapse" id="navbar-collapse-fragment">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item" title="Pàgina principal">
                <a class="nav-link" href="/">
                    <img class="icon" src="/View/images/home.png" href="/" alt="Pàgina principal">
                </a>
            </li>
            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == PROFESSOR) { ?>
                <li class="nav-item" title="Invitar professor">
                    <a class="nav-link add-object" onclick="generateToken()" href="" data-toggle="modal"
                       data-target="#inviteProfessorModal">
                        <img class="icon" src="/View/images/professor.png" href="/" alt="Invitar professor">
                    </a>
                </li>
            <?php } ?>
            <div class="breadcrumb flat"style="padding-top: 0px;padding-bottom: 0px; margin-top: 16px;margin-left: 20px;padding-right: 0px;padding-left: 0px;">
                <?php foreach(array_keys($_SESSION['hist']) as $item_key){
                    ?><a href="<?php echo $_SESSION['hist'][$item_key]?>"><?php echo $item_key ?></a>
                <?php } ?>
            </div>
        </ul>
        <style>
            @import url(https://fonts.googleapis.com/css?family=Merriweather+Sans);

            body {
                font-family: 'Merriweather Sans', arial, verdana;
            }
            .breadcrumb {
                /*centering*/
                display: inline-block;
                box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.35);
                overflow: hidden;
                border-radius: 7px;
                /*Lets add the numbers for each link using CSS counters. flag is the name of the counter. to be defined using counter-reset in the parent element of the links*/
                counter-reset: flag;
                background-color: var(--themeActiveHeaderBGColor);
            }
            .breadcrumb a {
                background-color:var(--themeBreadcrumbColor) !important;
                text-decoration: none;
                outline: none;
                display: block;
                float: left;
                font-size: 15px;
                line-height: 36px;
                /*need more margin on the left of links to accomodate the numbers*/
                padding: 0px 10px 0 60px;
                position: relative;
            }
            /*since the first link does not have a triangle before it we can reduce the left padding to make it look consistent with other links*/
            .breadcrumb a:first-child {
                padding-left: 46px;
                border-radius: 5px 0 0 5px; /*to match with the parent's radius*/
            }
            .breadcrumb a:first-child:before {
                left: 14px;
            }
            .breadcrumb a:last-child {
                border-radius: 0 5px 5px 0; /*this was to prevent glitches on hover*/
                padding-right: 20px;
            }

            /*adding the arrows for the breadcrumbs using rotated pseudo elements*/
            .breadcrumb a:after {
                content: '';
                position: absolute;
                top: 0;
                right: -18px; /*half of square's length*/
                /*same dimension as the line-height of .breadcrumb a */
                width: 36px;
                height: 36px;
                transform: scale(0.707) rotate(45deg);
                /*we need to prevent the arrows from getting buried under the next link*/
                z-index: 1;
                /*background same as links but the gradient will be rotated to compensate with the transform applied*/
                background:var(--themeBreadcrumbColor) !important;
                background: linear-gradient(135deg, #666, #333);
                /*stylish arrow design using box shadow*/
                box-shadow:
                        2px -2px 0 2px rgba(0, 0, 0, 0.4),
                        3px -3px 0 2px rgba(255, 255, 255, 0.1);

                border-radius: 0 5px 0 50px;
            }
            /*we dont need an arrow after the last link*/
            .breadcrumb a:last-child:after {
                content: none;
            }
            /*we will use the :before element to show numbers*/
            .breadcrumb a:before {
                content: counter(flag);
                counter-increment: flag;
                /*some styles now*/
                border-radius: 100%;
                width: 20px;
                height: 20px;
                padding-left: 5px;
                line-height: 20px;
                margin: 8px 0;
                position: absolute;
                top: 0;
                left: 30px;
                background: #444;
                background: linear-gradient(#444, #222);
                font-weight: bold;
            }
            .flat a, .flat a:after {
                background: white;
                color: black;
                transition: all 0.6s;
            }
            .flat a:before {
                background: white;
                box-shadow: 0 0 0 1px #ccc;
            }
            .flat a:hover, .flat a.active,
            .flat a:hover:after, .flat a.active:after{
                background: #6495ED !important; /*CornFlower Blue.
            }

        </style>

    </div>

    <ul class="navbar-nav align-items-center">
        <?php if (isset($_SESSION['user'])) { ?>
            <div class="nav-item dropdown">
                <button class="btn dropdown-toggle" type="button" id="user-dropdown" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Perfil
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-dropdown">
                    <h6 class="font-weight-bold">Hola, <?php echo $_SESSION['user']; ?>!</h6>
                    <div class="dropdown-divider"></div>
                    <div class="d-inline-flex">
                        <div class="align-self-center">
                            <label for="dark-theme-switch"><i class="fas fa-moon"></i></label>
                        </div>
                        <input id="dark-theme-switch" type="checkbox" data-toggle="toggle" data-on=" " data-off=" "/>
                    </div>

                    <div class="dropdown-divider"></div>
                    <a id="dropdown-item logout-button" class="nav-link text-capitalize" href="/Model/logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Tancar Sessió
                    </a>
                </div>
            </div>
        <?php } else { ?>
            <li class="nav-item">
                <div class="align-self-center">
                    <label for="dark-theme-switch"><i class="fas fa-moon"></i></label>
                </div>
                <input id="dark-theme-switch" type="checkbox" data-toggle="toggle" data-on=" " data-off=" "/>
            </li>
            <li id="login-button" class="nav-item">
                <a class="nav-link text-capitalize" href="/index.php?query=Pàgina LogIn">
                    <img class="icon" src="/View/images/user.png" alt="User">
                    Iniciar Sessió
                </a>
            </li>
        <?php } ?>

    </ul>
</header>

<?php
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == PROFESSOR) {
//Generate a random string.
    $token = openssl_random_pseudo_bytes(164);
//Convert the binary data into hexadecimal representation.
    $token = bin2hex($token); ?>
    <div class="modal fade" id="inviteProfessorModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content col-12">
                <div class="modal-header">
                    <h4 class="modal-title">Enllaç per invitar un professor</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    Copia el link i passa-li a un professor!
                </div>
                <div class="modal-footer">
                    <label class="message" for="invitation_link">Link</label>
                    <div class="row">
                        <input disabled class="col-10 ur" type="url" id="invitation_link" size="30"
                               title="Link d'invitació">
                        <button class="cpy" onclick="copyInvitationLink()">
                            <i class="far fa-clone"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>