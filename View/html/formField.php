<?php if(isset($formField)) { ?>
    <div class="input-container">
        <?php if($formField['type'] === "file") {   ?>
            <input id="<?php echo $formField['id'] ?>" name="<?php echo $formField['id'] ?>[]"
                   class="custom-file-input" type="<?php echo $formField['type'] ?>"
                <?php echo $formField['required'] ?> multiple/>
            <label for="<?php echo $formField['id'] ?>" class="custom-file-label"><?php echo $formField['placeholder'] ?></label>
        <?php } else { ?>
            <?php if($formField['type'] === "textarea") { ?>
                <textarea id="<?php echo $formField['id'] ?>" class='input text-input'
                          name="<?php echo $formField['id'] ?>" type="text" rows="<?php echo $formField['rows'] ?>"
                          <?php echo $formField['required'] ?> placeholder=" "><?php echo $formField['value'] ?></textarea>
            <?php } else if($formField['type'] === "selector") {

                if (isset($problems_session_string)) { ?>
                    <div class="session_problems">
                        <p class="subtitle" style="text-align: left; margin-bottom: 20px"> Problemes Actuals:</p>
                        <p style="color: var(--themeActiveFormTextColor);"><?php echo $problems_session_string;?></p>
                    </div>

                    <p class="subtitle" style="text-align: left"> Afegir problema:</p>
                <?php } ?>

                <select id="<?php echo $formField['id'] ?>" class="input" name="<?php echo $formField['name'] ?>"
                    <?php echo $formField['required'] ?>
                    <?php echo $formField['multiple']? 'multiple='.$formField['multiple']: "" ?>
                    <?php echo $formField['value']? 'value='.$formField['value']: "" ?>>
                    <?php foreach ($formField['options'] as $option) { ?>
                        <option value="<?php echo $option['id'] ?>">
                            <?php echo $option['title'] ?>
                        </option>
                    <?php } ?>
                </select>
            <?php }else if($formField['type'] === "checkbox") {?>

                <input type="<?php echo $formField['type'] ?>" id="<?php echo $formField['id'] ?>" name="<?php echo $formField['id'] ?>" value = "on"
                    <?php if($_GET['query'] == "Editar Problemas"){ if($problem['entregable'] == 'on'){?> checked <?php } } ?> />

                <label for="<?php echo $formField['id'] ?>" id = "entregableLabel"> Entregable </label>

                <?php if($_GET['query'] == "Editar Problemas"){ if($problem['entregable'] == 'on' && isset($problem['deadline'])){?>
                    <p id="hiddenDeadline" hidden> <?php echo $problem['deadline']?> </p>
                <?php } } ?>

            <?php }
            else {

                if( $formField['id'] == "class_group"){?>

                    <div id="wrapper">
                        <div id="first">
                            <input id="<?php echo $formField['id'] ?>" name="<?php echo $formField['id'] ?>"
                                   class="input <?php echo $formField['inputClasses'] ?>" type="<?php echo $formField['type'] ?>"
                                <?php echo $formField['required'] ?> placeholder=" "
                                <?php echo $formField['maxlength']? 'maxlength='.$formField['maxlength']: "" ?>
                                <?php echo $formField['minlength']? 'minlength='.$formField['minlength']: "" ?>
                                <?php echo $formField['max']? 'max='.$formField['max']: "" ?>
                                <?php echo $formField['min']? 'min='.$formField['min']: "" ?>
                                <?php echo $formField['multiple']? 'multiple': "" ?>
                                <?php echo $formField['value']? 'value='.$formField['value']: "" ?>>
                            <div class="cut"></div>
                            <label for="<?php echo $formField['id'] ?>" class="placeholder <?php echo $formField['labelClasses'] ?>">
                                <?php echo $formField['placeholder'] ?>
                            </label>
                        </div>
                        <div id="second">
                                <span    class="info"
                                         data-toggle="tooltip"
                                         data-placement="right"
                                         title="Un grup: x / Varis grups: x,y,z">
                                    <img src="View/images/icon-info-sign.png" height="30" width="30">
                                </span>
                            <style>
                                .info {
                                    display: inline-block;
                                    float: left;
                                    clear: both;
                                }
                                .tooltip-custom .tooltip-inner{
                                    background-color: cornflowerblue !important;
                                }
                                .bs-tooltip-right .arrow::before,
                                bs-tooltip-auto[x-placement="right"] .arrow::before{
                                    border-right-color: cornflowerblue;
                                }
                                #wrapper {
                                    width: 500px;
                                    overflow: hidden; /* will contain if #first is longer than #second */
                                }
                                #first {
                                    width: 300px;
                                    float:left; /* add this */
                                }
                                #second {
                                    padding-left: 10px;
                                    overflow: hidden; /* if you don't want #second to wrap below #first */
                                }
                            </style>
                        </div>
                    </div>

                <?php } else {?>
                    <input id="<?php echo $formField['id'] ?>" name="<?php echo $formField['id'] ?>"
                           class="input <?php echo $formField['inputClasses'] ?>" type="<?php echo $formField['type'] ?>"
                        <?php echo $formField['required'] ?> placeholder=" "
                        <?php echo $formField['maxlength']? 'maxlength='.$formField['maxlength']: "" ?>
                        <?php echo $formField['minlength']? 'minlength='.$formField['minlength']: "" ?>
                        <?php echo $formField['max']? 'max='.$formField['max']: "" ?>
                        <?php echo $formField['min']? 'min='.$formField['min']: "" ?>
                        <?php echo $formField['multiple']? 'multiple': "" ?>
                        <?php echo $formField['value']? 'value='.$formField['value']: "" ?>>

                    <div class="cut"></div>
                    <label for="<?php echo $formField['id'] ?>" class="placeholder <?php echo $formField['labelClasses'] ?>">
                        <?php echo $formField['placeholder'] ?>
                    </label>
                <?php}?>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>
<?php } ?>