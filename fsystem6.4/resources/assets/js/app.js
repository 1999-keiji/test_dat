'use strict'

require('es6-promise/auto')
require('babel-polyfill')
require('./bootstrap')
require('./vue-strap-lang')

window.Vue = require('vue')
Vue.filter('clone', function (value) {
  return _.cloneDeep(value)
})

Vue.component('datepicker-ja',                           require('./components/Datepicker'))
Vue.component('delete-form',                             require('./components/DeleteForm'))
Vue.component('input-number-with-formatter',             require('./components/InputNumberWithFormatter'))
Vue.component('select-multiple',                         require('./components/SelectMultiple'))
Vue.component('year-monthpicker-ja',                     require('./components/YearMonthpicker'))
Vue.component('export-work-instruction-excel-form',      require('./components/factory_production_work/ExportWorkInstructionExcelForm'))
Vue.component('select-factory-species',                  require('./components/factory_production_work/SelectFactorySpecies'))
Vue.component('add-delivery-factory-product',            require('./components/master/AddDeliveryFactoryProduct'))
Vue.component('add-delivery-warehouse',                  require('./components/master/AddDeliveryWarehouse'))
Vue.component('add-factory-product',                     require('./components/master/AddFactoryProduct'))
Vue.component('add-factory-species',                     require('./components/master/AddFactorySpecies'))
Vue.component('add-species',                             require('./components/master/AddSpecies'))
Vue.component('edit-delivery-factory-product',           require('./components/master/EditDeliveryFactoryProduct'))
Vue.component('edit-delivery-warehouse',                 require('./components/master/EditDeliveryWarehouse'))
Vue.component('edit-factory-layout',                     require('./components/master/EditFactoryLayout'))
Vue.component('edit-factory-cycle-pattern',              require('./components/master/EditFactoryCyclePattern'))
Vue.component('edit-factory-product',                    require('./components/master/EditFactoryProduct'))
Vue.component('edit-species',                            require('./components/master/EditSpecies'))
Vue.component('overwrite-on-invoice',                    require('./components/master/OverwriteOnInvoice'))
Vue.component('save-event-form',                         require('./components/master/SaveEventForm'))
Vue.component('save-factory-rest-form',                  require('./components/master/SaveFactoryRestForm.vue'))
Vue.component('search-master',                           require('./components/master/SearchMaster'))
Vue.component('search-user',                             require('./components/master/SearchUser'))
Vue.component('set-transport-company-form',              require('./components/master/SetTransportCompanyForm'))
Vue.component('add-order-manually-form',                 require('./components/order/AddOrderManuallyForm'))
Vue.component('cancel-link',                             require('./components/order/CancelLink'))
Vue.component('delete-manual-created-order-form',        require('./components/order/DeleteManualCreatedOrderForm'))
Vue.component('edit-manual-created-order-form',          require('./components/order/EditManualCreatedOrderForm'))
Vue.component('edit-order-data-form-first',              require('./components/order/EditOrderDataFormFirst'))
Vue.component('edit-order-data-form-second',             require('./components/order/EditOrderDataFormSecond'))
Vue.component('export-forecast-excel-form',              require('./components/order/ExportForecastExcelForm'))
Vue.component('link-with-fixed-order',                   require('./components/order/LinkWithFixedOrder'))
Vue.component('return-input-form',                       require('./components/order/ReturnInputForm'))
Vue.component('search-manual-created-orders-form',       require('./components/order/SearchManualCreatedOrdersForm'))
Vue.component('search-orders-form',                      require('./components/order/SearchOrdersForm'))
Vue.component('search-returned-products-form',           require('./components/order/SearchReturnedProductsForm'))
Vue.component('whiteboard-reference',                    require('./components/order/WhiteboardReference'))
Vue.component('add-bed-state-form',                      require('./components/plan/AddBedStateForm'))
Vue.component('allocate-panels',                         require('./components/plan/AllocatePanels'))
Vue.component('bed-states',                              require('./components/plan/BedStates'))
Vue.component('export-growth-planned-table',             require('./components/plan/ExportGrowthPlannedTable'))
Vue.component('fix-simulation',                          require('./components/plan/FixSimulation'))
Vue.component('growth-simulation-details',               require('./components/plan/GrowthSimulationDetails'))
Vue.component('input-simulation-pattern',                require('./components/plan/InputSimulationPattern'))
Vue.component('management-factory-species',              require('./components/plan/ManagementFactorySpecies'))
Vue.component('replace-bed',                             require('./components/plan/ReplaceBed'))
Vue.component('search-bed-states-form',                  require('./components/plan/SearchBedStatesForm'))
Vue.component('search-growth-management-summary-form',   require('./components/plan/SearchGrowthManagementSummaryForm'))
Vue.component('search-growth-simulations-form',          require('./components/plan/SearchGrowthSimulationsForm'))
Vue.component('select-form-to-add-simulation',           require('./components/plan/SelectFormToAddSimulation'))
Vue.component('select-form-to-edit-simulation',          require('./components/plan/SelectFormToEditSimulation'))
Vue.component('select-stage-and-pattern',                require('./components/plan/SelectStageAndPattern'))
Vue.component('start-simulation-form',                   require('./components/plan/StartSimulationForm'))
Vue.component('update-simulation-form',                  require('./components/plan/UpdateSimulationForm'))
Vue.component('update-simulation-pattern',               require('./components/plan/UpdateSimulationPattern'))
Vue.component('allocate-products',                       require('./components/shipment/AllocateProducts'))
Vue.component('allocation-status-per-shipping-date',     require('./components/shipment/AllocationStatusPerShippingDate'))
Vue.component('collection-request-detail-modal',         require('./components/shipment/CollectionRequestDetailModal'))
Vue.component('export-invoice-form',                     require('./components/shipment/ExportInvoiceForm'))
Vue.component('input-allocation-quantity',               require('./components/shipment/InputAllocationQuantity'))
Vue.component('input-productized-results',               require('./components/shipment/InputProductizedResults'))
Vue.component('search-collection-request',               require('./components/shipment/SearchCollectionRequest'))
Vue.component('search-form-output-form',                 require('./components/shipment/SearchFormOutputForm'))
Vue.component('search-productized-results',              require('./components/shipment/SearchProductizedResults'))
Vue.component('search-shipment-data-export-file',        require('./components/shipment/SearchShipmentDataExportFile'))
Vue.component('search-shipment-fix-form',                require('./components/shipment/SearchShipmentFixForm'))
Vue.component('slip-detail-information',                 require('./components/shipment/SlipDetailInformation'))
Vue.component('update-shipment-data-form',               require('./components/shipment/UpdateShipmentDataForm'))
Vue.component('adjust-stock-form',                       require('./components/stock/AdjustStockForm'))
Vue.component('input-disposal-quantity',                 require('./components/stock/InputDisposalQuantity'))
Vue.component('dispose-stocks-form',                     require('./components/stock/DisposeStocksForm'))
Vue.component('move-stock-form',                         require('./components/stock/MoveStockForm'))
Vue.component('search-disposed-stocks-form',             require('./components/stock/SearchDisposedStocksForm'))
Vue.component('search-stock-histories-form',             require('./components/stock/SearchStockHistoriesForm'))
Vue.component('search-stocks-form',                      require('./components/stock/SearchStocksForm'))
Vue.component('search-stock-states-form',                require('./components/stock/SearchStockStatesForm'))
Vue.component('search-stock-summary-form',               require('./components/stock/SearchStockSummaryForm'))
Vue.component('search-stocktaking-form',                 require('./components/stock/SearchStocktakingForm'))
// 追記
Vue.component('search-jccores-form',                     require('./components/external_integration/SearchJccoresForm'))
Vue.component('edit-factory-jccores',                    require('./components/master/EditFactoryJccores'))


