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
    $.async("#reservation_option_1_main_clickMe,#reservation_date_option_2,#reservation_option_3_main_clickMe,#reservation_date_option_0", function () {
        var id = $('div.reservation_fields_option_1').attr('data-mage');
        var clickMe = document.getElementById('reservation_option_1_main_clickMe');
        var addToCartButton = document.getElementById('reservation_option_0_popup');
        var addToCartButtonMagentoDisplay;
        if (addToCartButton != null) {
            addToCartButtonMagentoDisplay = document.getElementById('product-addtocart-button');
            addToCartButtonMagentoDisplay.style.visibility = 'hidden';
            $(document.getElementsByClassName('field qty')).remove();
        }
        addToCartButton = document.getElementById('reservation_option_1_popup');
        if (addToCartButton != null) {
            addToCartButtonMagentoDisplay = document.getElementById('product-addtocart-button');
            addToCartButtonMagentoDisplay.style.visibility = 'hidden';
            $(document.getElementsByClassName('field qty')).remove();
        }
        addToCartButton = document.getElementById('reservation_option_2_popup');
        if (addToCartButton != null) {
            addToCartButtonMagentoDisplay = document.getElementById('product-addtocart-button');
            addToCartButtonMagentoDisplay.style.visibility = 'hidden';
            $(document.getElementsByClassName('field qty')).remove();
        }
        addToCartButton = document.getElementById('reservation_option_3_popup');
        if (addToCartButton != null) {
            addToCartButtonMagentoDisplay = document.getElementById('product-addtocart-button');
            addToCartButtonMagentoDisplay.style.visibility = 'hidden';
            $(document.getElementsByClassName('field qty')).remove();
        }


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
            var line = 0;
            var column = 0
            var monthNames = [
                "January", "February", "March",
                "April", "May", "June", "July",
                "August", "September", "October",
                "November", "December"
            ];
            addToCartButton = document.getElementById('reservation_option_1_addToCart');
            var previousMonthButton = document.getElementById('reservation_option_1_previous_month');
            var nextMonthButton = document.getElementById('reservation_option_1_next_month');
            var thisMonthTitle = document.getElementById('reservation_option_1_this_month');
            var staffListTitle = document.getElementById('reservation_option_1_staff_list_title');
            var eventNotify = document.getElementById('reservation_option_1_event_notify');
            var staffList = document.getElementById('reservation_option_1_staff_list');
            var staffName = document.getElementById('reservation_option_1_staff_name');
            var staffIntro = document.getElementById('reservation_option_1_staff_intro');
            var staffAvatar = document.getElementById('reservation_option_1_staff_avatar');
            var currentPrice = document.getElementById('reservation_option_1_current_price');
            var staffTemp = document.getElementById('reservation_option_1_staff_temp');
            var myCart = document.getElementById('reservation_option_1_myCart');
            var canAddToCart = 0;
            var cartTempInput = document.getElementById('reservation_option_1_cart_temp');
            var today = new Date();
            var todayMonth = today.getMonth();
            var currentMonth = 0;
            var todayYear = today.getFullYear();
            var currentYear = todayYear;
            var canAddToCartNow = 0;
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
                    var dateTitleTemp = document.getElementById('reservation_option_1_date_' + i + j + '_title');
                    var dateTemp = document.getElementById('reservation_option_1_date_' + i + j);
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
                dateTitleTemp = document.getElementById('reservation_option_1_date_' + i + j + '_title');
                dateTemp = document.getElementById('reservation_option_1_date_' + i + j);
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
                url: 'reservation/product/time10',
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
                                dateTitleTemp = document.getElementById('reservation_option_1_date_' + line + column);
                                $(dateTitleTemp).removeClass('curr-month');
                                $(dateTitleTemp).addClass('next-month');
                                dateTitleTemp.value = i;
                                currentMonthTemp = currentMonth + 1;
                                $(dateTitleTemp).click(function () {
                                    var dateTemp = $(this).val() + '/' + currentMonthTemp + '/' + currentYear;
                                    var staffData = {
                                        product_id: id,
                                        product_price: price,
                                        date: dateTemp
                                    };
                                    $(staffListTitle).text(dateTemp);
                                    $.ajax({
                                        url: 'reservation/product/time11',
                                        data: staffData,
                                        type: 'POST',
                                        success: function (staffRespond) {
                                            var staffResponseArray = JSON.parse(staffRespond);
                                            for (j = 0; j < staffResponseArray.length; j++) {
                                                staffTemp.value = 0;

                                                var staffListElement = $(staffList).find('option');
                                                $(staffListElement).each(function (index, element) {
                                                    $(element).remove();
                                                });
                                                var selected = 'selected="selected"';
                                                for (j = 0; j < staffResponseArray.length; j++) {
                                                    var newStaffItem = '<option ' + selected + ' value="' + j + '">' + staffResponseArray[j]['staff_name'] + '</option>';
                                                    $(staffList).append(newStaffItem);
                                                    selected = '';
                                                }
                                                $(eventNotify).text(staffResponseArray[0]['event_name']);
                                                $(staffIntro).text(staffResponseArray[0]['staff_intro']);
                                                $(staffAvatar).attr("src", staffResponseArray[0]['staff_avatar']);
                                                $(staffName).text(staffResponseArray[0]['staff_name']);
                                                $(currentPrice).text(staffResponseArray[$(staffTemp).val()]['symbol'] + staffResponseArray[0]['event_amount']);
                                                $(staffList).change(function () {
                                                    $(eventNotify).text(staffResponseArray[$(staffTemp).val()]['event_name']);
                                                    $(staffIntro).text(staffResponseArray[$(staffTemp).val()]['staff_intro']);
                                                    $(staffAvatar).attr("src", staffResponseArray[$(staffTemp).val()]['staff_avatar']);
                                                    $(staffName).text(staffResponseArray[$(staffTemp).val()]['staff_name']);
                                                    $(currentPrice).text(staffResponseArray[$(staffTemp).val()]['symbol'] + staffResponseArray[$(staffTemp).val()]['event_amount']);
                                                    staffTemp.value = $(this).val();
                                                });
                                                canAddToCart = 1;
                                                $(addToCartButton).off("click");
                                                $(addToCartButton).click(function () {
                                                    var cartTempInputVal = $(cartTempInput).val();
                                                    if (canAddToCart == 1) {
                                                        canAddToCart = 0;
                                                        canAddToCartNow = 1;
                                                        if (cartTempInputVal.length > 1) {
                                                            if (cartTempInputVal.indexOf(JSON.stringify(staffResponseArray[$(staffTemp).val()])) > -1) {
                                                                canAddToCartNow = 0;
                                                            }
                                                        }
                                                        if (canAddToCartNow == 1) {
                                                            var newCartItem = '<li><span>' + staffListTitle.innerHTML;
                                                            var deleteButtonVal = '|' + JSON.stringify(staffResponseArray[$(staffTemp).val()]);
                                                            newCartItem += '<button value="' + staffResponseArray[$(staffTemp).val()]['date'];
                                                            newCartItem += staffResponseArray[$(staffTemp).val()]['staff_id'] + '" type="button" class="action-remove"><span>delete</span></button><br>';
                                                            newCartItem += '<span>' + staffName.innerHTML + ' - ' + currentPrice.innerHTML + '</span><span id="' + staffResponseArray[$(staffTemp).val()]['date'] + staffResponseArray[$(staffTemp).val()]['staff_id'] + '" style="display: none">' + deleteButtonVal + '</span></span></li>';
                                                            $(myCart).append(newCartItem);
                                                            cartTempInput.value = $(cartTempInput).val() + '|' + JSON.stringify(staffResponseArray[$(staffTemp).val()]);
                                                        }
                                                        canAddToCart = 1;
                                                    }
                                                    var deleteButtonList = $(myCart).find('button');
                                                    $(deleteButtonList).each(function (index, element) {
                                                        $(element).click(function () {
                                                            var cartValue = $(cartTempInput).val();
                                                            var closestSpan = document.getElementById($(this).val());
                                                            if (closestSpan != null) cartValue = cartValue.replace(closestSpan.innerHTML, '');
                                                            cartTempInput.value = cartValue;
                                                            $(this).closest('li').remove();
                                                        });
                                                    });
                                                });
                                            }
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
                        var dateTitleTemp = document.getElementById('reservation_option_1_date_' + i + j + '_title');
                        dateTemp = document.getElementById('reservation_option_1_date_' + i + j);
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
                    dateTitleTemp = document.getElementById('reservation_option_1_date_' + i + j + '_title');
                    dateTemp = document.getElementById('reservation_option_1_date_' + i + j);
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
                    url: 'reservation/product/time10',
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
                                    dateTitleTemp = document.getElementById('reservation_option_1_date_' + line + column);
                                    $(dateTitleTemp).removeClass('curr-month');
                                    $(dateTitleTemp).addClass('next-month');
                                    dateTitleTemp.value = i;
                                    currentMonthTemp = currentMonth + 1;
                                    $(dateTitleTemp).off('click');
                                    $(dateTitleTemp).click(function () {
                                        var dateTemp = $(this).val() + '/' + currentMonthTemp + '/' + currentYear;
                                        var staffData = {
                                            product_id: id,
                                            product_price: price,
                                            date: dateTemp
                                        };
                                        $(staffListTitle).text(dateTemp);
                                        $.ajax({
                                            url: 'reservation/product/time11',
                                            data: staffData,
                                            type: 'POST',
                                            success: function (staffRespond) {
                                                var staffResponseArray = JSON.parse(staffRespond);
                                                for (j = 0; j < staffResponseArray.length; j++) {
                                                    staffTemp.value = 0;

                                                    var staffListElement = $(staffList).find('option');
                                                    $(staffListElement).each(function (index, element) {
                                                        $(element).remove();
                                                    });
                                                    var selected = 'selected="selected"';
                                                    for (j = 0; j < staffResponseArray.length; j++) {
                                                        var newStaffItem = '<option ' + selected + ' value="' + j + '">' + staffResponseArray[j]['staff_name'] + '</option>';
                                                        $(staffList).append(newStaffItem);
                                                        selected = '';
                                                    }
                                                    $(staffIntro).text(staffResponseArray[0]['staff_intro']);
                                                    $(staffAvatar).attr("src", staffResponseArray[0]['staff_avatar']);
                                                    $(staffName).text(staffResponseArray[0]['staff_name']);
                                                    $(currentPrice).text(staffResponseArray[$(staffTemp).val()]['symbol'] + staffResponseArray[0]['event_amount']);
                                                    $(staffList).change(function () {
                                                        $(staffIntro).text(staffResponseArray[$(staffTemp).val()]['staff_intro']);
                                                        $(staffAvatar).attr("src", staffResponseArray[$(staffTemp).val()]['staff_avatar']);
                                                        $(staffName).text(staffResponseArray[$(staffTemp).val()]['staff_name']);
                                                        $(currentPrice).text(staffResponseArray[$(staffTemp).val()]['symbol'] + staffResponseArray[$(staffTemp).val()]['event_amount']);
                                                        staffTemp.value = $(this).val();
                                                    });
                                                    canAddToCart = 1;
                                                    $(addToCartButton).off("click");
                                                    $(addToCartButton).click(function () {
                                                        var cartTempInputVal = $(cartTempInput).val();
                                                        if (canAddToCart == 1) {
                                                            canAddToCart = 0;
                                                            canAddToCartNow = 1;
                                                            if (cartTempInputVal.length > 1) {
                                                                if (cartTempInputVal.indexOf(JSON.stringify(staffResponseArray[$(staffTemp).val()])) > -1) {
                                                                    canAddToCartNow = 0;
                                                                }
                                                            }
                                                            if (canAddToCartNow == 1) {
                                                                var newCartItem = '<li><span>' + staffListTitle.innerHTML;
                                                                var deleteButtonVal = '|' + JSON.stringify(staffResponseArray[$(staffTemp).val()]);
                                                                newCartItem += '<button value="' + staffResponseArray[$(staffTemp).val()]['date'];
                                                                newCartItem += staffResponseArray[$(staffTemp).val()]['staff_id'] + '" type="button" class="action-remove"><span>delete</span></button><br>';
                                                                newCartItem += '<span>' + staffName.innerHTML + ' - ' + currentPrice.innerHTML + '</span><span id="' + staffResponseArray[$(staffTemp).val()]['date'] + staffResponseArray[$(staffTemp).val()]['staff_id'] + '" style="display: none">' + deleteButtonVal + '</span></span></li>';
                                                                $(myCart).append(newCartItem);
                                                                cartTempInput.value = $(cartTempInput).val() + '|' + JSON.stringify(staffResponseArray[$(staffTemp).val()]);
                                                            }
                                                            canAddToCart = 1;
                                                        }
                                                        var deleteButtonList = $(myCart).find('button');
                                                        $(deleteButtonList).each(function (index, element) {
                                                            $(element).click(function () {
                                                                var cartValue = $(cartTempInput).val();
                                                                var closestSpan = document.getElementById($(this).val());
                                                                if (closestSpan != null) cartValue = cartValue.replace(closestSpan.innerHTML, '');
                                                                cartTempInput.value = cartValue;
                                                                $(this).closest('li').remove();
                                                            });
                                                        });
                                                    });
                                                }
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
                        var dateTitleTemp = document.getElementById('reservation_option_1_date_' + i + j + '_title');
                        dateTemp = document.getElementById('reservation_option_1_date_' + i + j);
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
                    dateTitleTemp = document.getElementById('reservation_option_1_date_' + i + j + '_title');
                    dateTemp = document.getElementById('reservation_option_1_date_' + i + j);
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
                    url: 'reservation/product/time10',
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
                                    dateTitleTemp = document.getElementById('reservation_option_1_date_' + line + column);
                                    $(dateTitleTemp).removeClass('curr-month');
                                    $(dateTitleTemp).addClass('next-month');
                                    dateTitleTemp.value = i;
                                    currentMonthTemp = currentMonth + 1;
                                    $(dateTitleTemp).off('click');
                                    $(dateTitleTemp).click(function () {
                                        var dateTemp = $(this).val() + '/' + currentMonthTemp + '/' + currentYear;
                                        var staffData = {
                                            product_id: id,
                                            product_price: price,
                                            date: dateTemp
                                        };
                                        $(staffListTitle).text(dateTemp);
                                        $.ajax({
                                            url: 'reservation/product/time11',
                                            data: staffData,
                                            type: 'POST',
                                            success: function (staffRespond) {
                                                var staffResponseArray = JSON.parse(staffRespond);
                                                for (j = 0; j < staffResponseArray.length; j++) {
                                                    staffTemp.value = 0;

                                                    var staffListElement = $(staffList).find('option');
                                                    $(staffListElement).each(function (index, element) {
                                                        $(element).remove();
                                                    });
                                                    var selected = 'selected="selected"';
                                                    for (j = 0; j < staffResponseArray.length; j++) {
                                                        var newStaffItem = '<option ' + selected + ' value="' + j + '">' + staffResponseArray[j]['staff_name'] + '</option>';
                                                        $(staffList).append(newStaffItem);
                                                        selected = '';
                                                    }
                                                    $(staffIntro).text(staffResponseArray[0]['staff_intro']);
                                                    $(staffAvatar).attr("src", staffResponseArray[0]['staff_avatar']);
                                                    $(staffName).text(staffResponseArray[0]['staff_name']);
                                                    $(currentPrice).text(staffResponseArray[$(staffTemp).val()]['symbol'] + staffResponseArray[0]['event_amount']);
                                                    $(staffList).change(function () {
                                                        $(staffIntro).text(staffResponseArray[$(staffTemp).val()]['staff_intro']);
                                                        $(staffAvatar).attr("src", staffResponseArray[$(staffTemp).val()]['staff_avatar']);
                                                        $(staffName).text(staffResponseArray[$(staffTemp).val()]['staff_name']);
                                                        $(currentPrice).text(staffResponseArray[$(staffTemp).val()]['symbol'] + staffResponseArray[$(staffTemp).val()]['event_amount']);
                                                        staffTemp.value = $(this).val();
                                                    });
                                                    canAddToCart = 1;
                                                    $(addToCartButton).off("click");
                                                    $(addToCartButton).click(function () {
                                                        var cartTempInputVal = $(cartTempInput).val();
                                                        if (canAddToCart == 1) {
                                                            canAddToCart = 0;
                                                            canAddToCartNow = 1;
                                                            if (cartTempInputVal.length > 1) {
                                                                if (cartTempInputVal.indexOf(JSON.stringify(staffResponseArray[$(staffTemp).val()])) > -1) {
                                                                    canAddToCartNow = 0;
                                                                }
                                                            }
                                                            if (canAddToCartNow == 1) {
                                                                var newCartItem = '<li><span>' + staffListTitle.innerHTML;
                                                                var deleteButtonVal = '|' + JSON.stringify(staffResponseArray[$(staffTemp).val()]);
                                                                newCartItem += '<button value="' + staffResponseArray[$(staffTemp).val()]['date'];
                                                                newCartItem += staffResponseArray[$(staffTemp).val()]['staff_id'] + '" type="button" class="action-remove"><span>delete</span></button><br>';
                                                                newCartItem += '<span>' + staffName.innerHTML + ' - ' + currentPrice.innerHTML + '</span><span id="' + staffResponseArray[$(staffTemp).val()]['date'] + staffResponseArray[$(staffTemp).val()]['staff_id'] + '" style="display: none">' + deleteButtonVal + '</span></span></li>';
                                                                $(myCart).append(newCartItem);
                                                                cartTempInput.value = $(cartTempInput).val() + '|' + JSON.stringify(staffResponseArray[$(staffTemp).val()]);
                                                            }
                                                            canAddToCart = 1;
                                                        }
                                                        var deleteButtonList = $(myCart).find('button');
                                                        $(deleteButtonList).each(function (index, element) {
                                                            $(element).click(function () {
                                                                var cartValue = $(cartTempInput).val();
                                                                var closestSpan = document.getElementById($(this).val());
                                                                if (closestSpan != null) cartValue = cartValue.replace(closestSpan.innerHTML, '');
                                                                cartTempInput.value = cartValue;
                                                                $(this).closest('li').remove();
                                                            });
                                                        });
                                                    });
                                                }
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
            });

            var options = {
                type: 'popup', responsive: true, innerScroll: true, title: $.mage.__('Reservation')
            };
            options.buttons = [
                {
                    text: $.mage.__('Cancel'),
                    class: 'action secondary action-hide-popup',
                    click: function () {
                        $(cartTempInput).val("");
                        this.closeModal();
                    }
                },
                {
                    text: $.mage.__('Save Selection'),
                    class: 'action primary action-save-address',
                    click: function () {
                        var finalSchedule = document.getElementById('reservation_option_1_final_schedule');
                        var finalScheduleData = [];
                        if ($(cartTempInput).val().length > 0) {
                            count = 0;
                            finalScheduleData[count] = 1;
                            count = count + 1;
                            finalScheduleData[count] = $(cartTempInput).val();
                            finalSchedule.value = JSON.stringify(finalScheduleData);
                        }
                        var addToCartButtonMagento = document.getElementById('product-addtocart-button');
                        $(addToCartButtonMagento).click();
                        $(cartTempInput).val("");
                        this.closeModal();
                    }
                }
            ];
            var popup = modal(options, $('#reservation_option_1_popup'));
            $('#reservation_option_1_popup').modal('openModal');
        });
    });
});