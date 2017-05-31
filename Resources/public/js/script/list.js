$(document).ready(function () {
    /*menus();*/
    /*switchStatusTrans();*/
    /*editEntity();*/
    
    /*hostPageFilters();*/

    initAjaxForm();
});



//Edition d'une entité via modal
function initAjaxForm() {
    $('.ajax').click(function (e) {
        e.preventDefault();
        $this = $(this);
        var url = $this.data('url');

        $.ajax({
            type: "POST",
            url: url,
            success: function (data) {

                $("#modal-container").html(data.form);
                $('#modal-edit-entity').modal();
                saveAjaxForm($('#modal-edit-entity'));
            }
        });
    });


}
//Permet d'initialiser la sauvegarde des entités à partir de la modal
function saveAjaxForm($modal) {

    $modal.find('button.save-form').click(function () {
        var data = $modal.find("form").serialize();
        var url = $modal.find("form").prop('action');
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function (data) {
                if (data.status == 'error') {
                    generateNotice(data.status, data.message);
                    $modal.modal('destroy');
                    $("#modal-container").html(data.form);
                    $('#modal-edit-entity').modal();
                    saveAjaxForm($('#modal-edit-entity'));

                }else if(data.status == 'refresh'){
                    window.location.reload();
                }else{
                    $this.attr('type', 'hidden');
                    generateNotice(data.status, data.message);

                    $modal.on('hide.bs.modal', function () {

                        $("#modal-container").html("");

                    });
                    $modal.modal('hide');

                }


            }
        });
    });

}
/*
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
}*/




//Edition d'une entité via modal
function editEntity() {
    $('.edit-entity').click(function () {
        $this = $(this);
        var url = $this.data('url');

        $.ajax({
            type: "POST",
            url: url,
            success: function (data) {
                if (data.status == true) {
                    $("#modal-container").html(data.form);
                    $('#modal-edit-entity').modal();
                    initSaveEntity($('#modal-edit-entity'));
                    initSelectChoice();
                }
            }
        });
    });


}/*

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

}*/