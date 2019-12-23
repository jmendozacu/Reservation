/**
 * Created by hoang on 07/07/2016.
 */
require([
    "Magento_Ui/js/modal/modal",
    "jquery",
    "jquery/ui",
    "mage/calendar",
    'Magento_Ui/js/lib/view/utils/async'
], function (modal,
             $) {
    'use strict';
    $.async("#reservation_option_3_main_clickMe", function () {
        var id = $('div.reservation_fields_option_3').attr('data-mage');
        var clickMe = document.getElementById('reservation_option_3_main_clickMe');
        $(clickMe).click(function () {
            var product_price;
            var price;
            var priceClass = '.price';
            if(document.getElementById('price-including-tax-product-price-'+ id)){
                var arr = (((jQuery('#price-excluding-tax-product-price-'+ id+' .price').html()).split(" "))[1]).split(",");
                price = parseFloat(arr[0]+arr[1]);
            }else{
                product_price = document.getElementById('product-price-' + id);
                price = $(product_price).attr('data-price-amount');
            }
            var i = 0;
            var j = 0;
            var k = 0;
            var line = 0;
            var column = 0;
            var monthNames = [
                "January", "February", "March",
                "April", "May", "June", "July",
                "August", "September", "October",
                "November", "December"
            ];
            var timeTitle = document.getElementById('reservation_option_3_time_title');
            var previousMonthButton = document.getElementById('reservation_option_3_previous_month');
            var nextMonthButton = document.getElementById('reservation_option_3_next_month');
            var thisMonthTitle = document.getElementById('reservation_option_3_this_month');
            var currentPrice = document.getElementById('reservation_option_3_current_price');
            var myCart = document.getElementById('reservation_option_3_myCart');
            var numberOfSlotsNotify = document.getElementById('reservation_option_3_numberOfSlots_notify');
            var eventNotify = document.getElementById('reservation_option_3_event_notify');
            var inputSlots = document.getElementById('reservation_option_3_numberOfSlots');
            var cartTemp = document.getElementById('reservation_option_3_cart_temp');
            var cartAllData = document.getElementById('reservation_option_3_cart_allData');
            var slotsResponseArray = [];
            var canAddToCart = 0;
            var addToCartButton = document.getElementById('reservation_option_3_addToCart');
            var today = new Date();
            var todayMonth = today.getMonth();
            var currentMonth = 0;
            var todayYear = today.getFullYear();
            var currentYear = todayYear;
            var canAddToCartNow = 0;
            var cartString = '';
            switch (todayMonth) {
                case 0:
                    todayMonth = monthNames[0];
                    currentMonth = 0;
                    break;
                case 1:
                    todayMonth = monthNames[1];
                    currentMonth = 1;
                    break;
                case 2:
                    todayMonth = monthNames[2];
                    currentMonth = 2;
                    break;
                case 3:
                    todayMonth = monthNames[3];
                    currentMonth = 3;
                    break;
                case 4:
                    todayMonth = monthNames[4];
                    currentMonth = 4;
                    break;
                case 5:
                    todayMonth = monthNames[5];
                    currentMonth = 5;
                    break;
                case 6:
                    todayMonth = monthNames[6];
                    currentMonth = 6;
                    break;
                case 7:
                    todayMonth = monthNames[7];
                    currentMonth = 7;
                    break;
                case 8:
                    todayMonth = monthNames[8];
                    currentMonth = 8;
                    break;
                case 9:
                    todayMonth = monthNames[9];
                    currentMonth = 9;
                    break;
                case 10:
                    todayMonth = monthNames[10];
                    currentMonth = 10;
                    break;
                case 11:
                    todayMonth = monthNames[11];
                    currentMonth = 11;
                    break;
            }
            $(thisMonthTitle).text(todayMonth + ' ' + todayYear);
            var currentMonthTemp = currentMonth + 1;
            if (currentMonthTemp < 10) {
                currentMonthTemp = '0' + currentMonthTemp;
            }
            var firstDayThisMonth = new Date(currentMonthTemp + '/01/' + currentYear);
            var firstDayThisMonthDay = firstDayThisMonth.getDay(); // chu nhat =0

            if (firstDayThisMonthDay == 0) {
                firstDayThisMonthDay = 7;
            }
            for (i = 1; i <= 7; i++) {
                for (j = 1; j <= 7; j++) {
                    var dateTitleTemp = document.getElementById('reservation_option_3_date_' + i + j + '_title');
                    var dateTemp = document.getElementById('reservation_option_3_date_' + i + j);
                    $(dateTitleTemp).text('');
                    $(dateTemp).removeClass('past-month');
                    $(dateTemp).removeClass('curr-month');
                    $(dateTemp).addClass('past-month');
                }
            }
            var count = 1;
            i = 1;
            j = firstDayThisMonthDay;
            while (count <= new Date(currentYear, currentMonth + 1, 0).getDate()) {
                dateTitleTemp = document.getElementById('reservation_option_3_date_' + i + j + '_title');
                dateTemp = document.getElementById('reservation_option_3_date_' + i + j);
                $(dateTitleTemp).text(count);
                $(dateTemp).removeClass('past-month');
                $(dateTemp).removeClass('curr-month');
                $(dateTemp).removeClass('next-month');
                j++;
                if (j > 7) {
                    j = 1;
                    i++;
                }
                count++;
            }


            var data = {
                date: thisMonthTitle.innerHTML,
                product_id: id
            };
            $.ajax({
                url: 'reservation/product/time30',
                data: data,
                type: "POST",
                success: function (response) {
                    if (response.length > 2) {
                        var responseArray = JSON.parse(response);
                        for (i = 1; i <= new Date(currentYear, currentMonth + 1, 0).getDate(); i++) {
                            var dateAvailable = 0;
                            for (j = 0; j < responseArray.length; j++) {
                                if (i == responseArray[j]) {
                                    dateAvailable = 1;
                                }
                            }
                            if (dateAvailable == 1) {
                                column = i + firstDayThisMonthDay - 1;
                                line = 1;
                                while (column > 7) {
                                    column -= 7;
                                    line += 1;
                                }
                                dateTitleTemp = document.getElementById('reservation_option_3_date_' + line + column);
                                $(dateTitleTemp).removeClass('curr-month');
                                $(dateTitleTemp).addClass('next-month');
                                dateTitleTemp.value = i;
                                currentMonthTemp = currentMonth + 1;
                                $(dateTitleTemp).off('click');
                                $(dateTitleTemp).click(function () {
                                    var dateTemp = $(this).val() + '/' + currentMonthTemp + '/' + currentYear;
                                    var timeData = {
                                        product_id: id,
                                        product_price: price,
                                        date: dateTemp
                                    };
                                    $(timeTitle).text(dateTemp);
                                    $.ajax({
                                        url: 'reservation/product/time31',
                                        data: timeData,
                                        type: 'POST',
                                        success: function (slotsRespond) {
                                            if ($(cartAllData).val().length > 0 && $(cartAllData).val().indexOf(slotsRespond) === -1) {
                                                cartAllData.value = $(cartAllData).val() + '|' + slotsRespond;
                                            } else if ($(cartAllData).val().length === 0) {
                                                cartAllData.value = $(cartAllData).val() + '|' + slotsRespond;
                                            }
                                            slotsResponseArray = JSON.parse(slotsRespond);
                                            $(eventNotify).text(slotsResponseArray[0]['event_name']);
                                            $(numberOfSlotsNotify).text(slotsResponseArray[0]['slots']);
                                            inputSlots.max = slotsResponseArray[0]['slots'];
                                            $(currentPrice).text(slotsResponseArray[0]['symbol'] + slotsResponseArray[0]['event_amount']);
                                            $(addToCartButton).off("click");
                                            $(addToCartButton).click(function () {
                                                var cartAdded = 0;
                                                cartString = $(cartTemp).val();
                                                if (cartString.length > 1) {
                                                    var cartArray = cartString.split(";");
                                                    for (k = 1; k < cartArray.length; k++) {
                                                        var cartItem = cartArray[k];
                                                        var cartItemArray = cartItem.split(",");
                                                        if (timeTitle.innerHTML == cartItemArray[0] && numberOfSlotsNotify.innerHTML == cartItemArray[1]) {
                                                            cartAdded = 1;
                                                        }
                                                    }
                                                }
                                                if (cartAdded == 0) {
                                                    var orderAdded = document.getElementById('reservation_option_3_cart_item_' + timeTitle.innerHTML);
                                                    var finalCartTemp = "";
                                                    if (orderAdded != null) {
                                                        cartArray = cartString.split(";");
                                                        var slotNumTemp = 0;
                                                        var cartItemInfoTemp = document.getElementById('reservation_option_3_cart_item_info_' + timeTitle.innerHTML);
                                                        for (k = 1; k < cartArray.length; k++) {
                                                            cartItem = cartArray[k];
                                                            cartItemArray = cartItem.split(",");
                                                            if (timeTitle.innerHTML == cartItemArray[0]) {
                                                                slotNumTemp = parseInt(cartItemArray[1]) + parseInt($(inputSlots).val());
                                                                if (slotNumTemp > parseInt(numberOfSlotsNotify.innerHTML)) {
                                                                    slotNumTemp = parseInt(numberOfSlotsNotify.innerHTML);
                                                                }
                                                                cartItemArray[1] = slotNumTemp;
                                                                var cartItemDelete = document.getElementById('reservation_option_3_cart_item_delete_' + timeTitle.innerHTML);
                                                                cartItemDelete.value = ';' + cartItemArray[0] + ',' + cartItemArray[1];
                                                            }
                                                            finalCartTemp += ';' + cartItemArray[0] + ',' + cartItemArray[1];
                                                        }
                                                        cartTemp.value = finalCartTemp;
                                                        var currentPriceTemp = Number(slotNumTemp) * parseFloat(slotsResponseArray[0]['event_amount']);
                                                        currentPriceTemp = currentPriceTemp.toFixed(2);
                                                        $(cartItemInfoTemp).text(slotNumTemp + ' slot(s) - ' + slotsResponseArray[0]['symbol'] + currentPriceTemp);
                                                    } else {
                                                        var inputSlotsVal = $(inputSlots).val();
                                                        var currentPriceVal = Number(inputSlotsVal) * parseFloat(slotsResponseArray[0]['event_amount']);
                                                        if (Number(inputSlotsVal) > Number(slotsResponseArray[0]['slots'])) inputSlotsVal = slotsResponseArray[0]['slots'];
                                                        var newCartItem = '<li id="reservation_option_3_cart_item_' + timeTitle.innerHTML + '"><div class="title">' + timeTitle.innerHTML + '</div>';
                                                        newCartItem += '<p id="reservation_option_3_cart_item_info_' + timeTitle.innerHTML + '">' + inputSlotsVal + ' slot(s) - ' + slotsResponseArray[0]['symbol'] + currentPriceVal + '</p>';
                                                        newCartItem += '<button id="reservation_option_3_cart_item_delete_' + timeTitle.innerHTML + '" value="' + ';' + timeTitle.innerHTML + ',' + inputSlotsVal + '" type="button" class="action-remove"></button></li>';
                                                        $(myCart).append(newCartItem);
                                                        cartTemp.value = $(cartTemp).val() + ';' + timeTitle.innerHTML + ',' + inputSlotsVal;
                                                    }
                                                }
                                                var deleteButtonList = $(myCart).find('button');
                                                $(deleteButtonList).each(function (index, element) {
                                                    $(element).click(function () {
                                                        var cartValue = $(cartTemp).val();
                                                        cartValue = cartValue.replace($(this).val(), '');
                                                        cartTemp.value = cartValue;
                                                        $(this).closest('li').remove();
                                                    });
                                                });
                                            });
                                        },
                                        showLoader: true
                                    });

                                });
                            }
                        }
                    }
                },
                showLoader: true
            });


            $(previousMonthButton).click(function () {
                currentMonth--;
                if (currentMonth == -1) {
                    currentMonth = 11;
                    currentYear--;
                }
                $(thisMonthTitle).text(monthNames[currentMonth] + ' ' + currentYear);
                currentMonthTemp = currentMonth + 1;
                var firstDayThisMonth = new Date(currentMonthTemp + '/01/' + currentYear);
                firstDayThisMonthDay = firstDayThisMonth.getDay();
                if (firstDayThisMonthDay == 0) {
                    firstDayThisMonthDay = 7;
                }
                for (i = 1; i <= 7; i++) {
                    for (j = 1; j <= 7; j++) {
                        var dateTitleTemp = document.getElementById('reservation_option_3_date_' + i + j + '_title');
                        dateTemp = document.getElementById('reservation_option_3_date_' + i + j);
                        $(dateTitleTemp).text('');
                        $(dateTemp).removeClass('past-month');
                        $(dateTemp).removeClass('curr-month');
                        $(dateTemp).removeClass('next-month');
                        $(dateTemp).addClass('past-month');
                        $(dateTemp).off('click');
                    }
                }
                var count = 1;
                i = 1;
                j = firstDayThisMonthDay;
                while (count <= new Date(currentYear, currentMonth + 1, 0).getDate()) {
                    dateTitleTemp = document.getElementById('reservation_option_3_date_' + i + j + '_title');
                    dateTemp = document.getElementById('reservation_option_3_date_' + i + j);
                    $(dateTitleTemp).text(count);
                    $(dateTemp).removeClass('past-month');
                    $(dateTemp).removeClass('curr-month');
                    j++;
                    if (j > 7) {
                        j = 1;
                        i++;
                    }
                    count++;
                }
                var data = {
                    date: thisMonthTitle.innerHTML,
                    product_id: id
                };
                $.ajax({
                    url: 'reservation/product/time30',
                    data: data,
                    type: 'POST',
                    success: function (response) {
                        if (response.length > 2) {
                            var responseArray = JSON.parse(response);
                            for (i = 1; i <= new Date(currentYear, currentMonth + 1, 0).getDate(); i++) {
                                var dateAvailable = 0;
                                for (j = 0; j < responseArray.length; j++) {
                                    if (i == responseArray[j]) {
                                        dateAvailable = 1;
                                    }
                                }
                                if (dateAvailable == 1) {
                                    column = i + firstDayThisMonthDay - 1;
                                    line = 1;
                                    while (column > 7) {
                                        column -= 7;
                                        line += 1;
                                    }
                                    dateTitleTemp = document.getElementById('reservation_option_3_date_' + line + column);
                                    $(dateTitleTemp).removeClass('curr-month');
                                    $(dateTitleTemp).addClass('next-month');
                                    dateTitleTemp.value = i;
                                    currentMonthTemp = currentMonth + 1;
                                    $(dateTitleTemp).off('click');
                                    $(dateTitleTemp).click(function () {
                                        var dateTemp = $(this).val() + '/' + currentMonthTemp + '/' + currentYear;
                                        var timeData = {
                                            product_id: id,
                                            product_price: price,
                                            date: dateTemp
                                        };
                                        $(timeTitle).text(dateTemp);
                                        $.ajax({
                                            url: 'reservation/product/time31',
                                            data: timeData,
                                            type: 'POST',
                                            success: function (slotsRespond) {
                                                if ($(cartAllData).val().length > 0 && $(cartAllData).val().indexOf(slotsRespond) === -1) {
                                                    cartAllData.value = $(cartAllData).val() + '|' + slotsRespond;
                                                } else if ($(cartAllData).val().length === 0) {
                                                    cartAllData.value = $(cartAllData).val() + '|' + slotsRespond;
                                                }
                                                slotsResponseArray = JSON.parse(slotsRespond);
                                                $(eventNotify).text(slotsResponseArray[0]['event_name']);
                                                $(numberOfSlotsNotify).text(slotsResponseArray[0]['slots']);
                                                inputSlots.max = slotsResponseArray[0]['slots'];
                                                $(currentPrice).text(slotsResponseArray[0]['symbol'] + slotsResponseArray[0]['event_amount']);
                                                $(addToCartButton).off("click");
                                                $(addToCartButton).click(function () {
                                                    var cartAdded = 0;
                                                    cartString = $(cartTemp).val();
                                                    if (cartString.length > 1) {
                                                        var cartArray = cartString.split(";");
                                                        for (k = 1; k < cartArray.length; k++) {
                                                            var cartItem = cartArray[k];
                                                            var cartItemArray = cartItem.split(",");
                                                            if (timeTitle.innerHTML == cartItemArray[0] && numberOfSlotsNotify.innerHTML == cartItemArray[1]) {
                                                                cartAdded = 1;
                                                            }
                                                        }
                                                    }
                                                    if (cartAdded == 0) {
                                                        var orderAdded = document.getElementById('reservation_option_3_cart_item_' + timeTitle.innerHTML);
                                                        var finalCartTemp = "";
                                                        if (orderAdded != null) {
                                                            cartArray = cartString.split(";");
                                                            var slotNumTemp = 0;
                                                            var cartItemInfoTemp = document.getElementById('reservation_option_3_cart_item_info_' + timeTitle.innerHTML);
                                                            for (k = 1; k < cartArray.length; k++) {
                                                                cartItem = cartArray[k];
                                                                cartItemArray = cartItem.split(",");
                                                                if (timeTitle.innerHTML == cartItemArray[0]) {
                                                                    slotNumTemp = parseInt(cartItemArray[1]) + parseInt($(inputSlots).val());
                                                                    if (slotNumTemp > parseInt(numberOfSlotsNotify.innerHTML)) {
                                                                        slotNumTemp = parseInt(numberOfSlotsNotify.innerHTML);
                                                                    }
                                                                    cartItemArray[1] = slotNumTemp;
                                                                    var cartItemDelete = document.getElementById('reservation_option_3_cart_item_delete_' + timeTitle.innerHTML);
                                                                    cartItemDelete.value = ';' + cartItemArray[0] + ',' + cartItemArray[1];
                                                                }
                                                                finalCartTemp += ';' + cartItemArray[0] + ',' + cartItemArray[1];
                                                            }
                                                            cartTemp.value = finalCartTemp;
                                                            var currentPriceTemp = Number(slotNumTemp) * parseFloat(slotsResponseArray[0]['event_amount']);
                                                            currentPriceTemp = currentPriceTemp.toFixed(2);
                                                            $(cartItemInfoTemp).text(slotNumTemp + ' slot(s) - ' + slotsResponseArray[0]['symbol'] + currentPriceTemp);
                                                        } else {
                                                            var inputSlotsVal = $(inputSlots).val();
                                                            var currentPriceVal = Number(inputSlotsVal) * parseFloat(slotsResponseArray[0]['event_amount']);
                                                            if (Number(inputSlotsVal) > Number(slotsResponseArray[0]['slots'])) inputSlotsVal = slotsResponseArray[0]['slots'];
                                                            var newCartItem = '<li id="reservation_option_3_cart_item_' + timeTitle.innerHTML + '"><div class="title">' + timeTitle.innerHTML + '</div>';
                                                            newCartItem += '<p id="reservation_option_3_cart_item_info_' + timeTitle.innerHTML + '">' + inputSlotsVal + ' slot(s) - ' + slotsResponseArray[0]['symbol'] + currentPriceVal + '</p>';
                                                            newCartItem += '<button id="reservation_option_3_cart_item_delete_' + timeTitle.innerHTML + '" value="' + ';' + timeTitle.innerHTML + ',' + inputSlotsVal + '" type="button" class="action-remove"></button></li>';
                                                            $(myCart).append(newCartItem);
                                                            cartTemp.value = $(cartTemp).val() + ';' + timeTitle.innerHTML + ',' + inputSlotsVal;
                                                        }
                                                    }
                                                    var deleteButtonList = $(myCart).find('button');
                                                    $(deleteButtonList).each(function (index, element) {
                                                        $(element).click(function () {
                                                            var cartValue = $(cartTemp).val();
                                                            cartValue = cartValue.replace($(this).val(), '');
                                                            cartTemp.value = cartValue;
                                                            $(this).closest('li').remove();
                                                        });
                                                    });
                                                });
                                            },
                                            showLoader: true
                                        });
                                    });
                                }
                            }
                        }
                    }, showLoader: true
                });
            });

            $(nextMonthButton).click(function () {
                currentMonth++;
                if (currentMonth == 12) {
                    currentMonth = 0;
                    currentYear++;
                }
                $(thisMonthTitle).text(monthNames[currentMonth] + ' ' + currentYear);
                currentMonthTemp = currentMonth + 1;
                var firstDayThisMonth = new Date(currentMonthTemp + '/01/' + currentYear);
                firstDayThisMonthDay = firstDayThisMonth.getDay();
                if (firstDayThisMonthDay == 0) {
                    firstDayThisMonthDay = 7;
                }
                for (i = 1; i <= 7; i++) {
                    for (j = 1; j <= 7; j++) {
                        var dateTitleTemp = document.getElementById('reservation_option_3_date_' + i + j + '_title');
                        dateTemp = document.getElementById('reservation_option_3_date_' + i + j);
                        $(dateTitleTemp).text('');
                        $(dateTemp).removeClass('past-month');
                        $(dateTemp).removeClass('curr-month');
                        $(dateTemp).removeClass('next-month');
                        $(dateTemp).addClass('past-month');
                        $(dateTemp).off('click');
                    }
                }
                var count = 1;
                i = 1;
                j = firstDayThisMonthDay;
                while (count <= new Date(currentYear, currentMonth + 1, 0).getDate()) {
                    dateTitleTemp = document.getElementById('reservation_option_3_date_' + i + j + '_title');
                    dateTemp = document.getElementById('reservation_option_3_date_' + i + j);
                    $(dateTitleTemp).text(count);
                    $(dateTemp).removeClass('past-month');
                    $(dateTemp).removeClass('curr-month');
                    j++;
                    if (j > 7) {
                        j = 1;
                        i++;
                    }
                    count++;
                }
                var data = {
                    date: thisMonthTitle.innerHTML,
                    product_id: id
                };
                $.ajax({
                    url: 'reservation/product/time30',
                    data: data,
                    type: 'POST',
                    success: function (response) {
                        if (response.length > 2) {
                            var responseArray = JSON.parse(response);
                            for (i = 1; i <= new Date(currentYear, currentMonth + 1, 0).getDate(); i++) {
                                var dateAvailable = 0;
                                for (j = 0; j < responseArray.length; j++) {
                                    if (i == responseArray[j]) {
                                        dateAvailable = 1;
                                    }
                                }
                                if (dateAvailable == 1) {
                                    column = i + firstDayThisMonthDay - 1;
                                    line = 1;
                                    while (column > 7) {
                                        column -= 7;
                                        line += 1;
                                    }
                                    dateTitleTemp = document.getElementById('reservation_option_3_date_' + line + column);
                                    $(dateTitleTemp).removeClass('curr-month');
                                    $(dateTitleTemp).addClass('next-month');
                                    dateTitleTemp.value = i;
                                    currentMonthTemp = currentMonth + 1;
                                    $(dateTitleTemp).off('click');
                                    $(dateTitleTemp).click(function () {
                                        var dateTemp = $(this).val() + '/' + currentMonthTemp + '/' + currentYear;
                                        var timeData = {
                                            product_id: id,
                                            product_price: price,
                                            date: dateTemp
                                        };
                                        $(timeTitle).text(dateTemp);
                                        $.ajax({
                                            url: 'reservation/product/time31',
                                            data: timeData,
                                            type: 'POST',
                                            success: function (slotsRespond) {
                                                if ($(cartAllData).val().length > 0 && $(cartAllData).val().indexOf(slotsRespond) === -1) {
                                                    cartAllData.value = $(cartAllData).val() + '|' + slotsRespond;
                                                } else if ($(cartAllData).val().length === 0) {
                                                    cartAllData.value = $(cartAllData).val() + '|' + slotsRespond;
                                                }
                                                slotsResponseArray = JSON.parse(slotsRespond);
                                                $(eventNotify).text(slotsResponseArray[0]['event_name']);
                                                $(numberOfSlotsNotify).text(slotsResponseArray[0]['slots']);
                                                inputSlots.max = slotsResponseArray[0]['slots'];
                                                $(currentPrice).text(slotsResponseArray[0]['symbol'] + slotsResponseArray[0]['event_amount']);
                                                $(addToCartButton).off("click");
                                                $(addToCartButton).click(function () {
                                                    var cartAdded = 0;
                                                    cartString = $(cartTemp).val();
                                                    if (cartString.length > 1) {
                                                        var cartArray = cartString.split(";");
                                                        for (k = 1; k < cartArray.length; k++) {
                                                            var cartItem = cartArray[k];
                                                            var cartItemArray = cartItem.split(",");
                                                            if (timeTitle.innerHTML == cartItemArray[0] && numberOfSlotsNotify.innerHTML == cartItemArray[1]) {
                                                                cartAdded = 1;
                                                            }
                                                        }
                                                    }
                                                    if (cartAdded == 0) {
                                                        var orderAdded = document.getElementById('reservation_option_3_cart_item_' + timeTitle.innerHTML);
                                                        var finalCartTemp = "";
                                                        if (orderAdded != null) {
                                                            cartArray = cartString.split(";");
                                                            var slotNumTemp = 0;
                                                            var cartItemInfoTemp = document.getElementById('reservation_option_3_cart_item_info_' + timeTitle.innerHTML);
                                                            for (k = 1; k < cartArray.length; k++) {
                                                                cartItem = cartArray[k];
                                                                cartItemArray = cartItem.split(",");
                                                                if (timeTitle.innerHTML == cartItemArray[0]) {
                                                                    slotNumTemp = parseInt(cartItemArray[1]) + parseInt($(inputSlots).val());
                                                                    if (slotNumTemp > parseInt(numberOfSlotsNotify.innerHTML)) {
                                                                        slotNumTemp = parseInt(numberOfSlotsNotify.innerHTML);
                                                                    }
                                                                    cartItemArray[1] = slotNumTemp;
                                                                    var cartItemDelete = document.getElementById('reservation_option_3_cart_item_delete_' + timeTitle.innerHTML);
                                                                    cartItemDelete.value = ';' + cartItemArray[0] + ',' + cartItemArray[1];
                                                                }
                                                                finalCartTemp += ';' + cartItemArray[0] + ',' + cartItemArray[1];
                                                            }
                                                            cartTemp.value = finalCartTemp;
                                                            var currentPriceTemp = Number(slotNumTemp) * parseFloat(slotsResponseArray[0]['event_amount']);
                                                            currentPriceTemp = currentPriceTemp.toFixed(2);
                                                            $(cartItemInfoTemp).text(slotNumTemp + ' slot(s) - ' + slotsResponseArray[0]['symbol'] + currentPriceTemp);
                                                        } else {
                                                            var inputSlotsVal = $(inputSlots).val();
                                                            var currentPriceVal = Number(inputSlotsVal) * parseFloat(slotsResponseArray[0]['event_amount']);
                                                            if (Number(inputSlotsVal) > Number(slotsResponseArray[0]['slots'])) inputSlotsVal = slotsResponseArray[0]['slots'];
                                                            var newCartItem = '<li id="reservation_option_3_cart_item_' + timeTitle.innerHTML + '"><div class="title">' + timeTitle.innerHTML + '</div>';
                                                            newCartItem += '<p id="reservation_option_3_cart_item_info_' + timeTitle.innerHTML + '">' + inputSlotsVal + ' slot(s) - ' + slotsResponseArray[0]['symbol'] + currentPriceVal + '</p>';
                                                            newCartItem += '<button id="reservation_option_3_cart_item_delete_' + timeTitle.innerHTML + '" value="' + ';' + timeTitle.innerHTML + ',' + inputSlotsVal + '" type="button" class="action-remove"></button></li>';
                                                            $(myCart).append(newCartItem);
                                                            cartTemp.value = $(cartTemp).val() + ';' + timeTitle.innerHTML + ',' + inputSlotsVal;

                                                        }
                                                    }
                                                    var deleteButtonList = $(myCart).find('button');
                                                    $(deleteButtonList).each(function (index, element) {
                                                        $(element).click(function () {
                                                            var cartValue = $(cartTemp).val();
                                                            cartValue = cartValue.replace($(this).val(), '');
                                                            cartTemp.value = cartValue;
                                                            $(this).closest('li').remove();
                                                        });
                                                    });
                                                });
                                            },
                                            showLoader: true
                                        });
                                    });
                                }
                            }
                        }
                    }, showLoader: true
                });
            });
            var options = {
                type: 'popup', responsive: true, innerScroll: true, title: $.mage.__('Reservation')
            };
            options.buttons = [
                {
                    text: $.mage.__('Cancel'),
                    class: 'action secondary action-hide-popup',
                    click: function () {
                        $(cartTemp).val("");
                        this.closeModal();
                    }
                },
                {
                    text: $.mage.__('Save Selection'),
                    class: 'action primary action-save-address',
                    click: function () {
                        var finalSchedule = document.getElementById('reservation_option_3_final_schedule');
                        var finalScheduleData = [];
                        if ($(cartTemp).val().length > 0) {
                            count = 0;
                            finalScheduleData[count] = 3;
                            count = count + 1;
                            finalScheduleData[count] = $(cartAllData).val();
                            count = count + 1;
                            finalScheduleData[count] = $(cartTemp).val();
                            finalSchedule.value = JSON.stringify(finalScheduleData);
                        }
                        var addToCartButtonMagento = document.getElementById('product-addtocart-button');
                        $(addToCartButtonMagento).click();
                        $(cartTemp).val("");
                        this.closeModal();
                    }
                }
            ];
            var popup = modal(options, $('#reservation_option_3_popup'));
            $('#reservation_option_3_popup').modal('openModal');
        });
    });
});