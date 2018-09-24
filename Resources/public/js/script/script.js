$(document).ready(function () {

    setMwGalleryWidget();
    setMwFileWidget();

    setMwCollectionField();

    $('form').bind('form-pre-serialize', function (e) {
        tinymce.triggerSave();
    });

    setPasswdStrengh();

    setSortableList();

    //Affiche les notices en flashbag
    $('.mweb-noty').each(function () {
        generateNotice($(this).data('type'), $(this).html());
    });
});


var canChangePassword = false;
var limitScorePassword = 25;
function setPasswdStrengh() {

    if ($('#mweb_user_form_change_password_plainPassword_first').length > 0 || $('#mweb_user_change_password_plainPassword_first').length > 0) {

        var options = {};
        options.ui = {
            showVerdictsInsideProgressBar: true,
            progressBarExtraCssClasses: "progress-bar-striped active"
        };
        options.common = {

            onKeyUp: function (evt, data) {
                $("#length-help-text").text("Current length: " + $(evt.target).val().length + " and score: " + data.score);

                if (data.score > limitScorePassword) {
                    canChangePassword = true;
                    $('#password-error').slideUp();
                } else {
                    canChangePassword = false;
                }
            },

        };
        $('#mweb_user_form_change_password_plainPassword_first').pwstrength(options);
        $('#mweb_user_change_password_plainPassword_first').pwstrength(options);

        $('button[type="submit"]').click(function () {
            if (canChangePassword)return true;
            else {
                $('#password-error').slideDown()
                return false;
            }
        })
    }
}


function setSortableList() {

    $('#mw-sortable-list tbody').sortable();

    $('#mw-sortable-list tbody').on("sortupdate", function (event, ui) {

        prototype = $('#form_entities').data('prototype');

        $('#mw-sortable-list tr.position-form').each(function (index, li) {
            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have
            var newForm = prototype.replace(/__name__/g, index);

            $(li).find('div:last-child').remove();
            $(li).append(newForm);
            $(li).find('#form_entities_' + index + '_id').val($(li).data('id'));
            $(li).find('#form_entities_' + index + '_position').val(index);


        });

    });

}

tinymce.init({
    selector: '.tinymce',  // change this value according to your HTML
    height: 350,
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
        {block: 'h3', title: 'Titre de niveau 3'},
        {block: 'h4', title: 'Titre de niveau 4'},
        {block: 'h5', title: 'Titre de niveau 5'},
        {block: 'p', title: 'Paragraphe'}
    ],
    external_filemanager_path: getDomain() + "/admin/tinymceBrowser/",
    filemanager_title: "Gestionnaire de fichier",
    external_plugins: {"filemanager": "plugins/responsivefilemanager/plugin.min.js"},
    setup: function (editor) {
        editor.on('change', function () {
            tinymce.triggerSave();
        });
    }
});

tinymce.init({
    selector: '.tinymce-small',  // change this value according to your HTML
    height: 150,
    plugins: [
        "advlist autolink link image lists charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
        "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
    ],
    toolbar1: "undo redo | bold italic underline ",
    image_advtab: true,
    menubar: false,
    style_formats: [
        {block: 'p', title: 'Paragraphe'}
    ],
    external_filemanager_path: getDomain() + "/admin/tinymceBrowser/",
    filemanager_title: "Gestionnaire de fichier",
    external_plugins: {"filemanager": "plugins/responsivefilemanager/plugin.min.js"},
    setup: function (editor) {
        editor.on('change', function () {
            tinymce.triggerSave();
        });
    }
});


/*********************************************************************/
/****************************  WIDGET  *******************************/
/*********************************************************************/

function setMwGalleryWidget() {

    $('.mw-gallery-widget').each(function () {
        var galleryItemCount = $(this).find('.mw-gallery-item').length;
        $(this).data('gallery-item-count', galleryItemCount)
    });
    // keep track of how many email fields have been rendered


    $('.mw-gallery-widget').find('.mw-gallery-item').each(function (i, item) {
        setMwGalleryWidgetDeleteItemButton($(item));
    });

    $('.mw-gallery-add').click(function (e) {
        e.preventDefault();

        $galleryWidget = $(this).parents('.mw-gallery-widget');
        galleryWidgetCount = $galleryWidget.data('gallery-item-count');

        var galleryPrototype = $galleryWidget.data('prototype');
        if ($galleryWidget.data('prototype-caption'))var galleryCaptionPrototype = $galleryWidget.data('prototype-caption');
        else var galleryCaptionPrototype = "";
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        galleryPrototype = galleryPrototype.replace(/__name__/g, galleryWidgetCount);
        if (galleryCaptionPrototype) galleryCaptionPrototype = galleryCaptionPrototype.replace(/__name__/g, galleryWidgetCount);
        galleryWidgetCount++;
        $galleryWidget.data('gallery-item-count', galleryWidgetCount);

        // create a new list element and add it to the list
        var $item = $('<li class="mw-gallery-item col-xs-6 col-sm-3 col-md-3 col-lg-2"><div class="mw-gallery-item-wrap"></div></li>')
        $item.find('.mw-gallery-item-wrap').html(galleryPrototype + galleryCaptionPrototype);

        $galleryWidget.find('.mw-gallery-item-add').before($item);

        setMwGalleryWidgetDeleteItemButton($item);
        log($item);
        //Ouvre la modal
        var filemanagerUrl = $galleryWidget.data('filemanager') + '?lang=' + $galleryWidget.data('lang') + '&type=' + $galleryWidget.data('type') + '&field_id=' + $item.find('input:first').prop('id') + '&relative_url=1';

        $('#modal-filemanager').on('shown.bs.modal', function () {

            /*$('#gallery-iframe').css('width', $(window).width() - 300);
             $('#gallery-iframe').height($(window).height() - 100);*/
            $('#modal-filemanager iframe').prop("src", filemanagerUrl);

        });
        $('#modal-filemanager').modal({show: true})
    });

}

