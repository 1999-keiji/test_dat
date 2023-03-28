<template>
  <div>
    <button type="button" class="btn btn-lg btn-default pull-right" @click="showModal = true">
      <i class="fa fa-plus"></i> 追加
    </button>

    <modal title="ベッド状況追加" effect="fade" v-model="showModal">
      <div class="form-horizontal basic-form">
        <div class="row">
          <div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 col-xs-5 control-label">
                工場<span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <select class="form-control" v-model="factoryCode" required @change="getFactorySpecies">
                  <option value=""></option>
                  <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">
                    {{ f.factory_abbreviation }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 col-xs-5 control-label">
                工場品種<span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <select class="form-control" name="factory_species_code" v-model="factorySpeciesCode" :disabled="disabledToSelectFactorySpecies">
                  <option value=""></option>
                  <option v-for="fs in factorySpeciesList" :key="fs.factory_species_code" :value="fs.factory_species_code">
                    {{ fs.factory_species_name }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 col-xs-5 control-label">
                日付<span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <datepicker-ja
                  attr-name="start_of_week"
                  :date="startOfWeek"
                  :disabled-days-of-week="[0, 2, 3, 4, 5, 6]"
                  @update-date="updateStartOfWeek">
                </datepicker-ja>&nbsp;から1週間
              </div>
            </div>
          </div>
        </div>
      </div>
      <div slot="modal-footer" class="modal-footer">
        <button class="btn btn-default btn-lg" type="button" :disabled="disabledToSubmit" @click="submitForm">
          <i class="fa fa-save"></i> 保存
        </button>
        <button class="btn btn-default btn-lg" type="button" @click="showModal = false">
          キャンセル
        </button>
      </div>
    </modal>
  </div>
</template>

<script>
import VueStrap from 'vue-strap'

export default {
  components: {Modal: VueStrap.modal},
  props: {
    routeAction: {
      type: String,
      required: true
    },
    factories: {
      type: Array,
      required: true
    }
  },
  data: function () {
    const moment = require('moment')
    return {
      factoryCode: null,
      factorySpeciesCode: null,
      factorySpeciesList: [],
      startOfWeek: moment().startOf('isoWeek').format('YYYY/MM/DD'),
      disabledToSubmit: false,
      showModal: false
    }
  },
  computed: {
    disabledToSelectFactorySpecies: function () {
      return this.factorySpeciesList.length === 0
    }
  },
  methods: {
    getFactorySpecies: function () {
      this.factorySpeciesCode = null
      this.factorySpeciesList = []
      if (! this.factoryCode) {
        return
      }

      axios.get('/api/get-factory-species', {
        params: {
          factory_code: this.factoryCode
        }
      })
        .then(response => {
          this.factorySpeciesList = response.data
          if (this.disabledToSelectFactorySpecies) {
            alert('工場品種が未登録の工場です。')
          }
        })
        .catch(() => {
          alert('工場品種の取得に失敗しました。')
        })
    },
    updateStartOfWeek: function (startOfWeek) {
      this.startOfWeek = startOfWeek
    },
    submitForm: function () {
      if (! this.factoryCode) {
        alert('工場を選択してください。')
        return
      }
      if (! this.factorySpeciesCode) {
        alert('工場品種を選択してください。')
        return
      }
      if (! this.startOfWeek) {
        alert('日付を選択してください。')
        return
      }

      if (confirm('データを登録しますか?')) {
        this.disabledToSubmit = true

        axios.post(this.routeAction, {
          factory_code: this.factoryCode,
          factory_species_code: this.factorySpeciesCode,
          start_of_week: this.startOfWeek,
          method: 'POST'
        })

        alert('データ登録を開始します。')
        location.reload()
      }
    }
  }
}
</script>
