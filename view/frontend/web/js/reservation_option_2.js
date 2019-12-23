/**
 * Created by hoang on 07/07/2016.
 */
define([
    "Magento_Ui/js/modal/modal",
    "jquery",
    "jquery/ui",
    "mage/calendar",
    'Magento_Ui/js/lib/view/utils/async'
], function (modal, $) {
    'use strict';
    var id = $('div.reservation_fields_option_2').attr('data-mage');
    $.widget('magenest.reservation_option_2', {
        options: {
            priceHolderSelector: '.price-box',
            productId: id,
            dateSelector: '#reservation_date_option_2'
        },
        _create: function () {
            var self = this;
            var dateId = self.options.dateSelector;
            $.async("#reservation_date_option_2", function () {
                var addToCartButton = document.getElementById('reservation_date_option_2');
                var addToCartButtonMagentoDisplay;
                if (addToCartButton != null) {
                    addToCartButtonMagentoDisplay = document.getElementById('product-addtocart-button');
                    addToCartButtonMagentoDisplay.style.visibility = 'hidden';
                    $(document.getElementsByClassName('field qty')).remove();
                }
            });
            $(dateId).calendar({
                    onSelect: function () {
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
                        var data = {
                            date: $(this).val(),
                            product_id: id,
                            product_price: price
                        };
                        var timeIntervalFrom = [];
                        var timeIntervalTo = [];
                        var i = 0;
                        var j = 0;
                        var resultArray = [];
                        var numberOfSlotsNotify = document.getElementById('reservation_option_2_numberOfSlots_notify');
                        var eventNotify = document.getElementById('reservation_option_2_event_notify');
                        var currentPrice = document.getElementById('reservation_option_2_current_price');
                        var inputSlots = document.getElementById('reservation_option_2_numberOfSlots');
                        var timeList = document.getElementById('reservation_option_2_time_list');
                        var cartTemp = document.getElementById('reservation_option_2_cart_temp');
                        var timeTemp = document.getElementById('reservation_option_2_time_temp');
                        var myCart = document.getElementById('reservation_option_2_cart');
                        var finalSchedule = document.getElementById('reservation_option_2_final_schedule');
                        var addToCart = document.getElementById('reservation_option_2_addToCart');
                        var addToCartButtonMagento = document.getElementById('product-addtocart-button');
                        var timeListElement;
                        $(addToCart).off("click");
                        timeListElement = $(timeList).find('li');
                        $(timeListElement).each(function (index, element) {
                            $(element).remove();
                        });
                        timeListElement = $(myCart).find('li');
                        $(timeListElement).each(function (index, element) {
                            $(element).remove();
                        });
                        $(numberOfSlotsNotify).text('');
                        $(eventNotify).text('');
                        $(currentPrice).text('');

                        $.ajax({
                            url: 'reservation/product/time2',
                            type: "POST",
                            data: data,
                            success: function (response) {
                                if (response.length > 2) {
                                    var responseArray = JSON.parse(response);
                                    for (i = 0; i < responseArray.length; i++) {
                                        timeIntervalFrom.push(responseArray[i]['from_time']);
                                        timeIntervalTo.push(responseArray[i]['to_time']);
                                    }

                                    for (i = 0; i < timeIntervalFrom.length; i++) {
                                        for (j = i + 1; j < timeIntervalFrom.length; j++)
                                            if (timeIntervalFrom[i] == timeIntervalFrom[j] && timeIntervalTo[i] == timeIntervalTo[j]) {
                                                timeIntervalFrom[j] = '0';
                                            }
                                    }
                                    var count = 0;
                                    if (timeIntervalFrom.length > 0) {
                                        for (i = 0; i < timeIntervalFrom.length; i++) {
                                            var resultChildArray = [];
                                            if (timeIntervalFrom[i] != '0') {
                                                var count1 = 0;
                                                for (j = 0; j < responseArray.length; j++) {
                                                    if (responseArray[j]['from_time'] == timeIntervalFrom[i] && responseArray[j]['to_time'] == timeIntervalTo[i]) {
                                                        resultChildArray[count1] = new Array(
                                                            responseArray[j]['from_time'],
                                                            responseArray[j]['to_time'],
                                                            responseArray[j]['slots'],
                                                            responseArray[j]['event_name'],
                                                            responseArray[j]['event_amount'],
                                                            responseArray[j]['symbol']);
                                                        count1++;
                                                    }
                                                }
                                            }
                                            if (resultChildArray.length > 0) {
                                                resultArray[count] = resultChildArray;
                                                count++;
                                            }
                                        }
                                    }
                                    if (resultArray.length > 0) {
                                        /**
                                         * start draw product schedule
                                         */
                                        timeListElement = $(timeList).find('li');
                                        $(timeListElement).each(function (index, element) {
                                            $(element).remove();
                                        });
                                        var active = 'reservation_option_2_active_';
                                        for (i = 0; i < resultArray.length; i++) {
                                            var newTimeItem = '<li><button id="' + active + i + '" value=' + i + ' class="btn' + '' + '" type="button"><span>From ' + resultArray[i][0][0] + ' To ' + resultArray[i][0][1] + '</span></button>';
                                            $(timeList).append(newTimeItem);
                                        }
                                        timeListElement = $(timeList).find('li');
                                        $(timeListElement).each(function (index, element) {
                                            $(element).find('button').click(function () {
                                                var timeListElementTemp = $(timeList).find('li');
                                                $(timeListElementTemp).each(function (index, element) {
                                                    $(element).find('button').removeClass('active');
                                                });
                                                $(this).addClass('active');
                                                timeTemp.value = $(this).val();

                                                inputSlots.max = resultArray[$(timeTemp).val()][0][2];
                                                $(numberOfSlotsNotify).text(resultArray[$(timeTemp).val()][0][2]);
                                                $(eventNotify).text(resultArray[$(timeTemp).val()][0][3]);
                                                $(currentPrice).text(resultArray[$(timeTemp).val()][0][5] + resultArray[$(timeTemp).val()][0][4]);
                                            });
                                        });
                                        $(addToCart).off("click");
                                        $(addToCart).click(function () {
                                            var cartAdded = 0;
                                            var cartString = $(cartTemp).val();
                                            if (cartString.length > 1) {
                                                var cartArray = cartString.split(";");
                                                for (i = 1; i < cartArray.length; i++) {
                                                    var cartItem = cartArray[i];
                                                    var cartItemArray = cartItem.split(",");
                                                    if ($(timeTemp).val() == cartItemArray[0] && $(numberOfSlotsNotify).val() == cartItemArray[1]) {
                                                        cartAdded = 1;
                                                    }
                                                }
                                            }
                                            if (cartAdded == 0) {
                                                var orderAdded = document.getElementById('reservation_option_2_cart_item_' + $(timeTemp).val());
                                                var finalCartTemp = "";
                                                if (orderAdded != null) {
                                                    cartArray = cartString.split(";");
                                                    var slotNumTemp = 0;
                                                    var cartItemInfoTemp = document.getElementById('reservation_option_2_cart_item_info_' + $(timeTemp).val());
                                                    for (i = 1; i < cartArray.length; i++) {
                                                        cartItem = cartArray[i];
                                                        cartItemArray = cartItem.split(",");
                                                        if ($(timeTemp).val() == cartItemArray[0]) {
                                                            slotNumTemp = parseInt(cartItemArray[1]) + parseInt($(inputSlots).val());
                                                            if (slotNumTemp > parseInt(numberOfSlotsNotify.innerHTML)) {
                                                                slotNumTemp = parseInt(numberOfSlotsNotify.innerHTML);
                                                            }
                                                            cartItemArray[1] = slotNumTemp;
                                                            var cartItemDelete = document.getElementById('reservation_option_2_cart_item_delete_' + $(timeTemp).val());
                                                            cartItemDelete.value = ';' + cartItemArray[0] + ',' + cartItemArray[1];
                                                        }
                                                        finalCartTemp += ';' + cartItemArray[0] + ',' + cartItemArray[1];
                                                    }
                                                    cartTemp.value = finalCartTemp;
                                                    var currentPriceTemp = Number(slotNumTemp) * parseFloat(resultArray[$(timeTemp).val()][0][4]);
                                                    currentPriceTemp = currentPriceTemp.toFixed(2);
                                                    $(cartItemInfoTemp).text(slotNumTemp + ' slot(s) - ' + resultArray[$(timeTemp).val()][0][5] + currentPriceTemp);
                                                } else {
                                                    var newCartItem = '<li id="reservation_option_2_cart_item_' + $(timeTemp).val() + '"><div class="title">' + 'From ' + resultArray[$(timeTemp).val()][0][0] + ' To ' + resultArray[$(timeTemp).val()][0][1] + '</div>';
                                                    newCartItem += '<p id="reservation_option_2_cart_item_info_' + $(timeTemp).val() + '">' + $(inputSlots).val() + " slot(s) - " + currentPrice.innerHTML + '</p>';
                                                    newCartItem += '<button id="reservation_option_2_cart_item_delete_' + $(timeTemp).val() + '" value="' + ';' + $(timeTemp).val() + ',' + $(inputSlots).val() + '" type="button" class="action-remove"></button></li>';
                                                    $(myCart).append(newCartItem);
                                                    cartTemp.value = $(cartTemp).val() + ';' + $(timeTemp).val() + ',' + $(inputSlots).val();
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
                                        $(document.getElementById('reservation_option_2_active_0')).click();
                                    }
                                } else {
                                    var newTimeItem = '<li><button class="btn' + '' + '" type="button"><span>No time slot found</span></button>';
                                    $(timeList).append(newTimeItem);
                                }
                            },
                            showLoader: true
                        });
                        var options = {
                            type: 'popup', responsive: true, innerScroll: true, title: $(dateId).val()
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
                                    var cartString = $(cartTemp).val();
                                    var finalScheduleData = [];
                                    var count = 0;
                                    finalScheduleData[count] = 2;
                                    count++;
                                    if (cartString.length > 1) {
                                        var cartArray = cartString.split(";");
                                        for (i = 1; i < cartArray.length; i++) {
                                            var cartItem = cartArray[i];
                                            var cartItemArray = cartItem.split(",");
                                            var currentPriceTemp = Number(cartItemArray[1]) * parseFloat(resultArray[cartItemArray[0]][0][4]);
                                            finalScheduleData[count] = [
                                                resultArray[cartItemArray[0]][0][0],
                                                resultArray[cartItemArray[0]][0][1],
                                                resultArray[cartItemArray[0]][0][3],
                                                cartItemArray[1],
                                                currentPriceTemp,
                                                resultArray[cartItemArray[0]][0][5]
                                            ];
                                            count++;
                                        }
                                        finalSchedule.value = JSON.stringify(finalScheduleData);
                                    }
                                    $(addToCartButtonMagento).click();
                                    $(cartTemp).val("");
                                    this.closeModal();
                                }
                            }
                        ];
                        var popup = modal(options, $('#reservation_option_2_popup'));
                        $('#reservation_option_2_popup').modal('openModal');
                    }
                }
            );
        }
    });
    return $.magenest.reservation_option_2;
});