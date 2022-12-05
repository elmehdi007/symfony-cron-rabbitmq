window.search = function search(gridIsNew = true, url = null, columns=[], idFormSearch = null, idTable="grid") {
    
    bodyFilters = {};
    if(idFormSearch != null) bodyFilters = $(idFormSearch).serializeArray();

    idTable = '#' + ((idTable.startsWith('#') === true)?idTable.replace('#', ''):idTable);
    if (gridIsNew != true || url === null) $(idTable).DataTable().ajax.reload(function({}){
                                              $('[data-toggle="tooltip"]').tooltip();
                                           }, true) ; 
    //if (gridIsNew != true || url === null) $(idTable).DataTable().clear().destroy() ; 

    else
    $(idTable).DataTable({
        processing: true,
        serverSide: true,
        dataType: "jsonp",
        responsive: true,
        bFilter: false,
        //language: 'fr',
        fnCreatedRow: function(row, data, dataIndex) {},
        initComplete: function(settings, json) {$('[data-toggle="tooltip"]').tooltip();},
        data: {filters: {}},
        order: [],
        ajax: {
            url: url,
            data: function(d) {
            body= {};
            d.filters = bodyFilters
        }
        },
        columns: columns
    });

    $(idTable).on('order.dt', function () {
        $('[data-toggle="tooltip"]').tooltip();
    } );

    return false;
}


window.downloadGrid = function downloadGrid(url, idFormSearch, doneCallback = null, failCallback = null, alwaysCallback = null){
    var method = "POST";

    bodyFilters = {};
    if(idFormSearch != null) bodyFilters = $(idFormSearch).serializeArray();

    $.ajax({
            url: url,
            method: method,
            //dataType : "json",
            data: {filters:bodyFilters, for_download:true},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        })
        .done(function(response, status, xhr){
            return doneCallback?doneCallback(response, xhr):{};
        })
        .fail(function(error){
            var txtError = "Quelque chose s'est mal passé";
            switch (error.status) {
                case 400:
                    txtError = "lien non trouvé";
                    break;
                case 401:
                    txtError = "non autorisé";
                    break;
                case 500:
                    txtError = "quelque chose s'est mal passé";
                case 403:
                    txtError = "l'accès est interdit";
                default:
                    txtError = "quelque chose s'est mal passé";
                    break;
            }
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: txtError
            })
            return failCallback?failCallback(error):{};
        })
        .always(function(){
            return alwaysCallback?alwaysCallback():{};
        });
}


window.callAjax = function callAjax(url,method="GET", body={}, doneCallback = null, failCallback = null, alwaysCallback = null){
    method = method.toUpperCase();
    if( method === "GET" || method === "POST" || method === "HEAD"  || method === "DELETE" )
    $.ajax({
            url: url,
            method: method,
            dataType : "json",
            data: body,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type' : "application/json",
            },
        })
        .done(function(response){
            return doneCallback?doneCallback(response):{};
        })
        .fail(function(error){
            var txtError = "Quelque chose s'est mal passé";
            switch (error.status) {
                case 400:
                    txtError = "lien non trouvé";
                    break;
                case 500:
                    txtError = "quelque chose s'est mal passé";
                case 403:
                    txtError = "l'accès est interdit";
                default:
                    txtError = "quelque chose s'est mal passé";
                    break;
            }
            console.log('error')
            return failCallback?failCallback(error):{};
        })
        .always(function(){
            return alwaysCallback?alwaysCallback():{};
        });
}

$("#btn-toggle-panel-search").click(function(event) {
    //
    //$(this).hasClass("fa-eye-slash")?$(this).replaceClass("fa-eye-slash",""):$(this).replaceClass("far fa-eye","fa-eye-slash");
    if($(this).hasClass("fa-eye-slash")){
        $(this).removeClass("fa-eye-slash")
        $(this).addClass("fa-eye")
    }

    else{
        $(this).removeClass("fa-eye")
        $(this).addClass("fa-eye-slash")
    }
});
