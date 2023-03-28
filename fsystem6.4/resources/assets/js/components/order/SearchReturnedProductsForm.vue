<style scoped>
  table.table>tbody>tr>th {
    width: 15%;
  }
  table.table>tbody>tr>td {
    width: 35%;
  }
</style>

<template>
  <div class="col-md-8 col-sm-8 col-xs-10 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered">
      <tbody>
        <tr>
          <th>工場<span class="required-mark">*</span>
          </th>
          <td>
            <select class="form-control" name="factory_code" v-model="factoryCode" @change="getFactoryProducts">
              <option value=""></option>
              <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
            </select>
          </td>
          <th>注文日</th>
          <td>
            <datepicker-ja attr-name="received_date" :date="receivedDate" :allow-empty="true">
            </datepicker-ja>
          </td>
        </tr>
        <tr>
          <th>納入日</th>
          <td>
            <datepicker-ja attr-name="delivery_date" :date="deliveryDate" :allow-empty="true">
            </datepicker-ja>
          </td>
          <th>エンドユーザ</th>
          <td class="text-left">
            <search-master target="end_user" :code="endUserCode" :name="endUserName" :factory-code="factoryCode">
            </search-master>
          </td>
        </tr>
        <tr>
          <th>納入先</th>
          <td class="text-left">
            <search-master target="delivery_destination"
              :code="deliveryDestinationCode"
              :name="deliveryDestinationName"
              :factory-code="factoryCode"
              @get-selected-delivery-destination="getSelectedDeliveryDestination">
            </search-master>
          </td>
          <th>工場取扱商品</th>
          <td>
          <select id="product_code" class="form-control" name="product_code" v-model="productCode" :disabled="disabledToFactoryProduct">
            <option value=""></option>
            <option v-for="fp in factoryProducts" :key="fp.sequence_number" :value="fp.product_code">
              {{ fp.factory_product_name }}
            </option>
          </select>
          </td>
        </tr>
        <tr>
          <th>注文番号</th>
          <td>
            <input class="form-control ime-inactive" maxlength="14" name="order_number" :value="orderNumber">
          </td>
          <th>BASE+注文番号</th>
          <td>
            <input name="base_plus_order_number" class="form-control ime-active base_plus_num" :value="basePlusOrderNumber" maxlength="10" type="text">
            <input name="base_plus_order_chapter_number" class="form-control ime-active base_plus_chap" :value="basePlusOrderChapterNumber" maxlength="3" type="text">
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: {
    factories: {
      type: Array,
      required: true
    },
    searchParams: {
      type: Object,
      required: true
    },
    oldParams: {
      type: Object,
      required: true
    }
  },
  data: function () {
    return {
      factoryCode: this.oldParams.factory_code || this.searchParams.factory_code,
      receivedDate: this.oldParams.received_date || this.searchParams.received_date,
      deliveryDate: this.oldParams.delivery_date || this.searchParams.delivery_date,
      endUserCode: this.oldParams.end_user_code || this.searchParams.end_user_code,
      endUserName: this.oldParams.end_user_name || this.searchParams.end_user_name,
      deliveryDestinationCode: this.oldParams.delivery_destination_code || this.searchParams.delivery_destination_code,
      deliveryDestinationName: this.oldParams.delivery_destination_name || this.searchParams.delivery_destination_name,
      orderNumber: this.oldParams.order_number || this.searchParams.order_number,
      basePlusOrderNumber: this.oldParams.base_plus_order_number || this.searchParams.base_plus_order_number,
      basePlusOrderChapterNumber: this.oldParams.base_plus_order_chapter_number || this.searchParams.base_plus_order_chapter_number,
      productCode: this.oldParams.product_code || this.searchParams.product_code,
      factoryProducts: [],
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getFactoryProducts()
      this.productCode = this.oldParams.product_code || this.searchParams.product_code
    }
  },
  computed: {
    disabledToFactoryProduct: function () {
      return this.factoryProducts.length === 0
    }
  },
  methods: {
    getSelectedDeliveryDestination: function (deliveryDestination) {
      this.deliveryDestinationCode = deliveryDestination.code
      this.deliveryDestinationName = deliveryDestination.name
      this.endUserCode = deliveryDestination.end_user.code
      this.endUserName = deliveryDestination.end_user.name
    },
    getFactoryProducts: function () {
      this.productCode = null
      this.factoryProducts = []
      axios.get('/api/get-factory-products', {
        params: {
          factory_code: this.factoryCode,
        }
      })
        .then(response => {
          this.factoryProducts = response.data
          if (this.disabledToFactoryProduct) {
            alert('工場取扱商品が未登録の工場です。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    }
  }
}
</script>