function setMwFileWidget() {
    $('.mw-file-add').click(function (e) {
        $mwFileWidget = $(this).parents('.mw-file-widget');
        $mwFileInput = $mwFileWidget.find('input:first');
        $mwFileItem = $mwFileWidget.find('.mw-file-item');
        setMwFileWidgetDeleteItemButton($mwFileItem);

        var filemanagerUrl = $mwFileWidget.data('filemanager') + '?lang=' + $mwFileWidget.data('lang') + '&type=' + $mwFileWidget.data('type') + '&field_id=' + $mwFileInput.prop('id') + '&relative_url=1';

        $('#modal-filemanager').on('shown.bs.modal', function () {

            /*$('#gallery-iframe').css('width', $(window).width() - 300);
             $('#gallery-iframe').height($(window).height() - 100);*/
            $('#modal-filemanager iframe').prop("src", filemanagerUrl);

        });

        $('#modal-filemanager').modal({show: true})
    });

    $('.mw-file-widget').each(function(i,elm){
        if($(this).find('input:first').val()!=""){
            $mwFileItem = $(this).find('.mw-file-item')
            setMwFileWidgetDeleteItemButton($mwFileItem);
            $(this).find('.mw-file-add').hide();
        }
    })
}


function responsive_filemanager_callback(field_id) {
    $field = $('#' + field_id);

    if ($field.parents('.mw-gallery-widget').length) {
        filemanagerFolder = $field.parents('.mw-gallery-widget').data('uploads-folder');
        //IMAGE
        if ($field.parents('.mw-gallery-widget').data('type') == '1') {
            $field.parents('.mw-gallery-item-wrap').prepend('<div class="illu mw-gallery-item-row" style="background-image: url(' + filemanagerFolder + $field.val() + ');"></div>');

            //FILE
        } else if ($field.parents('.mw-gallery-widget').data('type') == '2') {
            $field.parents('.mw-gallery-item-wrap').prepend('<div class="file mw-gallery-item-row"><span class="glyphicon glyphicon-file"></span><span class="text">' + $field.val() + '</span></div>');

            //VIDEO
        } else if ($field.parents('.mw-gallery-widget').data('type') == '3') {
            alert('TO DO video')
        }

    }

    if ($field.parents('.mw-file-widget').length) {
        $field.parents('.mw-file-widget').find('.mw-file-add').hide();
        filemanagerFolder = $field.parents('.mw-file-widget').data('uploads-folder');
        //IMAGE
        if ($field.parents('.mw-file-widget').data('type') == '1') {
            $field.parents('.mw-file-item').prepend('<div class="illu mw-file-item-row" style="background-image: url(' + filemanagerFolder + $field.val() + ');"></div>');

            //FILE
        } else if ($field.parents('.mw-file-widget').data('type') == '2') {
            $field.parents('.mw-file-item').prepend('<div class="file mw-file-item-row"><span class="glyphicon glyphicon-file"></span><span class="text">' + $field.val() + '</span></div>');

            //VIDEO
        } else if ($field.parents('.mw-file-widget').data('type') == '3') {
            alert('TO DO video')
        }

    }

    $('#modal-filemanager').modal('toggle');
}


function setMwGalleryWidgetDeleteItemButton($item) {
    var $btnDelete = $('<button type="button"><span class="glyphicon glyphicon-remove"></span></button>');
    $item.append($btnDelete);

    $btnDelete.on('click', function (e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $item.remove();
    });
}

function setMwFileWidgetDeleteItemButton($item) {
    var $btnDelete = $('<button type="button"><span class="glyphicon glyphicon-remove"></span></button>');
    $item.append($btnDelete);

    $btnDelete.on('click', function (e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();
        $item.parents('.mw-file-widget').find('.mw-file-add').show();
        $item.parents('.mw-file-widget').find('.mw-file-item .mw-file-item-row').remove();
        $item.parents('.mw-file-widget').find('input:first').val('');
        // remove the li for the tag form
        $item.find('input:first').val('');
        $btnDelete.remove();
    });
}

function setMwCollectionField() {
    setMwDeleteCollectionField();
    $('.mw-collections').each(function (i, collection) {
        $(collection).find('.mw-add-collection-field').on('click', function () {
            var $list = $(collection).find('.mw-collections-list');
            // Try to find the counter of the list
            var counter = $list.children().length;
            // If the counter does not exist, use the length of the list

            // grab the prototype template
            var newWidget = $list.attr('data-prototype');
            // replace the "__name__" used in the id and name of the prototype
            // with a number that's unique to your emails
            // end name attribute looks like name="contact[emails][2]"
            newWidget = newWidget.replace(/__name__/g, counter);
            // Increase the counter
            counter++;
            // And store it, the length cannot be used if deleting widgets is allowed
            $list.data(' widget-counter', counter);

            // create a new list element and add it to the list
            var newElem = $($list.attr('data-widget-tags')).html(newWidget+' <button type="button" class="mw-delete-collection-field"><span class="glyphicon glyphicon-remove"></span> </button>');
            newElem.appendTo($list);
            setMwDeleteCollectionField();
        });
    });
}

function setMwDeleteCollectionField(){
    $('.mw-delete-collection-field').off('click')
    $('.mw-delete-collection-field').on('click', function () {
        $(this).parents('li').remove();
    })
}