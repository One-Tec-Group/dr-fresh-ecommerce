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

function increase(id) {
    var quantity = 1;
    var url = websiteUrl + '/cart/add';
    url += '/' + id + '/' + quantity;

    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            console.log(data);
            window.livewire.emit('added_product_to_cart');
        }
    });
}
function decrease(id) {
    var quantity = 1;
    var url = websiteUrl + '/cart/decrease';
    url += '/' + id + '/' + quantity;

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



