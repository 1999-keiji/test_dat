<style scoped>
  .modal-header, .modal-footer, .basic-form {
    cursor: default;
  }
  table.product-stocks-table {
    margin-top: 1.5em;
    margin-bottom: 2.5em;
  }
  table.product-stocks-table>thead>tr>th.allocation-quantity {
    width: 25%;
  }
  table.quantity-table>tbody>tr>td {
    width: 33%;
  }
</style>

<template>
  <modal v-model="showModal" :backdrop="false" effect="fade">
    <div slot="modal-header" class="modal-header">
      <h4 class="modal-title">
        {{ deliveryDestination.delivery_destination_abbreviation + ' ' + shippingDate.date + '(' + shippingDate.day_of_the_week_ja + ')' }}
        <span v-if="hadBeenShipped" class="text-success">
          <b>[出荷済]</b>
        </span>
      </h4>
    </div>
    <div slot="modal-footer" class="modal-footer">
      <button v-if="! hadBeenShipped && canSaveProductAllocation" class="btn btn-success" type="button" :disabled="! canAllocateManually" @click="fixAllocation">OK</button>
      <button class="btn btn-default" type="button" @click="closeModal">キャンセル</button>
    </div>
    <div class="form-horizontal basic-form">
      <div class="row">
        <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label">工場商品</label>
        <div class="col-md-3 col-sm-3 text-left">
          <span class="shown_label">{{ factoryProduct.factory_product_abbreviation }}</span>
        </div>
        <label class="col-md-2 col-sm-2 control-label">倉庫</label>
        <div class="col-md-3 col-sm-3 text-left">
          <span class="shown_label">{{ warehouse.warehouse_abbreviation }}</span>
        </div>
      </div>
      <p class="pull-right">単位: 袋</p>
      <table class="table table-color-bordered table-more-condensed product-stocks-table">
        <thead>
          <tr>
            <th>引当</th>
            <th>収穫日</th>
            <th>製品化数</th>
            <th class="allocation-quantity">引当数</th>
            <th>在庫数</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="hd in harvestingDates" :key="hd.date">
            <td>
              <input type="checkbox" :value="hd.date" :disabled="hadBeenShipped || ! canSaveProductAllocation" v-model="allocatedHarvestingDates">
            </td>
            <td>{{ hd.date_ja }}</td>
            <td class="text-right">{{ numberFormat(hd.product_quantity) }}</td>
            <td>
              <input
                class="form-control text-right"
                type="text"
                :value="calculatedAllocationQuantities[hd.date]"
                :disabled="! allocatedHarvestingDates.includes(hd.date)"
                @change="changeQuantityManually($event, hd.date)">
            </td>
            <td class="text-right">{{ numberFormat(temporaryStockQuantities[hd.date]) }}</td>
          </tr>
        </tbody>
      </table>
      <div class="row">
        <div class="col-md-8 col-sm-8 col-md-offset-2 col-sm-offset-2">
          <p class="pull-right">単位: {{ factoryProduct.unit }}</p>
          <table class="table table-color-bordered table-more-condensed quantity-table">
            <thead>
              <tr>
                <th>受注数</th>
                <th>引当数</th>
                <th>残数</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-right">{{ numberFormat(orderQuantity) }}</td>
                <td class="text-right">
                  <input
                    v-model="inputtedAllocationQuantity"
                    class="form-control text-right"
                    :disabled="hadBeenShipped || ! canSaveProductAllocation"
                    @change="allocateProducts">
                </td>
                <td class="text-right" :class="{'text-danger': remainingQuantity < 0}">{{ numberFormat(remainingQuantity) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </modal>
</template>

<script>
import VueStrap from 'vue-strap'

export default {
  components: {Modal: VueStrap.modal},
  props: {
    showModal: {
      type: Boolean,
      required: true
    },
    warehouse: {
      type: Object,
      required: true
    },
    harvestingDates: {
      type: Array,
      required: true
    },
    stockQuantities: {
      type: Object,
      required: true
    },
    factoryProduct: {
      type: Object,
      required: true
    },
    deliveryDestination: {
      type: Object,
      required: true
    },
    shippingDate: {
      type: Object,
      required: true
    },
    orderQuantity: {
      type: Number,
      required: true
    },
    allocationQuantities: {
      type: Object,
      required: true
    },
    sumOfAllocationQuantity: {
      type: Number,
      required: true
    },
    hadBeenShipped: {
      type: Boolean,
      required: true
    },
    canSaveProductAllocation: {
      type: Boolean,
      required: true
    },
    numberFormat: {
      type: Function,
      required: true
    }
  },
  data: function () {
    const _ = require('lodash')
    return {
      temporaryStockQuantities: _.cloneDeep(this.stockQuantities),
      calculatedAllocationQuantities: _.cloneDeep(this.allocationQuantities),
      allocatedHarvestingDates: Object.keys(this.allocationQuantities),
      inputtedAllocationQuantity: this.sumOfAllocationQuantity,
      lodash: _
    }
  },
  watch: {
    stockQuantities: function () {
      this.resetAllocation()
    }
  },
  computed: {
    remainingQuantity: function () {
      return this.orderQuantity - this.inputtedAllocationQuantity
    },
    canAllocateManually: function () {
      return this.remainingQuantity >= 0
    },
    checkedHarvestingDates: function () {
      return this.harvestingDates.filter((hd) => this.allocatedHarvestingDates.includes(hd.date))
    },
    sumOfStockQuantity: function () {
      return this.checkedHarvestingDates.reduce((stock, hd) => {
        return stock += this.stockQuantities[hd.date] + (this.calculatedAllocationQuantities[hd.date] || 0)
      }, 0)
    }
  },
  methods: {
    allocateProducts: function () {
      if (this.inputtedAllocationQuantity === '') {
        this.inputtedAllocationQuantity = 0
      }
      if (parseInt(this.inputtedAllocationQuantity) === 0) {
        for (const [date, quantity] of Object.entries(this.calculatedAllocationQuantities)) {
          this.temporaryStockQuantities[date] += quantity
        }

        this.calculatedAllocationQuantities = {}
        this.allocatedHarvestingDates = []
        return
      }

      if (! this.canAllocateManually) {
        alert('注文数を上回る数値は入力できません。')

        this.resetAllocation()
        return
      }

      if (this.allocatedHarvestingDates.length === 0) {
        alert('引当対象の収穫日が選択されていません。')

        this.resetAllocation()
        return
      }

      let allocationQuantity = this.inputtedAllocationQuantity * this.factoryProduct.number_of_cases
      if (this.sumOfStockQuantity < allocationQuantity) {
        alert('選択された収穫日の製品化数の合計が、引当数に対して不足しています。')

        this.resetAllocation()
        return
      }

      let calculatedAllocationQuantities = {}
      for (const hd of this.checkedHarvestingDates) {
        let stockQuantity = this.temporaryStockQuantities[hd.date] + (this.calculatedAllocationQuantities[hd.date] || 0)

        calculatedAllocationQuantities[hd.date] = stockQuantity >= allocationQuantity ?
          allocationQuantity :
          stockQuantity

        allocationQuantity -= calculatedAllocationQuantities[hd.date]
        if (allocationQuantity === 0) {
          break
        }
      }

      for (const [date, quantity] of Object.entries(this.calculatedAllocationQuantities)) {
        this.temporaryStockQuantities[date] += quantity
      }
      for (const [date, quantity] of Object.entries(calculatedAllocationQuantities)) {
        this.temporaryStockQuantities[date] -= quantity
      }

      this.calculatedAllocationQuantities = calculatedAllocationQuantities
      this.allocatedHarvestingDates = Object.keys(this.calculatedAllocationQuantities)
    },
    changeQuantityManually: function (event, date) {
      if (isNaN(event.target.value)) {
        alert('数値を入力してください。')

        this.resetAllocation()
        return
      }

      this.temporaryStockQuantities[date] += this.calculatedAllocationQuantities[date] || 0

      const quantity = parseInt(event.target.value)
      this.temporaryStockQuantities[date] -= quantity
      if (this.temporaryStockQuantities[date] < 0) {
        alert('在庫数が0を下回っています。引当をリセットします。')

        this.resetAllocation()
        return
      }

      this.calculatedAllocationQuantities[date] = quantity || ''
    },
    resetAllocation: function () {
      this.temporaryStockQuantities = this.lodash.cloneDeep(this.stockQuantities)
      this.calculatedAllocationQuantities = this.lodash.cloneDeep(this.allocationQuantities)
      this.allocatedHarvestingDates = Object.keys(this.allocationQuantities)
      this.inputtedAllocationQuantity = this.sumOfAllocationQuantity
    },
    fixAllocation: function () {
      const sumOfAllocationQuantity = Object.values(this.calculatedAllocationQuantities)
        .reduce((sum, quantity) => sum += quantity, 0)

      if ((sumOfAllocationQuantity / this.factoryProduct.number_of_cases) !== parseInt(this.inputtedAllocationQuantity)) {
        alert('収穫日ごとの引当数の合計を入数で割った値と、受注に対する引当数が一致するようにしてください。')
        return
      }

      this.$emit('fix-allocation', this.calculatedAllocationQuantities)
      this.$emit('close-modal')
    },
    closeModal: function () {
      this.resetAllocation()
      this.$emit('close-modal')
    }
  }
}
</script>
