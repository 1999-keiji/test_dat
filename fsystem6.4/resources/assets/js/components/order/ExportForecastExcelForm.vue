<template>
  <table class="table table-color-bordered">
    <tbody>
      <tr>
        <th class="col-md-2 col-sm-2 col-xs-2">
          工場
          <span class="required-mark">*</span>
        </th>
        <td class="col-md-4 col-sm-4 col-xs-4">
          <select id="factory_code" class="form-control" name="factory_code" v-model="factoryCode" @change="getSpeciesList">
            <option value=""></option>
            <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
          </select>
        </td>
        <th class="col-md-2 col-sm-2 col-xs-2">
          品種
          <span class="required-mark">*</span>
        </th>
        <td class="col-md-4 col-sm-4 col-xs-4">
          <select id="species_code" class="form-control" name="species_code" v-model="speciesCode" :disabled="disabledToSelectSpecies" @change="getFactoryProducts">
            <option value=""></option>
            <option v-for="s in speciesList" :key="s.species_code" :value="s.species_code">{{ s.species_name }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>工場取扱商品</th>
        <td>
          <select id="factory_product_sequence_number" class="form-control" name="factory_product_sequence_number" v-model="factoryProductSequenceNumber" :disabled="disabledToFactoryProduct">
            <option value=""></option>
            <option v-for="fp in factoryProducts" :key="fp.sequence_number" :value="fp.sequence_number">{{ fp.factory_product_abbreviation }}</option>
          </select>
        </td>
        <th>納入先</th>
        <td>
          <search-master
            target="delivery_destination"
            :code="oldParams.delivery_destination_code || ''"
            :name="oldParams.delivery_destination_name || ''">
          </search-master>
        </td>
      </tr>
      <tr>
        <th>
          納入日
          <span class="required-mark">*</span>
        </th>
        <td>
          <datepicker-ja
            attr-name="delivery_date"
            :date="deliveryDate"
            :disabled-days-of-week="['0', '2', '3', '4', '5', '6']"
            @update-date="updateDeliveryDate">
          </datepicker-ja>
          から{{ weekTermOfExportOrderForecast }}週間
          <input name="harvesting_date" type="hidden" :value="deliveryDate">
          <input name="display_term" type="hidden" value="date">
          <input name="week_term" type="hidden" :value="weekTermOfExportOrderForecast">
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: {
    factories: {
      type: Array,
      required: true
    },
    oldParams: {
      type: Object,
      required: true
    },
    defaultDeliveryDate: {
      type: String,
      required: true
    },
    weekTermOfExportOrderForecast: {
      type: Number,
      required: true
    }
  },
  data: function () {
    return {
      factoryCode: this.oldParams.factory_code || null,
      speciesList: [],
      speciesCode: this.oldParams.species_code || null,
      factoryProducts: [],
      factoryProductSequenceNumber: this.oldParams.factory_product_sequence_number || null,
      deliveryDate: this.oldParams.delivery_date || this.defaultDeliveryDate
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getSpeciesList()
      this.speciesCode = this.oldParams.species_code
    }

    if (this.factoryCode && this.speciesCode) {
      this.getFactoryProducts()
      this.factoryProductSequenceNumber = this.oldParams.factory_product_sequence_number
    }
  },
  computed: {
    disabledToSelectSpecies: function () {
      return this.speciesList.length === 0
    },
    disabledToFactoryProduct: function () {
      return this.factoryProducts.length === 0
    }
  },
  methods: {
    getSpeciesList: function () {
      this.speciesCode = this.factoryProductSequenceNumber = null
      this.speciesList = this.factoryProducts = []
      if (! this.factoryCode) {
        return
      }

      axios.get('/api/get-species-with-factory-code', {
        params: {
          factory_code: this.factoryCode
        }
      })
        .then(response => {
          this.speciesList = response.data
          if (this.disabledToSelectSpecies) {
            alert('工場取扱品種が未登録の工場です。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    getFactoryProducts: function () {
      this.factoryProductSequenceNumber = null
      this.factoryProducts = []
      if (! (this.factoryCode && this.speciesCode)) {
        return
      }

      axios.get('/api/get-factory-products', {
        params: {
          factory_code: this.factoryCode,
          species_code: this.speciesCode
        }
      })
        .then(response => {
          this.factoryProducts = response.data
          if (this.disabledToFactoryProduct) {
            alert('工場取扱商品が未登録の品種です。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    updateDeliveryDate: function (deliveryDate) {
      this.deliveryDate = deliveryDate
    }
  }
}
</script>
