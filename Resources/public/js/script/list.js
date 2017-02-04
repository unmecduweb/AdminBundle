$(function () {
    /*menus();*/
    /*switchStatusTrans();*/
    /*editEntity();*/
    
    /*hostPageFilters();*/
    
});


function setDataTable(){

}

function menus()
{
    if ($(".connectedSortable").length)
    {
//        log($('#menu-reel').data('children').length);
        if ($('#menu-reel').attr('data-children').length)
        {
            $('#menu-reel').html(fromArrayToList($('#menu-reel').data('children')).html());
            var nestedArray = fromListToArray($('#menu-reel'));
            $('#json_menu').val(JSON.stringify(nestedArray));
        }
        $(".connectedSortable li").dblclick(function (e)
        {
            e.stopPropagation()
            menuPopoverForm($(this));
        });
        $(".connectedSortable").nestedSortable({
            connectWith: ".connectedSortable",
            listType: 'ul',
            relocate: function (e, ui)
            {
                var item = ui.item;
//                console.log(ui.item);
                menuPopoverForm(ui.item);

            }
        }).disableSelection();

        //    console.log(nestedArray);
    }

}


function menuPopoverForm(element)
{
    element.popover({trigger: 'manual', html: true, 'container': 'body'});
    element.on('shown.bs.popover', function ()
    {
        $('.menu_form input').focus().val(element.attr('data-name'));
//        $('.menu_form').blur(function(){element.popover('destroy');})
//                log($('.menu_form')[0].attr('data-target'));
        $('.menu_form').submit(
                function (e)
                {
                    e.preventDefault();
//            log('element');
//            log(element);
                    var newName = $(this).find('input').val();
                    element.children('.entityName:first').text(newName);
                    element.attr('data-name', newName);
                    var nestedArray = fromListToArray($('#menu-reel'));
                    $('#json_menu').val(JSON.stringify(nestedArray));
                    element.popover('destroy');

                });
        $('.menu_form_closer').click(function () {
            element.popover('destroy');
        });
    })
    element.popover('show');
}

function fromListToArray(list)
{
    var farray = [];
    var i = 0;
    list.children('li').each(function ()
    {
        var element = {'targetId': $(this).attr('target-id'), 'name': $(this).attr('data-name'), 'elementId': $(this).attr('data-elementId'), 'entityClass': $(this).attr('data-entityClass')};
        if ($(this).find('ul').length)
        {
            element.children = fromListToArray($(this).find('ul'));
        }
        element.position = i;
        farray.push(element);
        i++;
    });

    return farray;

}
function fromArrayToList(childrenArray)
{
//    log(childrenArray);
    var toReturn = '';
    var parent = $('<ul></ul>');

    childrenArray = $.map(childrenArray, function (value, index) {
        return [value];
    });
    for (var i = 0; i < childrenArray.length; i++)
    {

        var id = uniqid();
        var proto = $($('#liPrototype').html()).clone();
//        log('PROTO');
//        log(proto);
        proto.attr('target-id', childrenArray[i].targetId);
        proto.attr('data-name', childrenArray[i].name);
        proto.attr('data-elementId', childrenArray[i].elementId);
        proto.attr('data-entityClass', childrenArray[i].entityClass);
//        proto.html(childrenArray[i].name);
        proto.attr('id', 'menu-li-' + id);
        proto.find('.pageName').text(childrenArray[i].name);
        proto.find('.entityName').text(childrenArray[i].name);

        var dataContent = proto.attr('data-content');
        dataContent = dataContent.replace(/%_pageName_%/g, childrenArray[i].name);
        dataContent = dataContent.replace(/%_index_%/g, id);

        proto.attr('data-content', dataContent);

        if (childrenArray[i].children != undefined && childrenArray[i].children.length)
        {
//            var childrenList = $('<ul></ul>');
//            childrenList.append(fromArrayToList(proto,childrenArray[i].children));
            proto.append(fromArrayToList(childrenArray[i].children));

        }
//        log(proto);
//        log(id);
        parent.append(proto);

    }


    return parent;



}


function uniqid(prefix, more_entropy) {
    //  discuss at: http://phpjs.org/functions/uniqid/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //  revised by: Kankrelune (http://www.webfaktory.info/)
    //        note: Uses an internal counter (in php_js global) to avoid collision
    //        test: skip
    //   example 1: uniqid();
    //   returns 1: 'a30285b160c14'
    //   example 2: uniqid('foo');
    //   returns 2: 'fooa30285b1cd361'
    //   example 3: uniqid('bar', true);
    //   returns 3: 'bara20285b23dfd1.31879087'

    if (typeof prefix === 'undefined') {
        prefix = '';
    }

    var retId;
    var formatSeed = function (seed, reqWidth) {
        seed = parseInt(seed, 10)
                .toString(16); // to hex str
        if (reqWidth < seed.length) { // so long we split
            return seed.slice(seed.length - reqWidth);
        }
        if (reqWidth > seed.length) { // so short we pad
            return Array(1 + (reqWidth - seed.length))
                    .join('0') + seed;
        }
        return seed;
    };

    // BEGIN REDUNDANT
    if (!this.php_js) {
        this.php_js = {};
    }
    // END REDUNDANT
    if (!this.php_js.uniqidSeed) { // init seed with big random int
        this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
    }
    this.php_js.uniqidSeed++;

    retId = prefix; // start with prefix, add current milliseconds hex string
    retId += formatSeed(parseInt(new Date()
            .getTime() / 1000, 10), 8);
    retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
    if (more_entropy) {
        // for more entropy we add a float lower to 10
        retId += (Math.random() * 10)
                .toFixed(8)
                .toString();
    }

    return retId;
}

