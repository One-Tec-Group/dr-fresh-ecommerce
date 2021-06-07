function addToCart(id) {
    var quantity = $('#input_' + id).val();
    var url = websiteUrl + '/cart/add';
    url += '/' + id + '/' + quantity;

    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            window.livewire.emit('added_product_to_cart');
        }
    });
}

function addOfferToCart(offerId) {
    var quantity = $('#offer_input_' + offerId).val();
    console.log(offerId);
    var url = websiteUrl + '/cart/addOffer';
    url += '/' + offerId + '/' + quantity;

    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            window.livewire.emit('added_product_to_cart');
        }
    });
}

function removeFromCart(id) {
    var url = websiteUrl + '/cart/remove';
    url += '/' + id;

    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            window.livewire.emit('deleted_product_from_cart');
        }
    });
}


function changeQuantity(type, id,category) {
var input =$('.input_'+category+'_' + id);
var value = parseInt(input.val());

    if(type == 'increase') {
        input.val(value +1) ;
    }
    if(type == 'decrease') {
        if(input.val() > 1){
            input.val(value -1) ;
        }


    }
}

// $('.increase').click(function () {
//     var inputId = $(this).attr('input');
//
//     var value = Number($('#input_' + inputId).val()) + 1;
//     $('#input_' + inputId).val(value);
// });
//
// $('.decrease').click(function () {
//     var inputId = $(this).attr('input');
//     var value = Number($('#input_' + inputId).val()) - 1;
//     if (value >= 1) {
//         $('#input_' + inputId).val(value);
//
//     }
// });


