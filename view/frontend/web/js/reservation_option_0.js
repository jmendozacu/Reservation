/**
 * Created by hoang on 07/07/2016.
 */
define([
    "Magento_Ui/js/modal/modal",
    "jquery",
    "jquery/ui",
    "mage/calendar",
    'mage/translate'
], function (modal,
             $) {
    'use strict';
    var id = $('div.reservation_fields_option_0').attr('data-mage');
    var currency = $('div.reservation_fields_option_0').attr('data-price-currency');
    $.widget('magenest.reservation_option_0', {
        options: {
            priceHolderSelector: '.price-box',
            productId: id,
            dateSelector: '#reservation_date_option_0'
        },
        _create: function () {
            var self = this;
            var dateId = self.options.dateSelector;
            $(dateId).calendar({
                    onSelect: function () {
                        var eventNotify = document.getElementById('reservation_option_0_event_notify');
                        var staffName = document.getElementById('reservation_option_0_staff_name');
                        var staffIntro = document.getElementById('reservation_option_0_staff_intro');
                        var staffAvatar = document.getElementById('reservation_option_0_staff_avatar');
                        var currentPrice = document.getElementById('reservation_option_0_current_price');
                        var staffTemp = document.getElementById('reservation_option_0_staff_temp');
                        var cartTemp = document.getElementById('reservation_option_0_cart_temp');
                        var timeTemp = document.getElementById('reservation_option_0_time_temp');
                        var myCart = document.getElementById('reservation_option_0_cart');
                        var finalSchedule = document.getElementById('reservation_option_0_final_schedule');
                        var addToCartButton = document.getElementById('product-addtocart-button');
                        var addToCart = document.getElementById('reservation_option_0_addToCart');
                        var timeList = document.getElementById('reservation_option_0_time_list');
                        var staffList = document.getElementById('reservation_option_0_staff_list');
                        var timeListElement;
                        var staffListElement;
                        timeListElement = $(timeList).find('li');
                        $(timeListElement).each(function (index, element) {
                            $(element).remove();
                        });
                        timeListElement = $(myCart).find('li');
                        $(timeListElement).each(function (index, element) {
                            $(element).remove();
                        });
                        staffListElement = $(staffList).find('option');
                        $(staffListElement).each(function (index, element) {
                            $(element).remove();
                        });
                        $(eventNotify).text('');
                        $(staffIntro).text('');
                        $(staffAvatar).attr("src", '');
                        $(staffName).text('');
                        $(currentPrice).text('');
                        cartTemp.value = '';
                        $(addToCart).off("click");
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
                        $.ajax({
                            url: 'reservation/product/time0',
                            data: data,
                            type: 'POST',
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
                                                            responseArray[j]['staff_id'],
                                                            responseArray[j]['staff_name'],
                                                            responseArray[j]['staff_avatar'],
                                                            responseArray[j]['staff_intro'],
                                                            responseArray[j]['from_time'],
                                                            responseArray[j]['to_time'],
                                                            responseArray[j]['staff_amount'],
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
                                        var active = 'reservation_option_0_active_';
                                        for (i = 0; i < resultArray.length; i++) {
                                            var newTimeItem = '<li><button id="' + active + i + '" value=' + i + ' class="btn' + '' + '" type="button"><span>From ' + resultArray[i][0][4] + ' To ' + resultArray[i][0][5] + '</span></button>';
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
                                                staffListElement = $(staffList).find('option');
                                                $(staffListElement).each(function (index, element) {
                                                    $(element).remove();
                                                });
                                                var selected = 'selected="selected"';
                                                for (j = 0; j < resultArray[$(this).val()].length; j++) {
                                                    var newStaffItem = '<option ' + selected + ' value="' + j + '">' + resultArray[$(this).val()][j][1] + '</option>';
                                                    $(staffList).append(newStaffItem);
                                                    selected = '';
                                                }
                                                staffTemp.value = 0;
                                                $(eventNotify).text(resultArray[$(timeTemp).val()][$(staffList).val()][7]);
                                                $(staffIntro).text(resultArray[$(timeTemp).val()][$(staffList).val()][3]);
                                                $(staffAvatar).attr("src", resultArray[$(timeTemp).val()][$(staffList).val()][2]);
                                                $(staffName).text(resultArray[$(timeTemp).val()][$(staffList).val()][1]);
                                                $(currentPrice).text(resultArray[$(timeTemp).val()][$(staffList).val()][9] + resultArray[$(timeTemp).val()][$(staffList).val()][8]);
                                                $(staffList).change(function () {
                                                    $(eventNotify).text(resultArray[$(timeTemp).val()][$(staffList).val()][7]);
                                                    $(staffIntro).text(resultArray[$(timeTemp).val()][$(this).val()][3]);
                                                    $(staffAvatar).attr("src", resultArray[$(timeTemp).val()][$(this).val()][2]);
                                                    $(staffName).text(resultArray[$(timeTemp).val()][$(this).val()][1]);
                                                    $(currentPrice).text(resultArray[$(timeTemp).val()][$(staffList).val()][9] + resultArray[$(timeTemp).val()][$(staffList).val()][8]);
                                                    staffTemp.value = $(this).val();
                                                });
                                            });
                                        });
                                        $(addToCart).click(function () {
                                            var cartAdded = 0;
                                            var cartString = $(cartTemp).val();
                                            if (cartString.length > 1) {
                                                var cartArray = cartString.split(";");
                                                for (i = 1; i < cartArray.length; i++) {
                                                    var cartItem = cartArray[i];
                                                    var cartItemArray = cartItem.split(",");
                                                    if ($(timeTemp).val() == cartItemArray[0] && $(staffTemp).val() == cartItemArray[1]) {
                                                        cartAdded = 1;
                                                    }
                                                }
                                            }
                                            if (cartAdded == 0) {
                                                var newCartItem = '<li><div class="title">' + 'From ' + resultArray[$(timeTemp).val()][0][4] + ' To ' + resultArray[$(timeTemp).val()][0][5] + '</div>';
                                                newCartItem += '<p>' + staffName.innerHTML + " - " + currentPrice.innerHTML + '</p>';
                                                newCartItem += '<button value="' + ';' + $(timeTemp).val() + ',' + $(staffTemp).val() + '" type="button" class="action-remove"></button></li>';
                                                $(myCart).append(newCartItem);
                                                cartTemp.value = $(cartTemp).val() + ';' + $(timeTemp).val() + ',' + $(staffTemp).val();
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
                                        $(document.getElementById('reservation_option_0_active_0')).click();
                                    }
                                } else {
                                    newTimeItem = '<li><button class="btn' + '' + '" type="button"><span>'+$.mage.__("No time slot found")+'</span></button>';
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
                                    finalScheduleData[count] = 0;
                                    count++;
                                    if (cartString.length > 1) {
                                        var cartArray = cartString.split(";");
                                        for (i = 1; i < cartArray.length; i++) {
                                            var cartItem = cartArray[i];
                                            var cartItemArray = cartItem.split(",");
                                            finalScheduleData[count] = resultArray[cartItemArray[0]][cartItemArray[1]];
                                            count++;
                                        }
                                        finalSchedule.value = JSON.stringify(finalScheduleData);
                                    }
                                    $(addToCartButton).click();
                                    $(cartTemp).val("");
                                    this.closeModal();
                                }
                            }
                        ];
                        var popup = modal(options, $('#reservation_option_0_popup'));
                        $('#reservation_option_0_popup').modal('openModal');
                    }
                }
            );
        }
    });
    return $.magenest.reservation_option_0;
});