@push('script')
    <script>
        (function($) {
            'use strict';

            var baseUrl = '{{ url('/') }}';
            var gsCurText = '{{ gs()->cur_text }}';

            function initializeFields() {
                hideElement('operatorDiv');
                hideElement('amount-wrapper');
                hideElement('fixed-amounts-wrapper');
                hideElement('suggested-amounts-wrapper');
            }

            $(document).ready(function() {
                initializeFields();
                setCallingCodes();

                // Country selection change
                $('[name=country_id]').on('change', function() {
                    let countryId = $(this).val();
                    
                    if (!countryId) {
                        initializeFields();
                        return;
                    }

                    setCallingCodes();
                    showOperatorsModal(countryId);
                });

                // Modal operators click
                $(document).on('click', '.select-operator', function() {
                    let operator = $(this).data();
                    updateSelectedOperator(operator);
                    closeOperatorsModal();
                });

                // Change operator button
                $(document).on('click', '.changeOperatorBtn', function() {
                    let countryId = $(this).data('country_id');
                    showOperatorsModal(countryId);
                });

                // Confirm operator button
                $(document).on('click', '.confirmOperatorBtn', function() {
                    let selectedOperator = $('[name=temp_operator_id]:checked');
                    if (selectedOperator.length) {
                        let operator = selectedOperator.data();
                        updateSelectedOperator(operator);
                        closeOperatorsModal();
                    } else {
                        notify('error', '@lang('Please select an operator')');
                    }
                });

                // Suggested amounts click
                $(document).on('click', '[name=suggested_amount]', function() {
                    let amount = $(this).val();
                    $('.amount').val(amount);
                });

                // Tab switching in modal
                $(document).on('click', '.operator-tab', function() {
                    let target = $(this).data('target');
                    updateOperatorTabPanel(target);
                });
            });

            function setCallingCodes() {
                let countryData = $('[name=country_id] option:selected').data('calling_codes');
                let callingCodeSelect = $('[name=calling_code]');
                
                callingCodeSelect.html('');
                
                if (countryData) {
                    $.each(countryData, function(index, code) {
                        callingCodeSelect.append(`<option value="${code}">+${code}</option>`);
                    });
                }
            }

            function showOperatorsModal(countryId) {
                $('#operatorsModal').removeClass('hidden');
                $('.modal-preloader').removeClass('hidden');
                
                $.ajax({
                    url: '{{ route('user.airtime.country.operators', '') }}/' + countryId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let operators = response.data.operators;
                            updateOperatorTabPanel('all', operators);
                        } else {
                            notify('error', response.message);
                            closeOperatorsModal();
                        }
                    },
                    error: function() {
                        notify('error', '@lang('Something went wrong')');
                        closeOperatorsModal();
                    },
                    complete: function() {
                        $('.modal-preloader').addClass('hidden');
                    }
                });
            }

            function updateOperatorTabPanel(type = 'all', operators = null) {
                if (!operators) {
                    operators = window.allOperators || [];
                }
                
                // Store operators globally for tab switching
                if (operators && operators.length) {
                    window.allOperators = operators;
                }

                let filteredOperators = operators;
                
                if (type !== 'all') {
                    filteredOperators = operators.filter(operator => {
                        return operator.type && operator.type.toLowerCase() === type.toLowerCase();
                    });
                }

                let html = '';
                
                if (filteredOperators.length === 0) {
                    html = `
                        <div class="text-center py-8">
                            <i class="las la-exclamation-circle text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">@lang('No operators available')</p>
                        </div>
                    `;
                } else {
                    html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
                    
                    filteredOperators.forEach(operator => {
                        let dataProperties = makeDataKeyValuePair(operator);
                        
                        html += `
                            <div class="operator-item border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:border-blue-500 dark:hover:border-blue-400 transition-colors duration-200 cursor-pointer">
                                <input type="radio" name="temp_operator_id" value="${operator.id}" ${dataProperties} class="hidden select-operator">
                                <div class="flex items-center space-x-4" onclick="selectOperatorRadio(${operator.id})">
                                    <div class="flex-shrink-0">
                                        <img src="${operator.logo_urls[0]}" alt="${operator.name}" class="w-12 h-12 rounded-lg object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">${operator.name}</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">${operator.country_name || ''}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="las la-check-circle text-2xl text-blue-600 opacity-0 operator-check"></i>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                }

                $(`.operator-wrapper.${type}`).html(html);
            }

            function setElementByDenominationType(operator) {
                let denominationType = operator.denomination_type;
                
                if (denominationType === 'FIXED') {
                    setFixedAmountInputs();
                    hideElement('amount-wrapper');
                    showTopUpLimit(operator);
                } else {
                    hideElement('fixed-amounts-wrapper');
                    showElement('amount-wrapper');
                    showTopUpLimit(operator);
                }
            }

            function showTopUpLimit(operator) {
                let minAmount = operator.min_amount || 0;
                let maxAmount = operator.max_amount || 0;
                
                if (minAmount || maxAmount) {
                    let limitText = '';
                    if (minAmount && maxAmount) {
                        limitText = `(${minAmount} - ${maxAmount} ${gsCurText})`;
                    } else if (minAmount) {
                        limitText = `(Min: ${minAmount} ${gsCurText})`;
                    } else if (maxAmount) {
                        limitText = `(Max: ${maxAmount} ${gsCurText})`;
                    }
                    
                    $('.topupLimit').text(limitText).removeClass('hidden');
                } else {
                    $('.topupLimit').addClass('hidden');
                }
            }

            function showSuggestedAmounts() {
                let operator = $('[name=operator_id]:checked').data();
                let suggestedAmounts = operator.suggested_amounts;
                
                if (!suggestedAmounts || !suggestedAmounts.length) {
                    hideElement('suggested-amounts-wrapper');
                    return;
                }

                let html = '<div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">';
                
                suggestedAmounts.forEach((amount, key) => {
                    html += `
                        <div class="suggested-amount-item">
                            <input type="radio" name="suggested_amount" id="suggested-${key}" value="${amount}" class="hidden">
                            <label for="suggested-${key}" class="block w-full px-3 py-2 text-center text-sm font-medium border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900 hover:border-blue-500 dark:hover:border-blue-400 transition-colors duration-200">
                                ${amount} ${gsCurText}
                            </label>
                        </div>
                    `;
                });

                html += '</div>';
                
                $('.suggested-amounts').html(html);
                showElement('suggested-amounts-wrapper');
            }

            function setFixedAmountInputs() {
                let operator = $('[name=operator_id]:checked').data();
                
                if (operator.denomination_type !== 'FIXED') {
                    $('.fixed-amount-input-wrapper').html('');
                    hideElement('fixed-amounts-wrapper');
                    return false;
                }

                let fixedAmounts = operator.fixed_amounts_descriptions && !jQuery.isEmptyObject(operator.fixed_amounts_descriptions) 
                    ? operator.fixed_amounts_descriptions 
                    : operator.fixed_amounts;

                let hasDesc = operator.fixed_amounts_descriptions && !jQuery.isEmptyObject(operator.fixed_amounts_descriptions);

                if (!fixedAmounts || (Array.isArray(fixedAmounts) && fixedAmounts.length === 0) || 
                    (typeof fixedAmounts === "object" && Object.keys(fixedAmounts).length === 0)) {
                    $('.fixed-amount-input-wrapper').html('');
                    hideElement('fixed-amounts-wrapper');
                    return false;
                }

                let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
                
                $.each(fixedAmounts, function(key, value) {
                    let displayValue = hasDesc ? key : value;
                    let description = hasDesc ? value : '';
                    
                    html += `
                        <div class="fixed-amount-item border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:border-blue-500 dark:hover:border-blue-400 transition-colors duration-200">
                            <input type="radio" name="amount" id="fixed-${key}" value="${displayValue}" required class="hidden">
                            <label for="fixed-${key}" class="cursor-pointer block">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-900 dark:text-white">${displayValue} ${gsCurText}</span>
                                    <i class="las la-check-circle text-2xl text-blue-600 opacity-0 amount-check"></i>
                                </div>
                                ${description ? `<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">${description}</p>` : ''}
                            </label>
                        </div>
                    `;
                });

                html += '</div>';
                
                $('.fixed-amount-input-wrapper').html(html);
                showElement('fixed-amounts-wrapper');
            }

            function updateSelectedOperator(operator) {
                let dataProperties = makeDataKeyValuePair(operator);
                
                $(".operatorDiv").find('.operator-wrapper').html(`
                    <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <input name="operator_id" type="radio" value="${operator.id}" ${dataProperties} checked class="hidden">
                        <div class="flex items-center space-x-4">
                            <img src="${operator.logo_urls[0]}" alt="${operator.name}" class="w-12 h-12 rounded-lg object-cover">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">${operator.name}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">${operator.country_name || ''}</p>
                            </div>
                        </div>
                        <button type="button" class="changeOperatorBtn px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200" data-country_id="${operator.country_id}">
                            @lang('Change')
                        </button>
                    </div>
                `);

                @if (!old('amount'))
                    resetVal('amount');
                @endif

                showElement('operatorDiv');
                setElementByDenominationType(operator);

                if (operator.suggested_amounts && operator.suggested_amounts.length) {
                    showSuggestedAmounts();
                } else {
                    hideElement('suggested-amounts-wrapper');
                }
            }

            function makeDataKeyValuePair(obj) {
                let data = '';
                delete obj.created_at;
                delete obj.updated_at;

                for (const key in obj) {
                    if (typeof(obj[key]) === 'object') {
                        let value = JSON.stringify(obj[key]).replace(/"/g, '&quot;');
                        data += `data-${key}="${value}" `;
                    } else {
                        data += `data-${key}="${obj[key]}" `;
                    }
                }

                return data;
            }

            function resetVal(elem) {
                $(`.${elem}`).val('');
            }

            function hideElement(elem) {
                $(`.${elem}`).addClass('hidden');
            }

            function showElement(elem) {
                $(`.${elem}`).removeClass('hidden');
            }

            // Global functions for modal interactions
            window.selectOperatorRadio = function(operatorId) {
                // Remove selected state from all items
                $('.operator-item').removeClass('border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900');
                $('.operator-check').addClass('opacity-0');
                
                // Add selected state to clicked item
                let selectedItem = $(`input[value="${operatorId}"]`).closest('.operator-item');
                selectedItem.addClass('border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900');
                selectedItem.find('.operator-check').removeClass('opacity-0');
                
                // Check the radio button
                $(`input[value="${operatorId}"]`).prop('checked', true);
            };

            window.closeOperatorsModal = function() {
                $('#operatorsModal').addClass('hidden');
                
                if (!$('[name=operator_id]').val()) {
                    $('[name=country_id]').val('');
                }
            };

            // Handle fixed amount selection
            $(document).on('change', '[name=amount]', function() {
                $('.amount-check').addClass('opacity-0');
                $('.fixed-amount-item').removeClass('border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900');
                
                if (this.checked) {
                    let selectedItem = $(this).closest('.fixed-amount-item');
                    selectedItem.addClass('border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900');
                    selectedItem.find('.amount-check').removeClass('opacity-0');
                }
            });

            // Handle suggested amount selection
            $(document).on('change', '[name=suggested_amount]', function() {
                if (this.checked) {
                    $('.amount').val(this.value);
                }
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .operator-item.selected {
            border-color: rgb(37 99 235);
            background-color: rgb(239 246 255);
        }
        
        .dark .operator-item.selected {
            border-color: rgb(147 197 253);
            background-color: rgb(30 58 138);
        }
        
        .fixed-amount-item.selected {
            border-color: rgb(37 99 235);
            background-color: rgb(239 246 255);
        }
        
        .dark .fixed-amount-item.selected {
            border-color: rgb(147 197 253);
            background-color: rgb(30 58 138);
        }
        
        .suggested-amount-item input:checked + label {
            background-color: rgb(239 246 255);
            border-color: rgb(37 99 235);
            color: rgb(37 99 235);
        }
        
        .dark .suggested-amount-item input:checked + label {
            background-color: rgb(30 58 138);
            border-color: rgb(147 197 253);
            color: rgb(147 197 253);
        }
    </style>
@endpush