$(document).ready(function () {

    /*manageProductionPrices();
    manageCollectionProducts();
*/
    $('form').bind('form-pre-serialize', function (e) {
        tinymce.triggerSave();
    });


    setPasswdStrengh();


    //Affiche les notices en flashbag
    $('.mweb-noty').each(function(){
        generateNotice($(this).data('type'), $(this).html());
    });
});


var canChangePassword = false;
var limitScorePassword = 25;
function setPasswdStrengh(){

    if($('#mweb_user_form_change_password_plainPassword_first').length>0 || $('#mweb_user_change_password_plainPassword_first').length>0){

        var options = {};
        options.ui = {
            showVerdictsInsideProgressBar: true,
            progressBarExtraCssClasses: "progress-bar-striped active"
        };
        options.common = {

            onKeyUp: function (evt, data) {
                $("#length-help-text").text("Current length: " + $(evt.target).val().length + " and score: " + data.score);

                if( data.score > limitScorePassword){
                    canChangePassword=true;
                    $('#password-error').slideUp();
                }else{
                    canChangePassword=false;
                }
            },

        };
        $('#mweb_user_form_change_password_plainPassword_first').pwstrength(options);
        $('#mweb_user_change_password_plainPassword_first').pwstrength(options);

        $('button[type="submit"]').click(function(){
            if(canChangePassword)return true;
            else{
                $('#password-error').slideDown()
                return false;
            }
        })
    }
}


tinymce.init({
    selector: '.tinymce',  // change this value according to your HTML
    plugins: [
        "advlist autolink link image lists charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
        "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
    ],
    toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
    toolbar2: "| responsivefilemanager | link unlink | forecolor backcolor  | print preview code ",
    image_advtab: true,
    menubar: false,
    style_formats: [
        {title: 'h3', block: 'Titre de niveau 1'},
        {title: 'h4', block: 'Titre de niveau 2'},
        {title: 'h5', block: 'Titre de niveau 3'},
        {title: 'p', block: 'Paragraphe'}
    ],
    external_filemanager_path: getDomain()+"/admin/tinymceBrowser/",
    filemanager_title: "Gestionnaire de fichier",
    external_plugins: {"filemanager": "plugins/responsivefilemanager/plugin.min.js"},
    setup: function (editor) {
        editor.on('change', function () {
            tinymce.triggerSave();
        });
    }
});

/*

function manageCollectionProducts() {
    // garde une trace du nombre de champs email qui ont été affichés
    var emailCount = 0;

    $('.collection-add').click(function () {
        var $list = $(this).prev('.collection-list');

        // parcourt le template prototype
        var newWidget = $list.attr('data-prototype');
        // remplace les "__name__" utilisés dans l'id et le nom du prototype
        // par un nombre unique pour chaque email
        // le nom de l'attribut final ressemblera à name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, emailCount);
        emailCount++;

        // créer une nouvelle liste d'éléments et l'ajoute à notre liste
        var newLi = $('<li></li>').html(newWidget);
        newLi.appendTo($list);
        setCollectionProductsUnity();
        initSelectChoice();

        return false;

    });
    $('.select').click(function () {
        setCollectionProductsUnity();
    });

}

function setCollectionProductsUnity() {
    $('#products-quantity-fields-list li').each(function (i, item) {
        log($(item).find('select.select option:selected').data('unity'));

        $(item).find('.product-unity').html($(item).find('select.select option:selected').data('unity'))
    });
}


function manageProductionPrices() {
    var REF_nbPerson = $('#nb-person').data('ref');

    $('#nb-person').focusout(function () {

        var nbPerson = $(this).val();

        $('.product-line').each(function (i, product) {
            var REF_quantity = $(product).find('.quantity').data('ref')
            var REF_price = $(product).find('.price').data('ref')

            //NOUVELLE QUANTITE
            log(nbPerson + ' * ' + REF_quantity + ' / ' + REF_nbPerson);
            var quantity = (nbPerson * REF_quantity) / REF_nbPerson;
            $(product).find('.quantity').html(quantity)

            log(nbPerson + ' * (' + REF_price + '*' + quantity + ') / ' + REF_nbPerson);
            var price = REF_price * quantity;
            $(product).find('.price').html(price)
        });

    });
}*/
