/*******************************************************************************/
/********************************* MW LIBRARY  ********************************/

/* Fonctions communes aux projets zen (hall-inn & like-inn) */


//Evite les erreurs liés au console.log
function log(msg) {
    try {
        console.log(msg);
    } catch (e) {

    }
}


function getDomain() {
    domainEnd = window.location.href.indexOf('/', 8);
    if (window.location.href.indexOf('app_dev.php', domainEnd) >= 0) {

        return window.location.href.substr(0, domainEnd + 12);

    } else {

        return window.location.href.substr(0, domainEnd);
        
    }

}


//Convertit un formulaire ou un objet en JSON (utilisé pour l'envoie de donnée en ajax)
$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

/******************************* FORM FUNCTION ********************************/
//Fonctions liées au formulaires des projets ZEN

//Affiche ou cache un champ en fonction d'une checkbox ou d'un select en parametre data-element
function formToggler() {

    $('input[data-show-on], select[data-show-on]').each(function (i, elm) {
        $block = $(elm).parents('.form-item');
        $block.hide();


        //Form group possède l'id [pattern] de tous les champs enfants
        $dependency = $('#' + $(elm).parents('.form-group').attr('id') + '_' + $(elm).data('on-element'));
        $hider = $('#' + $(elm).parents('.form-group').attr('id') + '_' + $(elm).data('hider'));

        if ($dependency.is('select')) {

            if ($dependency.val() == $(elm).data('show-on')) {
                $block.show();
            } else {
                $block.hide();
            }

            $dependency
                .off('change', {elm: elm}, dependencySelectChange)
                .on('change', {elm: elm}, dependencySelectChange);

        } else if ($dependency.is('input[type="checkbox"],input[type="radio"] ')) {
            if ($dependency.prop('checked')) {
                $block.show();
            } else {
                $(elm).attr('disabled', 'disabled');
                $block.hide();
            }

            $dependency
                .off('change', {elm: elm}, dependencyInputChange)
                .on('change', {elm: elm}, dependencyInputChange);

            $hider.off('change', {elm: elm}, hiderChange)
                .on('change', {elm: elm}, hiderChange);

        }


    });
}

function checkFormBeforePost(form) {
    $return = true;
    //on parcourt les champs select qui sont marqués required, mais qui sont actif (cf sinon problème avec formToggleer)
    $(form).find('select.required:not(:disabled) ').each(function (i, select) {

        if ($(select).val() == '') {
            $('html, body').animate({
                    scrollTop: $(select).nextAll('.chosen-container').offset().top - 300
                }, 200, function () {
                    $(select).nextAll('.chosen-container').addClass('field-error');
                    $(select).prev('label').addClass('error');
                }
            );

            $return = false;
        }
    });
    log($return)
    return $return;
}

//Ajoute le préfixe http au champ de type url qui n'ont pas de protocle renseigné
function initFieldUrl() {
    $('input[type="url"]').each(function (i, field) {
        $(field).change(function () {
            log($(this).val());
            if ($(this).val().substr(0, 7) !== "http://" && $(this).val().substr(0, 8) !== "https://") {
                $(this).val('http://' + $(this).val());
            }
        })
    });
}

function initTooltip() {
    $('[data-toggle=tooltip]').tooltip({html: true});
    $('.help-tooltip').tooltip({
        'placement': 'bottom'
    });
}

function getDateFormatted(date) {
    var _d = date.getDate(),
        d = _d > 9 ? _d : '0' + _d,
        _m = date.getMonth() + 1,
        m = _m > 9 ? _m : '0' + _m,
        formatted = date.getFullYear() + '-' + m + '-' + d;
    return formatted;
}


function setFieldCollectionTag($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<div class="form-item"></div>').append(newForm);
    $newLinkLi.before($newFormLi);

}

function ajaxForm() {
    $('.ajax-form-submit, .modal-submit').click(function (e) {
        e.preventDefault();
        $form = $(this).parents('form');
        if (checkFormBeforePost($form)) {
            $.ajax({
                method: $form.prop('method'),
                url: $form.prop('action'),
                datatype: 'json',
                data: $form.serialize(),
                success: function (resp) {
                    $form.trigger("ajax-success", [resp.customParam]);
                    if (resp.type && resp.content) {
                        if (resp.content)
                            generateNotice(resp.type, resp.content);
                    }
                }
            });
        }
        if ($form.parents('.modal').length > 0) {
            $form.parents('.modal').modal('hide');
        }
    });
}

//Affiche une alert au click sur un bouton submit lorsqu'un utilisateur admin tente de modifer un établissement
function userNotAllowToEdit() {
    alert('Vous nêtes pas autorisé à effectuer cette action');
    return false;
}

function generateNotice(type, text) {
    var n = noty({
        text: text,
        type: type,
        dismissQueue: true,
        layout: 'bottomRight',
        closeWith: ['click'],
        maxVisible: 10,
        animation: {
            open: 'animated bounceInDown',
            close: 'animated bounceOutDown',
            easing: 'swing',
            speed: 500
        }
    });
}

////////////////////////////////  FABULUS SLIDER ///////////////////////////////
//set an .fabulus-slider class on your wrap slide, and use <ul> inside 
//ul has to be in width: 100%; position: absolute;
//        .list-arrow-left  .list-arrow-right
function setFabulusSlider(nbCol) {

    $('.fabulus-slider').each(function (i, wrap) {
        var $wrap = $(wrap);
        var $slider = $(wrap).children('ul');
        var nbElm = $slider.find('li').length;
        var ratio = nbElm / nbCol;
        var $prevArrow = $(wrap).prev('.list-arrow-left').show();
        var $nextArrow = $(wrap).next('.list-arrow-right').show();
        $wrap.attr('data-pos', 0);
        //TODO checkPos function 
        if (ratio > 1) {

            var sizeCol = 100 / (nbCol * ratio);
            log(nbCol)
            log(ratio)
            log(sizeCol)
            $slider.width(100 * ratio + '%');
            log($slider.find('li').width());
            $slider.find('li').css('width', sizeCol + '%')
            log($slider.find('li'));
            log($slider.find('li').width());
            $nextArrow.click(function () {

                if ($wrap.attr('data-pos') + nbCol < nbElm) {
                    move = -(parseInt($wrap.attr('data-pos')) + 1) * (100 / nbCol);
                    $wrap.attr('data-pos', parseInt($wrap.attr('data-pos')) + 1);
                    $slider.animate({left: move + '%'}, 200);
                    $prevArrow.css('opacity', '0.8');
                } else {
                    $nextArrow.css('opacity', '0.5');
                }
            });
            $prevArrow.click(function () {

                if ($wrap.attr('data-pos') > 0) {
                    move = -(parseInt($wrap.attr('data-pos')) - 1) * (100 / nbCol);
                    $wrap.attr('data-pos', parseInt($wrap.attr('data-pos')) - 1)
                    $slider.animate({left: move + '%'}, 200);
                    $nextArrow.css('opacity', '0.8');
                } else {
                    $prevArrow.css('opacity', '0.5');
                }
            });
        }
    });
}