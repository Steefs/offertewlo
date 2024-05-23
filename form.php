<?php 
$form = new tmz_FORM();

if($form->form):
    $falses = false;
    if(is_array($form->form)): 
        $falses = $form->form;  
    endif;
    var_dump($falses);
?>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="offerteform">
    <?php
    if($falses): 
        ?>
        <div class="errorform">Er is een fout in een of meerdere velden</div>
        <?php
    endif;
    ?>
        <div class="fields">
            <div class="section">
                <h3>Boekingsinformatie</h3> 
            </div>
            <div class="field">
                <label>Huurdatum</label>
                <input type="date" id="start" name="trip-start" value="2018-07-22" min="2018-01-01" max="2018-12-31" />
                <i>Wij zijn niet verantwoordelijk voor het verkeerd invoeren van de huurdatum</i>
                <ul class="formlist">
                <li><input name="nodate" id="nodate" type="checkbox" value="1" ><label class="" for="nodate">Huurdatum Onbekend</label></li>
                </ul>
            </div>
            <div class="section">
                <h3>Contactinformatie</h3> 
            </div>
            <div class="field">
         
                <label>Naam</label>
                <div class="subfields"> 
                    <div class="field kapital  <?php echo $form->falsescheck('firstname', $falses);?>">
                        <input type="text" name="firstname">
                        <label>Achternaam</label>
                    </div>
                    <div class="field kapital  <?php echo $form->falsescheck('lastname', $falses);?>">
                        <input type="text" name="lastname">
                        <label>lastname</label>
                    </div>
                </div>
            </div>
            <div class="field">
                <label>Email</label> 
                <input type="email" name="mail">
            </div>
            
            <div class="field">
                <label>Telefoonnummer*</label> 
                <input type="text" name="phone">
            </div>
            <div class="section">
                <h3>Adres</h3> 
            </div>
            <div class="field">
                <div class="subfields"> 
                    <div class="field kapital  <?php echo $form->falsescheck('firstname', $falses);?>">
                        <label>Postcode</label>
                        <input type="text" name="firstname">
                    </div>
                    <div class="field kapital  <?php echo $form->falsescheck('lastname', $falses);?>">
                        <label>Huisnummer</label>
                        <input type="text" name="lastname">
                    </div>
                </div>
            </div>
            <div class="field">
                <label>Straatnaam</label> 
                <input type="text" name="street">
            </div>
            <div class="field">
                <label>Plaatsnaam</label> 
                <input type="text" name="city">
            </div>
            <div class="section">
                <h3>Extra wensen</h3> 
                <textarea class="inputbox"></textarea>
            </div>
            <div class="field">
            <label>Heb je een cadeaubon gekregen?</label> 
                <ul class="formlist">
                    <li><input name="giftcard" id="giftcard" type="checkbox" value="1" ><label class="" for="giftcard">Ja, ik heb een cadeaubon en wil deze graag inwisselen</label></li>
                </ul>
            </div>
        </div>
        <input type="submit" value="versturen">

    </form>
<?php
endif;
