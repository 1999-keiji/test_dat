<style scoped>
  table.orders-table {
    width: 100%;
    height: 100%;
  }
  table.orders-table.pointer-cursor {
    cursor: pointer;
  }
  table.orders-table>tbody>tr>td.allocation-quantity {
    height: 40%;
    border-bottom: solid 1px #667f34;
  }
  table.orders-table>tbody>tr>td.allocation-quantity.prev-week {
    background-image: -webkit-radial-gradient(#555 20%, transparent 0), -webkit-radial-gradient(#555 20%, transparent 0);
    background-image: radial-gradient(#555 20%, transparent 0), radial-gradient(#555 20%, transparent 0);
    background-position: 0 0, 10px 10px;
    -webkit-background-size: 5px 5px;
    background-size: 5px 5px;
  }
  table.orders-table>tbody>tr>td.order-quantity {
    height: 60%;
  }
</style>

<template>
  <table class="orders-table" :class="{'pointer-cursor': canAllocate}" @click="allocateProducts">
    <tbody>
      <tr>
        <td
          class="allocation-quantity"
          :class="allocatedWeekStyle"
          :style="allocatedDateStyle">
          <span v-if="canAllocate" :style="allocatedQuantityStyle">
            {{ numberFormat(sumOfAllocationQuantity) }}
          </span>
        </td>
      </tr>
      <tr>
        <td class="order-quantity" :style="allocationStatusStyle">
          <span v-if="canAllocate">
            {{ numberFormat(order.order_quantity) }}
          </span>
          <input-allocation-quantity
            v-if="canAllocate"
            :show-modal="showModal"
            :warehouse="warehouse"
            :harvesting-dates="allocatableHarvestingDates"
            :stock-quantities="stockQuantities | clone"
            :factory-product="factoryProduct"
            :delivery-destination="deliveryDestination"
            :shipping-date="shippingDate"
            :order-quantity="order.order_quantity"
            :allocation-quantities="allocationQuantities"
            :sum-of-allocation-quantity="sumOfAllocationQuantity"
            :had-been-shipped="order.had_been_shipped"
            :can-save-product-allocation="canSaveProductAllocation"
            :number-format="numberFormat"
            v-on:fix-allocation="fixAllocation"
            v-on:close-modal="closeModal">
          </input-allocation-quantity>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: {
    allocationType: {
      type: String,
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
    order: {
      type: Object,
      required: true
    },
    allocationStatusColors: {
      type: Object,
      required: true
    },
    allocationQuantities: {
      type: Object,
      required: true
    },
    warningDateTermOfAllocation: {
      type: Number,
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
    const orderQuantity = this.order.order_quantity
    return {
      orderQuantity,
      canAllocate: orderQuantity !== 0,
      numberOfCases: this.factoryProduct.number_of_cases,
      showModal: false,
      moment: require('moment')
    }
  },
  computed: {
    allocatableHarvestingDates: function () {
      return this.harvestingDates.filter((hd) => {
        return this.moment(this.shippingDate.date, 'YYYY/MM/DD').isSameOrAfter(this.moment(hd.date, 'YYYY/MM/DD'))
      })
    },
    sumOfAllocationQuantity: function () {
      if (! this.canAllocate) {
        return null
      }

      return Object.values(this.allocationQuantities).reduce((sum, quantity) => {
        return sum + quantity
      }, 0) / this.numberOfCases
    },
    sumOfStockQuantity: function () {
      return this.allocatableHarvestingDates.reduce((stock, hd) => {
        return stock += this.stockQuantities[hd.date] + (this.allocationQuantities[hd.date] || 0)
      }, 0)
    },
    oldestHarvestingDate: function () {
      return Object.keys(this.allocationQuantities).sort()[0]
    },
    harvestingDateStyle : function () {
      return this.harvestingDates.filter(hd => hd.date === this.oldestHarvestingDate)[0]
    },
    allocationStatusStyle: function () {
      if (! this.canAllocate) {
        return {}
      }

      if (this.order.allocated_at_other_warehouse) {
        return {'background-color': this.allocationStatusColors['allocated-at-other-warehouse']}
      }
      if (this.sumOfAllocationQuantity === 0) {
        return {'background-color': this.allocationStatusColors['not-allocated']}
      }
      if (this.orderQuantity === this.sumOfAllocationQuantity) {
        return {'background-color': this.allocationStatusColors['allocated']}
      }
      if (this.orderQuantity > this.sumOfAllocationQuantity) {
        return {'background-color': this.allocationStatusColors['allocated-partially']}
      }
    },
    allocatedDateStyle: function () {
      if (this.sumOfAllocationQuantity === 0) {
        return {}
      }
      if (! this.harvestingDateStyle) {
        return {}
      }

      return {
        backgroundColor: this.harvestingDateStyle.label_color
      }
    },
    allocatedWeekStyle: function () {
      if (this.sumOfAllocationQuantity === 0) {
        return {}
      }
      if (! this.harvestingDateStyle) {
        return {}
      }

      return {
        'prev-week': this.harvestingDateStyle.prev_week
      }
    },
    allocatedQuantityStyle: function () {
      if (this.sumOfAllocationQuantity === 0) {
        return {}
      }

      if (! this.oldestHarvestingDate) {
        return {}
      }

      const diff = this.moment(this.oldestHarvestingDate, 'YYYY/MM/DD')
        .diff(this.moment(this.shippingDate.date, 'YYYY/MM/DD'), 'days')

      return Math.abs(diff) >= this.warningDateTermOfAllocation ? {color: '#b5b5b5'} : {}
    }
  },
  methods: {
    allocateProducts: function () {
      if (this.order.allocated_at_other_warehouse) {
        alert('他倉庫で引当済のため、引当を変更することはできません。')
        return
      }

      if (this.allocationType === 'manual') {
        this.showModal = true
        return
      }

      if (! this.canSaveProductAllocation) {
        return
      }

      if (this.order.had_been_shipped) {
        alert('出荷確定済のため、引当を変更することはできません。')
        return
      }
      if (this.sumOfAllocationQuantity > 0) {
        alert('引当実績のある出荷日です。引当数を変更する場合、手動で引当してください。')
        return
      }

      let allocationQuantity = this.orderQuantity
      if ((allocationQuantity * this.numberOfCases) > this.sumOfStockQuantity) {
        allocationQuantity = Math.floor(this.sumOfStockQuantity / this.numberOfCases)
      }

      if (allocationQuantity === 0) {
        alert('引当可能なだけの在庫がありません。')
        return
      }

      let calculatedAllocationQuantities = {},
        sumOfAllocationQuantity = allocationQuantity * this.numberOfCases
      for (const hd of this.allocatableHarvestingDates) {
        const stockQuantity = this.stockQuantities[hd.date]

        const calculatedAllocationQuantity = stockQuantity >= sumOfAllocationQuantity ?
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

      this.fixAllocation(calculatedAllocationQuantities)
    },
    closeModal: function () {
      this.showModal = false
    },
    fixAllocation: function (allocationQuantities) {
      this.$emit('fix-allocation', allocationQuantities, this.factoryProduct, this.deliveryDestination, this.shippingDate)
    }
  }
}
</script>
