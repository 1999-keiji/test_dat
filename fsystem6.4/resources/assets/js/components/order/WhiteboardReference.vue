<style scoped>
  table.table-required-code {
    margin-bottom: 5px;
  }
  td.year-monthpicker-col {
    width: 130px;
  }
</style>

<template>
  <div>
    <form ref="form" method="POST" v-if="! isFullScreenMode">
      <div class="row">
        <div class="col-md-10 col-sm-10 col-xs-10">
          <table class="table table-color-bordered table-required-code">
            <tbody>
              <tr>
                <th>
                  年月<span class="required-mark">*</span>
                </th>
                <td class="year-monthpicker-col">
                  <year-monthpicker-ja name="year_month" :value="yearMonth"></year-monthpicker-ja>
                </td>
                <th>
                  工場
                  <span class="required-mark">*</span>
                </th>
                <td class="col-md-3 col-sm-3 col-sm-3">
                  <select
                    id="factory_code"
                    class="form-control"
                    :class="{'has-error': 'factory_code' in errors}"
                    name="factory_code"
                    v-model="factoryCode"
                    @change="getSpeciesList()">
                    <option value=""></option>
                    <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
                  </select>
                </td>
                <th>
                  品種
                  <span class="required-mark">*</span>
                </th>
                <td>
                  <select
                    id="species_code"
                    class="form-control"
                    :class="{'has-error': 'species_code' in errors}"
                    name="species_code"
                    v-model="speciesCode"
                    :disabled="disabledToSelectSpecies"
                    @change="getFactoryProducts()">
                    <option value=""></option>
                    <option v-for="s in speciesList" :key="s.species_code" :value="s.species_code">{{ s.species_name }}</option>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2" v-if="hasSearchParams">
          <button class="btn btn-lg btn-default pull-right" type="button" @click="startFullScreen()">
            <i class="fa fa-expand"></i> 全画面
          </button>
        </div>
      </div>

      <div class="row">
        <div class="col-md-9 col-sm-9 col-xs-10">
          <table class="table table-color-bordered">
            <tbody>
              <tr>
                <th>工場商品</th>
                <td>
                  <select
                    id="factory_product_sequence_number"
                    class="form-control"
                    :class="{'has-error': 'factory_product_sequence_number' in errors}"
                    name="factory_product_sequence_number"
                    v-model="factoryProductSequenceNumber"
                    :disabled="disabledToSelectFactoryProduct">
                    <option value=""></option>
                    <option v-for="fp in factoryProducts" :key="fp.sequence_number" :value="fp.sequence_number">{{ fp.factory_product_abbreviation }}</option>
                  </select>
                </td>
                <th>表示日付</th>
                <td>
                  <label class="radio-inline">
                    <input type="radio" name="output_date" v-model="outputDate" value="shipping_date">出荷日
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="output_date" v-model="outputDate" value="delivery_date">納入日
                  </label>
                </td>
                <th>出力条件</th>
                <td>
                  <label class="radio-inline" v-for="(value, label) in outputConditionList" :key="value">
                    <input type="radio" name="output_condition" v-model="outputCondition" :value="value">{{ label }}
                  </label>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-md-3 col-sm-3 col-xs-2">
          <button class="btn btn-lg btn-default pull-right" type="button" @click="exportSubmit">
            <i class="fa fa-download"></i> Excel出力
          </button>
          <button class="btn btn-lg btn-default pull-right" type="button" @click="searchSubmit">
            <i class="fa fa-search"></i> 検索
          </button>
        </div>
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="POST">
      </div>
    </form>
    <div class="row" v-if="hasSearchParams">
      <div class="col-md-10 col-sm-10 col-xs-10">
        最終表示日付：{{ displayedAt }}
      </div>
      <div class="col-md-2 col-sm-2 col-xs-2" v-if="isFullScreenMode">
        <a class="pull-right" href="#" @click="endFullScreen()">
          <i class="fa fa-compress"></i>元に戻す
        </a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    factories: {
      type: Array,
      required: true
    },
    outputConditionList: {
      type: Object,
      required: true
    },
    searchParams: {
      type: Object,
      required: true
    },
    oldParams: {
      type: Object,
      required: true
    },
    errors: {
      type: [Array, Object],
      required: true
    },
    reloadInterval: {
      type: Number,
      required: true
    },
    searchAction: {
      type: String,
      required: true
    },
    exportAction: {
      type: String,
      required: true
    }
  },
  data: function () {
    const moment = require('moment')
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      yearMonth: this.oldParams.year_month || this.searchParams.year_month || '',
      factoryCode: this.oldParams.factory_code || this.searchParams.factory_code || '',
      speciesList: [],
      speciesCode: this.oldParams.species_code || this.searchParams.species_code || '',
      factoryProducts: [],
      factoryProductSequenceNumber: this.oldParams.factory_product_sequence_number || this.searchParams.factory_product_sequence_number || null,
      outputDate: this.oldParams.output_date || this.searchParams.output_date || 'shipping_date',
      outputCondition: this.oldParams.output_condition || this.searchParams.output_condition || this.outputConditionList['全件'],
      displayedAt: moment().format('YYYY/MM/DD kk:mm'),
      isFullScreenMode: false,
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getSpeciesList()
      this.speciesCode = this.oldParams.species_code || this.searchParams.species_code
    }
    if (this.factoryCode && this.speciesCode) {
      this.getFactoryProducts()
      this.factoryProductSequenceNumber = this.oldParams.factory_product_sequence_number || this.searchParams.factory_product_sequence_number
    }

    if (this.searchParams.length !== 0) {
      setTimeout(() => { location.reload() }, this.reloadInterval * 1000)

      setInterval(() => {
        if (this.getFullScreenElement() == null && this.isFullScreenMode) {
          this.isFullScreenMode = false
        } else if(this.getFullScreenElement() != null && !this.isFullScreenMode) {
          this.isFullScreenMode = true
        }
      }, 500)
    }
  },
  watch: {
    isFullScreenMode: function () {
      $('#main-content').toggleClass('mode-full-screen', this.isFullScreenMode)
      setTimeout(() => { $(window).trigger('resize') }, 500)
    }
  },
  computed: {
    hasSearchParams: function () {
      return Object.keys(this.oldParams).length === 0 && Object.keys(this.searchParams).length !== 0
    },
    disabledToSelectSpecies: function () {
      return this.speciesList.length === 0
    },
    disabledToSelectFactoryProduct: function () {
      return this.factoryProducts.length === 0
    }
  },
  methods: {
    getSpeciesList: function () {
      this.speciesCode = ''
      this.factoryProductsSequenceNumber = null

      this.speciesList = this.factoryProducts = []
      if (!this.factoryCode) {
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

      if (! this.factoryCode || ! this.speciesCode) {
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
          if (this.disabledToSelectFactoryProduct) {
            alert('指定された品種の取扱のない工場です。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    searchSubmit: function () {
      $('.alert').remove()
      this.$refs.form.action = this.searchAction
      this.$refs.form.submit()
    },
    exportSubmit: function () {
      if (confirm('Excelをダウンロードしますか?')) {
        $('.alert').remove()
        this.$refs.form.action = this.exportAction
        this.$refs.form.submit()
      }
    },
    getFullScreenElement: function () {
      let fullScreenElement = undefined
      if (document.webkitFullscreenElement !== undefined) {
        fullScreenElement = document.webkitFullscreenElement 	// Chrome,Safari
      } else if (document.mozFullScreenElement !== undefined) {
        fullScreenElement = document.mozFullScreenElement 	// Firefox
      } else if (document.msFullscreenElement !== undefined) {
        fullScreenElement = document.msFullscreenElement 	// IE
      } else {
        fullScreenElement = document.fullscreenElement
      }

      return fullScreenElement
    },
    startFullScreen: function () {
      const target = document.getElementById('main-content')
      if (target.webkitRequestFullscreen) {
        target.webkitRequestFullscreen() // Chrome15+,Safari5.1+,Opera15+
      } else if (target.mozRequestFullScreen) {
        target.mozRequestFullScreen() // FF10+
      } else if (target.msRequestFullscreen) {
        target.msRequestFullscreen() // IE11+
      } else if (target.requestFullscreen) {
        target.requestFullscreen() // HTML5 Fullscreen API仕様
      } else {
        alert('ご利用のブラウザはフルスクリーン操作に対応していません')
        return
      }

      this.isFullScreenMode = true
    },
    endFullScreen: function () {
      if (document.webkitCancelFullScreen) {
        document.webkitCancelFullScreen() // Chrome15+,Safari5.1+,Opera15+
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen() // FF10+
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen() // IE11+
      } else if(document.cancelFullScreen) {
        document.cancelFullScreen() // Gecko:FullScreenAPI仕様
      } else if(document.exitFullscreen) {
        document.exitFullscreen() // HTML5 Fullscreen API仕様
      }

      this.isFullScreenMode = false
    }
  }
}
</script>
