$(document).ready(function () {
    getproduct();

    $(".btn-filter").bind("click", function () {
        $("div#container").toggle();
        return false; // prevent propagation
    })
});


function getproduct(category = 0) {

    setactive(category)

    var url = 'getproducts/' + category;
    $.ajax({
        url: url,
        type: "GET",

        success: function (response) {
            if (response) {

                $('#products').html(response);
            }
        },
    });

}


function setactive(category) {

    $('.btn-filter').removeClass('active');
    if (category == 0) {
        $('#main').addClass('active');

    } else {
        $('#category_' + category).addClass('active');

    }
}


function get_search_result() {
    setactive(0);
    var search_data = document.getElementById("search_input").value;
    if (search_data) {


        var url = 'get_search_result/' + search_data;
        $.ajax({
            url: url,
            type: "GET",

            success: function (response) {
                if (response) {
                    $('#products').html(response);
                }
            },
        });
    }
    else {
        getproduct();
    }
}