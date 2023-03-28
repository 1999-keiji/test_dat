<style scoped>
  #add-row {
    margin-top: -2em;
  }
</style>

<template>
  <div>
    <button type="button" class="btn btn-lg btn-default pull-right" @click="initModal">
      <i class="fa fa-plus"></i> 追加
    </button>
    <modal title="品種マスタ追加" effect="fade" large v-model="showModal">
      <form id="add-species-form" class="form-horizontal basic-form" :action="routeAction" method="POST">
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <div class="row form-group">
              <label class="col-md-3 col-sm-3 col-xs-4 col-md-offset-1 col-sm-offset-1 control-label required">
                品種コード
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <input
                  class="form-control ime-inactive"
                  name="species_code"
                  :maxlength="speciesCode.max_length"
                  :title="speciesCode.help_text"
                  type="text"
                  data-toggle="tooltip"
                  v-model="species.species_code"
                  required>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-3 col-sm-3 col-xs-4 col-md-offset-1 col-sm-offset-1 control-label required">
                品種名
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-3 col-sm-3 col-xs-6">
                <input
                  class="form-control ime-active"
                  name="species_name"
                  maxlength="20"
                  type="text"
                  v-model="species.species_name"
                  required>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-3 col-sm-3 col-xs-4 col-md-offset-1 col-sm-offset-1 control-label required">
                品種名略称
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-3 col-sm-3 col-xs-6">
                <input
                  class="form-control ime-active"
                  name="species_abbreviation"
                  maxlength="10"
                  type="text"
                  v-model="species.species_abbreviation"
                  required>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-3 col-sm-3 col-xs-4 col-md-offset-1 col-sm-offset-1 control-label">
                備考
              </label>
              <div class="col-md-3 col-sm-3 col-xs-6">
                <input
                  class="form-control ime-active"
                  name="remark"
                  maxlength="50"
                  type="text"
                  v-model="species.remark">
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-3 col-sm-3 col-xs-4 col-md-offset-1 col-sm-offset-1 control-label">
                変換元実績集計コード
              </label>
              <div class="col-md-6 col-sm-8 col-xs-8">
                <table class="table table-color-bordered table-more-condensed">
                  <thead>
                    <tr>
                      <th>削除</th>
                      <th>大カテゴリ</th>
                      <th>中カテゴリ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(sc, index) in speciesConverters" :key="index">
                      <td>
                        <button class="btn btn-danger" type="button" @click="deleteSpeciesConverters(index)">削除</button>
                      </td>
                      <td>
                        <input
                          class="form-control ime-inactive"
                          :name="'species_converters[' + index + '][product_large_category]'"
                          :maxlength="categoryCode.max_length"
                          :title="categoryCode.help_text"
                          type="text"
                          v-model="sc.product_large_category">
                      </td>
                      <td>
                        <input
                          class="form-control ime-inactive"
                          :name="'species_converters[' + index + '][product_middle_category]'"
                          :maxlength="categoryCode.max_length"
                          :title="categoryCode.help_text"
                          type="text"
                          v-model="sc.product_middle_category">
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
                <button id="add-row" type="button" class="btn btn-default pull-right" @click="addSpeciesConverters">
                  <i class="fa fa-plus"></i> 追加
                </button>
              </div>
            </div>
          </div>
        </div>
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="POST">
      </form>
      <div slot="modal-footer" class="modal-footer">
        <button class="btn btn-default btn-lg" type="button" @click="submitForm">
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
      requried: true
    },
    speciesCode: {
      type: Object,
      requried: true
    },
    categoryCode: {
      type: Object,
      requried: true
    }
  },
  data: () => {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      showModal: false,
      species: {
        species_code: null,
        species_name: null,
        species_abbreviation: null,
        remark: null
      },
      speciesConverters: [],
    }
  },
  methods: {
    initModal: function () {
      this.species = {
        species_code: null,
        species_name: null,
        species_abbreviation: null,
        remark: null
      }
      this.speciesConverters = []
      this.showModal = true
    },
    addSpeciesConverters: function () {
      this.speciesConverters.push({
        product_large_category: '',
        product_middle_category: ''
      })
    },
    deleteSpeciesConverters: function (index) {
      this.speciesConverters.splice(index, 1)
    },
    submitForm: function () {
      if (this.speciesConverters.length === 0) {
        alert('変換元実績集計コードを設定してください。')
        return
      }

      if (confirm('データを登録しますか?')) {
        document.getElementById('add-species-form').submit()
      }
    }
  }
}
</script>