new Vue({el: '#app'})

$(function () {
  $('.clear-session').click(() => {
    $('#clear-session-form').submit()
  })

  const can_save_data = $('#can-save-data').val()
  if (can_save_data != null && can_save_data === '0') {
    $('.save-data-form input, .save-data-form select, .save-data-form textarea').prop('disabled', true)
  }

  $('[data-toggle="tooltip"]').tooltip({html: true, trigger: 'hover'})

  $('.datepicker-input').attr('autocomplete', 'off')

  $('.save-data').click(function () {
    if (confirm('データを登録しますか？')) {
      $('.alert').remove()
      $(this).prop('disabled', true)

      $(this).parents('.save-data-form').submit()
    }
  })

  $('#affiliation').change(function () {
    $('#factory_code').prop('disabled', ! $(this).children('option:selected').data('can-select-factory'))
    if (! $(this).children('option:selected').data('can-select-factory')) {
      $('#factory_code').val('')
    }
  })

  $('#affiliation_user_add').change(function() {
    $(this).parents('form').attr('action', $(this).data('action'))
    $(this).parents('form').submit()
  })

  $('#affiliation_user_edit').change(function() {
    $(this).parents('form').find('input[name="_method"]').val('POST')
    $(this).parents('form').attr('action', $(this).data('action'))
    $(this).parents('form').submit()
  })

  $('#reset-password').click(function () {
    if (confirm('パスワードをリセットしますか?')) {
      $('.alert').remove()

      $(this).parents('form').attr('action', $(this).data('action'))
      $(this).parents('form').submit()
    }
  })

  $('.export-data').click(function () {
    if (confirm('Excelをダウンロードしますか?')) {
      $('.alert').remove()

      $(this).parents('.export-data-form').submit()
    }
  })

  $('.import-data').click(function () {
    var file = $('#file_select_hidden').prop('files')[0]
    if(file == null) {
      alert('ファイルが選択されていません。')
      return
    }
    if (confirm('Excelをアップロードしますか?')) {
      $('.alert').remove()
      $(this).prop('disabled', true)
      $(this).parents('.import-data-form').submit()
    }
  })

  $('#file_select_btn').click(function () {
    $(this).prev('#file_select_hidden').click()
    return false
  })

  $('#file_select_hidden').change(function() {
    if ($(this).prop('files')[0]) {
      var file = $(this).prop('files')[0]
      $(this).parents('form').find('#file_name_view').val(file.name)
    } else {
      $(this).parents('form').find('#file_name_view').val('')
    }
  })

  $('.change-pattern').on('mouseover', function(){
    $('#simulation_name').removeAttr('required')
  })
  $('.change-pattern').on('mouseout', function(){
    $('#simulation_name').attr('required', 'required')
  })

  $('.remove-alert').click(function () {
    $('.alert').remove()
  })

  $('.pattern-head input#simulation_name').keypress(function (ev) {
    return (ev.which && ev.which === 13) || (ev.keyCode && ev.keyCode === 13) ? false : true
  })

  $('input[name="factory_species_code"]:radio').change(function () {
    const $form = $(this).parents('form')
    $form.attr('action', $form.attr('action') + $(this).val()).submit()
  })

  $('input[name="factory_product_sequence_number"]:radio').click(function () {
    const $form = $(this).parents('form')
    $form.attr('action', $form.attr('action') + $(this).val()).submit()
  })

  $('#check-all').on('click', function() {
    $('.check-target').prop('checked', this.checked)
  })

  $('button.excel_download').on('click', function () {
    if (confirm('Excelをダウンロードしますか?')) {
      $('.alert').remove()

      $('button.form_output_excel').click()
    }
  })

  if ($('.get-width-target').length && $('.set-width-target').length) {
    var set_width = $('.get-width-target').outerWidth()
    $('.set-width-target').attr('style', 'width: ' + set_width + 'px;')
  }
})

$(window).on('resize', function () {
  if ($('.get-width-target').length && $('.set-width-target').length) {
    var set_width = $('.get-width-target').outerWidth()
    $('.set-width-target').attr('style', 'width: ' + set_width + 'px;')
  }
})