function switchStatusTrans() {
    $('.status-trans').click(function () {
        $this = $(this);
        var entity = $this.data('entity');
        var id = $this.data('id');
        var url = $this.data('url');

        var DATA = 'id=' + id + '&entity=' + entity;

        $.ajax({
            type: "POST",
            url: url,
            data: DATA,
            success: function (data) {
                $span = $('.status-trans[data-id="' + data.itemId + '"]').find('span');
                if (data.statusTrans == true) {
                    $span.removeClass("desactive");
                    $span.addClass("active");
                } else {
                    $span.removeClass("active");
                    $span.addClass("desactive");
                }
                generateNotice(data.type, data.textTrans);
            }
        });
        return false;
    });
}

//Edition d'une entité via modal
function editEntity() {
    $('.edit-entity').click(function () {
        $this = $(this);
        var url = $this.data('url');

        $.ajax({
            type: "POST",
            url: url,
            success: function (data) {
                if (data.success == true) {
                    $("#modal-container").html(data.form);
                    $('#modal-edit-entity').modal();
                    initSaveEntity($('#modal-edit-entity'));
                    initSelectChoice();
                }
            }
        });
    });

   
}

 //Permet d'initialiser la sauvegarde des entités à partir de la modal
function initSaveEntity($modal) {

    $modal.find('button.save-entity').click(function () {
        //Update les textarea
        if (typeof CKEDITOR != 'undefined') {
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
        }
        var data = $modal.find("form").serialize();
        var url = $modal.find("form").prop('action');
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function (data) {
                $this.attr('type', 'hidden');
                generateNotice(data.type, data.textTrans);

                initSelectChoice();
                $modal.on('hide.bs.modal', function () {

                    $("#modal-container").html("");

                });
                $modal.modal('hide');


            }
        });
    });

}
var dataTable;
//Gestion des filtres sur la liste des établissements
function hostPageFilters(){
    
    //création du tableau des établissements avec le plugin dataTable
    dataTable = $('#table_host').dataTable(
        {
            language: {
                processing:     "Traitement en cours...",
                search:         "Rechercher&nbsp;:",
                lengthMenu:     "Afficher _MENU_ &eacute;l&eacute;ments",
                info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
                infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                infoPostFix:    "",
                loadingRecords: "Chargement en cours...",
                zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                emptyTable:     "Aucune donnée disponible dans le tableau",
                paginate: {
                    first:      "Premier",
                    previous:   "Pr&eacute;c&eacute;dent",
                    next:       "Suivant",
                    last:       "Dernier"
                },
                aria: {
                    sortAscending:  ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            }

        }
    );
    
    
    //Filtres sur les types d'établissements
    $('.filter-form input').change(function(){
        tableFilterMode();
    });
    
    //A chque récriture du tableau (changement de page, tri sur une colone, chargement) l'event draw est appelé
    $('#table_host').on( 'draw.dt', function () {
        tableFilterCol();
    });
    
    
    $('.table-mode-radio').change(function(){
        tableFilterCol();
    });
    tableFilterMode();
    
   
}
//Affiche/Cache les lignes du tableau en fontion des types d'établissemnts séelctionnés    
function tableFilterMode(){
    //Récupère les éléments cochés
    values = new Array();
    $('.filter-form input:checked').each(function(){
        values.push($(this).val());
    });

    //Créer un string avec le tableau de valeur
    var choosedString = values.join("|");
    //Filtre les lignes du tableau
    dataTable.fnFilter(choosedString,1,true, false); 
}

function tableFilterCol(){
    $('.col-mode').hide();
    $('.col-mode.'+$('.table-mode-radio:checked').val()).show();
}
   
//    initComplete: function () {
//            var column = this.api().column('.filter');
//                $('.').on( 'change', function () {
//                    var val = $.fn.dataTable.util.escapeRegex(
//                        $(this).val()
//                    );
// 
//                    log(val);
//                    
//                    column
//                        .search( val ? '^'+val+'$' : '', true, false )
//                        .draw();
//                    } );
// 
//                column.data().unique().sort().each( function ( d, j ) {
//                    select.append( '<option value="'+d+'">'+d+'</option>' )
//                } );
//            } );
//        }
//    } );
   
    
