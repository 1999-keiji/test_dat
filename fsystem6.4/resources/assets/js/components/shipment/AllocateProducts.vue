<style scoped>
label.header {
  background-color: #667f34;
  color: #ffffff;
  font-size: 0.9em;
  margin-bottom: 15px;
  padding: 0.2em 0.5em;
  text-align: left;
}

table.allocation-status>tbody>tr.border {
  visibility: hidden;
}
table.allocation-status>tbody>tr>td.legend {
  border: solid 1px #667f34;
  width: 2em;
}
table.allocation-status>tbody>tr>td.border {
  background-color: #ffffff;
  border: none;
  height: 3px;
}

table.allocation-status>tbody>tr>td.buffer {
  width: 5px;
}
table.allocation-status>tbody>tr>td.status {
  border: dashed 1px #667f34;
  width: 10em;
}

select#warehouse-code {
  height: 24px;
  padding: 0px 6px;
  margin-left: 2.5em;
  width: 15em;
}

table.product-stocks,
table.product-stocks>tbody>tr>td {
  border: solid 1px #667f34;
}
table.product-stocks>tbody>tr>td.warehouse-name {
  padding: 2px;
  width: 10em;
}
table.product-stocks>tbody>tr>td.harvesting-date {
  padding: 2px;
  text-align: center;
  width: 12em;
}
table.product-stocks>tbody>tr>td.stock {
  font-weight: bold;
  padding: 2px;
  text-align: center;
  width: 3em;
}
table.product-stocks>tbody>tr>td.stock.prev-week {
  background-image: -webkit-radial-gradient(#555 20%, transparent 0), -webkit-radial-gradient(#555 20%, transparent 0);
  background-image: radial-gradient(#555 20%, transparent 0), radial-gradient(#555 20%, transparent 0);
  background-position: 0 0, 10px 10px;
  -webkit-background-size: 5px 5px;
  background-size: 5px 5px;
}

.shipping-date-term {
  margin-top: 1.5em;
}

.shipping-dates {
  width: 11em;
}
.shipping-dates>table>thead>tr.factory-product-row,
.shipping-dates>table>tbody>tr.delivery-lead-time-row,
.allocation-quantities>table>thead>tr.factory-product-row,
.allocation-quantities>table>tbody>tr.delivery-lead-time-row {
  height: 1em;
}
.shipping-dates>table>thead>tr.delivery-destination-row,
.allocation-quantities>table>thead>tr.delivery-destination-row {
  height: 15em;
}
.shipping-dates>table>tbody>tr.product-allocation-row,
.allocation-quantities>table>tbody>tr.product-allocation-row {
  height: 3em;
}
.shipping-dates>table>tbody>tr>th.shipping-date {
  width: 6em;
}
.shipping-dates>table>tbody>tr>th.day-of-the-week {
  width: 5em;
}
.shipping-dates>table>tbody>tr>th.shipping-date.pointer-cursor {
  cursor: pointer;
}

.allocation-quantities {
  overflow-x: scroll;
  white-space: nowrap;
  width: 70%;
}
.allocation-quantities>table {
  table-layout: fixed;
}
.allocation-quantities>table>colgroup>col.delivery-destination-col {
  width: 5em;
}
.allocation-quantities>table>thead>tr>th.delivery-destination-name {
  padding: 0;
}
.allocation-quantities>table>thead>tr>th.delivery-destination-name>p {
  display: inline;
  letter-spacing: 2px;
  line-height: 1em;
  margin: 0 auto;
  text-orientation: upright;
  white-space: nowrap;
  writing-mode: vertical-rl;
  -ms-writing-mode: tb-rl;
}
.allocation-quantities>table>tbody>tr>td.allocation-status-per-shipping-date {
  padding: 0;
}
</style>

<template>
  <div>
    <div class="row">
      <div class="col-md-8 col-sm-8 col-xs-10">
        <a class="btn btn-default btn-lg back-button" :href="hrefToIndexOfProductizedResults" @click="confirmLeave($event)">
          <i class="fa fa-arrow-left"></i> 戻る
        </a>
      </div>
      <div v-if="canSaveProductAllocation" class="col-md-4 col-sm-4 col-xs-2">
        <button class="btn btn-default btn-lg pull-right" type="button" :disabled="! allocationHasChanged" @click="saveProductAllocations($event)">
          <i class="fa fa-save"></i> 保存
        </button>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8 col-sm-10 col-md-offset-2 col-sm-offset-1">
        <div class="form-inline">
          <table class="table table-color-bordered">
            <tbody>
              <tr>
                <th>工場</th>
                <td class="text-left">{{ factory.factory_abbreviation }}</td>
                <th>品種</th>
                <td class="text-left">{{ species.species_name }}</td>
              </tr>
              <tr>
                <th>商品規格</th>
                <td>
                  <select class="form-control" v-model="packagingStyle" @change="searchStockedProductsBypackagingStyle">
                    <option></option>
                    <option v-for="(ps, index) in packagingStyles" :key="index" :value="ps">
                      {{ ps.number_of_heads }}株
                      {{ ps.weight_per_number_of_heads }}g
                      {{ inputGroupList[ps.input_group] }}
                    </option>
                  </select>
                </td>
                <th>引当方法</th>
                <td>
                  <label class="radio-inline">
                    <input type="radio" name="allocation_type" value="auto" v-model="allocationType"> 自動引当
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="allocation_type" value="manual" v-model="allocationType"> 手動引当
                  </label>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="harvestingDates.length !== 0" class="row">
      <div class="col-md-2 col-md-offset-2 col-sm-3 col-sm-offset-2">
        <label class="header">引当状況</label>
        <table class="table allocation-status">
          <tbody>
            <tr>
              <td class="legend" :style="{backgroundColor: allocationStatusColors['not-allocated']}"></td>
              <td class="buffer"></td>
              <td class="text-center status">未引当</td>
            </tr>
            <tr class="border">
              <td class="border" colspan="3"></td>
            </tr>
            <tr>
              <td class="legend" :style="{backgroundColor: allocationStatusColors['allocated']}"></td>
              <td class="buffer"></td>
              <td class="text-center status">引当済</td>
            </tr>
            <tr class="border">
              <td class="border" colspan="2"></td>
            </tr>
            <tr>
              <td class="legend" :style="{backgroundColor: allocationStatusColors['allocated-partially']}"></td>
              <td class="buffer"></td>
              <td class="text-center status">部分引当</td>
            </tr>
            <tr class="border">
              <td class="border" colspan="2"></td>
            </tr>
            <tr>
              <td class="legend" :style="{backgroundColor: allocationStatusColors['allocated-at-other-warehouse']}"></td>
              <td class="buffer"></td>
              <td class="text-center status">他倉庫引当</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="col-md-6 col-md-offset-1 col-sm-7 form-inline">
        <div class="form-group">
          <label class="header">在庫</label>
          <select id="warehouse-code" class="form-control" v-model="warehouse" @change="searchStockedProductsBypackagingStyle">
            <option v-for="w in warehouses" :key="w.warehouse_code" :value="w">{{ w.warehouse_abbreviation }}</option>
          </select>
        </div>
        <div class="row">
          <div class="col-lg-4 col-md-4 col-sm-4">
            <table class="product-stocks">
              <tbody>
                <tr v-for="w in warehouses" :key="w.warehouse_code">
                  <td class="warehouse-name">{{ w.warehouse_abbreviation }}</td>
                  <td class="stock">{{ numberFormat(getTotalStockQuantityByWarehouse(w)) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-for="(harvesting_dates, week_index) in this.chunkedHarvestingDates" :key="week_index" class="col-lg-4 col-md-4 col-sm-4">
            <table class="product-stocks">
              <tbody>
                <tr v-for="hd in harvesting_dates" :key="hd.date">
                  <td class="harvesting-date">{{ hd.date_ja }}</td>
                  <td
                    class="stock"
                    :class="{'prev-week': week_index === 0}"
                    :style="{backgroundColor: hd.label_color}">
                    {{ numberFormat(stockQuantities[hd.date]) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div v-if="factoryProducts.length !== 0" class="row">
      <div class="col-sm-offset-1 shipping-date-term">
        <div class="text-center">
          <h4>
            出荷日:
            {{ shippingDates[0].date_except_year }}({{ shippingDates[0].day_of_the_week_ja }})&nbsp;～
            {{ shippingDates[shippingDates.length - 1].date_except_year }}({{ shippingDates[shippingDates.length - 1].day_of_the_week_ja }})
          </h4>
        </div>
      </div>

      <div class="col-md-offset-1 pull-left shipping-dates">
        <table class="table table-color-bordered">
          <thead>
            <tr class="factory-product-row">
              <th colspan="2">&nbsp;</th>
            </tr>
            <tr class="delivery-destination-row">
              <th colspan="2"></th>
            </tr>
          </thead>
          <tbody>
            <tr class="delivery-lead-time-row">
              <th colspan="2">出荷日&nbsp;＼&nbsp;配送LT</th>
            </tr>
            <tr v-for="(sd, idx) in shippingDates" :key="sd.date" class="product-allocation-row">
              <th
                class="shipping-date"
                :class="{'pointer-cursor': canSaveProductAllocation && allocationType === 'auto'}"
                @click="allocateProducts(idx)">
                {{ sd.date_except_year }}
              </th>
              <th class="day-of-the-week">{{ sd.day_of_the_week_ja }}</th>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="allocation-quantities">
        <table class="table table-color-bordered">
          <colgroup>
            <template v-for="fp in factoryProducts">
              <col
                v-for="dd in fp.delivery_destinations"
                :key="[fp.factory_product_sequence_number, dd.delivery_destination_code].join('-')"
                class="delivery-destination-col">
            </template>
          </colgroup>
          <thead>
            <tr class="factory-product-row">
              <th
                v-for="fp in factoryProducts"
                :key="fp.factory_product_sequence_number"
                :colspan="fp.delivery_destinations.length">
                {{ fp.factory_product_abbreviation }}
              </th>
            </tr>
            <tr class="delivery-destination-row">
              <template v-for="fp in factoryProducts">
                <th
                  v-for="dd in fp.delivery_destinations"
                  :key="[fp.factory_product_sequence_number, dd.delivery_destination_code].join('-')"
                  class="delivery-destination-name">
                  <p v-for="(splited, idx) in splitDeliveryDestinationName(dd.delivery_destination_abbreviation)" :key="idx">
                    {{ splited }}
                  </p>
                </th>
              </template>
            </tr>
          </thead>
          <tbody>
            <tr class="delivery-lead-time-row">
              <template v-for="fp in factoryProducts">
                <th
                  v-for="dd in fp.delivery_destinations"
                  :key="[fp.factory_product_sequence_number, dd.delivery_destination_code].join('-')">
                  {{ dd.delivery_lead_time }}
                </th>
              </template>
            </tr>
            <tr v-for="(sd, idx) in shippingDates" :key="sd.date" class="product-allocation-row">
              <template v-for="fp in factoryProducts">
                <td v-for="dd in fp.delivery_destinations"
                  :key="[fp.factory_product_sequence_number, dd.delivery_destination_code, sd.date].join('-')"
                  class="allocation-status-per-shipping-date">
                  <allocation-status-per-shipping-date
                    :allocation-type="allocationType"
                    :warehouse="warehouse"
                    :harvesting-dates="harvestingDates"
                    :stock-quantities="stockQuantities | clone"
                    :factory-product="fp"
                    :delivery-destination="dd"
                    :shipping-date="sd"
                    :order="dd.shipping_dates[idx]"
                    :allocation-status-colors="allocationStatusColors"
                    :allocation-quantities="allocationQuantitiesList[fp.factory_product_sequence_number][dd.delivery_destination_code][sd.date]"
                    :warning-date-term-of-allocation="warningDateTermOfAllocation"
                    :number-format="numberFormat"
                    :can-save-product-allocation="canSaveProductAllocation"
                    v-on:fix-allocation="fixAllocation">
                  </allocation-status-per-shipping-date>
                </td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <form ref="form" :action="currentUrl" method="POST">
      <input type="hidden" name="warehouse_code" :value="selectedWarehouseCode">
      <input v-for="fp of factoryProducts" :key="fp.factory_product_sequence_number"
        type="hidden"
        name="factory_product_sequence_numbers[]"
        :value="fp.factory_product_sequence_number">
      <template v-for="(deliveryDestinations, seqNum) of allocationQuantitiesList">
        <template v-for="(shippingDates, ddCode) of deliveryDestinations">
          <template v-for="(harvestingDates, sd) of shippingDates">
            <input v-for="(quantity, hd) of harvestingDates"
              :key="[seqNum, ddCode, sd, hd].join('-')"
              type="hidden"
              :name="'factory_products[' + seqNum + '][' + ddCode + '][' + sd + '][' + hd + ']'"
              :value="quantity">
          </template>
        </template>
      </template>
      <input name="_token" type="hidden" :value="csrf">
      <input name="_method" type="hidden" value="POST">
    </form>
  </div>
</template>

<script>
export default {
  props: {
    factory: {
      type: Object,
      required: true
    },
    species: {
      type: Object,
      required: true
    },
    packagingStyles: {
      type: Array,
      required: true
    },
    inputGroupList: {
      type: Object,
      required: true
    },
    warehouses: {
      type: Array,
      required: true
    },
    shippingDates: {
      type: Array,
      required: true
    },
    selectedPackagingStyle: {
      type: Object,
      required: true
    },
    selectedWarehouseCode: {
      type: String,
      required: true
    },
    factoryProducts: {
      type: Array,
      required: true
    },
    warningDateTermOfAllocation: {
      type: Number,
      required: true
    },
    hrefToIndexOfProductizedResults: {
      type: String,
      required: true
    },
    canSaveProductAllocation: {
      type: Boolean,
      required: true
    }
  },
  data: function () {
    const warehouse = this.warehouses.filter(w => w.warehouse_code === this.selectedWarehouseCode)[0] || {},
      harvestingDates = warehouse.hasOwnProperty('stock') ? warehouse.stock.harvesting_dates : []

    let stockQuantities = {}
    for (const hd of harvestingDates) {
      stockQuantities[hd.date] = hd.stock_quantity
    }

    let allocationQuantitiesList = {}
    for (const fp of this.factoryProducts) {
      allocationQuantitiesList[fp.factory_product_sequence_number] = {}
      for (const dd of fp.delivery_destinations) {
        allocationQuantitiesList[fp.factory_product_sequence_number][dd.delivery_destination_code] = {}
        for (const [idx, sd] of this.shippingDates.entries()) {
          allocationQuantitiesList[fp.factory_product_sequence_number][dd.delivery_destination_code][sd.date] =
            dd.shipping_dates[idx].allocation_quantities
        }
      }
    }

    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      currentUrl: location.href.split('?')[0],
      packagingStyle: this.selectedPackagingStyle,
      warehouse,
      harvestingDates,
      allocationType: 'auto',
      allocationStatusColors: {
        'not-allocated': '#ffffff',
        'allocated': '#a9a9a9',
        'allocated-partially': '#fbd5d5',
        'allocated-at-other-warehouse': '#c4bd97'
      },
      stockQuantities,
      allocationQuantitiesList,
      allocationHasChanged: false,
      confirmMessage: '保存されていない情報があります。画面を切り替えてよろしいですか？'
    }
  },
  computed: {
    chunkedHarvestingDates: function () {
      const _ = require('lodash')
      return _.chunk(this.harvestingDates, 7)
    }
  },
  methods: {
    numberFormat: function (number) {
      const number_format = require('locutus/php/strings/number_format')
      return number_format(number)
    },
    splitDeliveryDestinationName: function (deliveryDestinationName) {
      return deliveryDestinationName.replace(/　/g, ' ').split(' ').reverse()
    },
    searchStockedProductsBypackagingStyle: function () {
      if (this.allocationHasChanged && ! confirm(this.confirmMessage)) {
        return
      }

      location.href = this.currentUrl +
        '?warehouse_code=' + this.warehouse.warehouse_code +
        '&number_of_heads=' + (this.packagingStyle.number_of_heads || '') +
        '&weight_per_number_of_heads=' + (this.packagingStyle.weight_per_number_of_heads || '') +
        '&input_group=' + (this.packagingStyle.input_group || '')
    },
    getTotalStockQuantityByWarehouse: function (warehouse) {
      if (warehouse.warehouse_code !== this.selectedWarehouseCode) {
        return warehouse.stock.total_stock_quantity
      }

      let totalStockQuantity = 0
      for (const hd in this.stockQuantities) {
        totalStockQuantity += this.stockQuantities[hd]
      }

      return totalStockQuantity
    },
    allocateProducts: function (shippingDateIdx) {
      if (! this.canSaveProductAllocation) {
        return
      }

      if (this.allocationType === 'manual') {
        return
      }

      const shippingDate = this.shippingDates[shippingDateIdx],
        harvestingDates = this.harvestingDates.filter((hd) => {
          const moment = require('moment')
          return moment(shippingDate.date, 'YYYY/MM/DD').isSameOrAfter(moment(hd.date, 'YYYY/MM/DD'))
        })

      for (const fp of this.factoryProducts) {
        for (const dd of fp.delivery_destinations) {
          for (const [idx, sd] of this.shippingDates.entries()) {
            if (shippingDate.date !== sd.date) {
              continue
            }

            const order = dd.shipping_dates[idx]
            if (order.had_been_shipped || order.allocated_at_other_warehouse) {
              continue
            }

            const currentAllocationQuantities =
              this.allocationQuantitiesList[fp.factory_product_sequence_number][dd.delivery_destination_code][sd.date]
            if (Object.keys(currentAllocationQuantities).length > 0) {
              continue
            }

            const numberOfCases = fp.number_of_cases,
              sumOfStockQuantity = harvestingDates.reduce((stock, hd) => {
                return stock += this.stockQuantities[hd.date]
              }, 0)

            let allocationQuantity = order.order_quantity
            if ((allocationQuantity * numberOfCases) > sumOfStockQuantity) {
              allocationQuantity = Math.floor(sumOfStockQuantity / numberOfCases)
            }

            if (allocationQuantity === 0) {
              break
            }

            let calculatedAllocationQuantities = {},
              sumOfAllocationQuantity = allocationQuantity * numberOfCases
            for (const hd of harvestingDates) {
              let stockQuantity = this.stockQuantities[hd.date] + (currentAllocationQuantities[hd.date] || 0)

              let calculatedAllocationQuantity = stockQuantity >= sumOfAllocationQuantity ?
                sumOfAllocationQuantity :
                stockQuantity

              if (calculatedAllocationQuantity === 0) {
                continue
              }

              calculatedAllocationQuantities[hd.date] = calculatedAllocationQuantity
              sumOfAllocationQuantity -= calculatedAllocationQuantities[hd.date]
              if (sumOfAllocationQuantity === 0) {
                break
              }
            }

            this.fixAllocation(calculatedAllocationQuantities, fp, dd, sd)
          }
        }
      }
    },
    fixAllocation: function (allocationQuantities, factoryProduct, deliveryDestination, shippingDate) {
      const old =
        this.allocationQuantitiesList[factoryProduct.factory_product_sequence_number][deliveryDestination.delivery_destination_code][shippingDate.date]
      for (const [hd, quantity] of Object.entries(old)) {
        this.stockQuantities[hd] += quantity
      }
      for (const [hd, quantity] of Object.entries(allocationQuantities)) {
        this.stockQuantities[hd] -= quantity
      }

      this.allocationQuantitiesList[factoryProduct.factory_product_sequence_number][deliveryDestination.delivery_destination_code][shippingDate.date] =
        allocationQuantities

      this.allocationHasChanged = true
    },
    confirmLeave: function (event) {
      if (this.allocationHasChanged && ! confirm(this.confirmMessage)) {
        event.preventDefault()
      }
    },
    saveProductAllocations: function (event) {
      if (confirm('データを登録します。よろしいでしょうか？')) {
        event.target.disabled = true
        this.$refs.form.submit()
      }
    }
  }
}
</script>
