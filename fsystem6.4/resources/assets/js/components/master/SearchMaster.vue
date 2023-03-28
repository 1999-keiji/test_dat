<style scoped>
  span.shown_label {
    line-height: 1.8;
  }
  p.norice-text {
    text-align: left;
  }
  table.table-in-modal {
    margin-top: 1em;
  }
</style>

<template>
  <div class="form-inline">
    <span v-if="selectedMaster.name" class="shown_label">
      {{ selectedMaster.name }}
    </span>
    <input
      type="hidden"
      :name="targets[target]['dummyName']"
      :value="selectedMaster.name">
    <input
      type="hidden"
      :name="targets[target]['genuineName']"
      :value="selectedMaster.code">
    <button v-if="! disabled" class="btn btn-default btn-xs pull-right" type="button" @click="initModal">
      <i class="fa fa-search" aria-hidden="true"></i>
    </button>
    <modal :title="targets[target]['title']" effect="fade" large v-model="showModal">
      <div class="form-horizontal basic-form">
        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="master-code" class="col-md-5 col-sm-5 control-label">
                {{ targets[target]['target'] }}コード
              </label>
              <div class="col-md-7 col-sm-7">
                <input class="form-control ime-inactive" maxlength="15" type="text" v-model="form.masterCode">
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="master-name" class="col-md-5 col-sm-5 control-label">
                {{ targets[target]['target'] }}名称
              </label>
              <div class="col-md-7 col-sm-7">
                <input class="form-control ime-active" maxlength="50" type="text" v-model="form.masterName">
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6">
            <div class="row form-group">
              <label for="master-name2" class="col-md-5 col-sm-5 control-label">
                {{ targets[target]['target'] }}名称2
              </label>
              <div class="col-md-7 col-sm-7">
                <input class="form-control ime-active" maxlength="50" type="text" v-model="form.masterName2">
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="master-abbreviation" class="col-md-5 col-sm-5 control-label">
                {{ targets[target]['target'] }}略称
              </label>
              <div class="col-md-7 col-sm-7">
                <input class="form-control ime-active" maxlength="20" type="text" v-model="form.masterAbbreviation">
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6">
            <div class="row form-group">
              <label for="master-name-kana" class="col-md-5 col-sm-5 control-label">
                {{ targets[target]['target'] }}カナ
              </label>
              <div class="col-md-7 col-sm-7">
                <input class="form-control ime-active" maxlength="30" type="text" v-model="form.masterNameKana">
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="master-address" class="col-md-5 col-sm-5 control-label">住所</label>
              <div class="col-md-7 col-sm-7">
                <input class="form-control ime-active" maxlength="50" type="text" v-model="form.masterAddress">
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6">
            <div class="row form-group">
              <label for="master-phone-number" class="col-md-5 col-sm-5 control-label">電話番号</label>
              <div class="col-md-7 col-sm-7">
                <input class="form-control ime-inactive" maxlength="20" type="text" v-model="form.masterPhoneNumber">
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-10 col-sm-offset-10 col-xs-offset-9">
            <button class="btn btn-default" type="button" @click="searchMasters">
              <i class="fa fa-search"></i> 検索
            </button>
          </div>
        </div>

        <div class="row">
          <div class="col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1">
            <p class="norice-text">最大20件まで表示されます。</p>
            <table class="table table-color-bordered table-more-condensed table-in-modal">
              <thead>
                <tr>
                  <th>選択</th>
                  <th>{{ targets[target]['target'] }}コード</th>
                  <th>{{ targets[target]['target'] }}名称</th>
                  <th>住所</th>
                  <th>電話番号</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="master in masters" :key="master.code">
                  <td>
                    <input type="radio" name="selected_master" @click="selectMaster(master)" :value="master">
                  </td>
                  <td class="text-left">{{ master.code }}</td>
                  <td class="text-left">{{ master.name }}</td>
                  <td class="text-left">{{ master.address }}</td>
                  <td class="text-left">{{ master.phone_number }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div slot="modal-footer" class="modal-footer">
        <button class="btn btn-success" type="button" @click="fixMaster">選択</button>
        <button class="btn btn-default" type="button" @click="cancelSelect">キャンセル</button>
      </div>
    </modal>
  </div>
</template>

<script>
import VueStrap from 'vue-strap'

export default {
  components: {Modal: VueStrap.modal},
  props: {
    target: {
      type: String,
      required: true,
      validator: (target) => {
        return ['delivery_destination', 'end_user'].includes(target)
      }
    },
    code: {
      type: String,
      default: ''
    },
    name: {
      type: String,
      default: ''
    },
    factoryCode: {
      type: String,
      default: null
    },
    customerCode: {
      type: String,
      default: null
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data: function () {
    return {
      targets: {
        delivery_destination: {
          dummyName: 'delivery_destination_name',
          genuineName: 'delivery_destination_code',
          target: '納入先',
          title: '納入先検索',
          apiUrl: '/api/search-delivery-destinations'
        },
        end_user: {
          dummyName: 'end_user_name',
          genuineName: 'end_user_code',
          target: 'エンドユーザ',
          title: 'エンドユーザ検索',
          apiUrl: '/api/search-end-users'
        }
      },
      showModal: false,
      form: {
        masterCode: '',
        masterName: '',
        masterName2: '',
        masterAbbreviation: '',
        masterNameKana: '',
        masterAddress: '',
        masterPhoneNumber: ''
      },
      masters: [],
      selectedMaster: {
        code: this.code,
        name: this.name
      }
    }
  },
  watch: {
    code: function (code) {
      this.selectedMaster.code = code
    },
    name: function (name) {
      this.selectedMaster.name = name
    }
  },
  methods: {
    initModal: function () {
      this.form = {
        masterCode: '',
        masterName: '',
        masterName2: '',
        masterAbbreviation: '',
        masterNameKana: '',
        masterAddress: '',
        masterPhoneNumber: ''
      }
      this.masters = []
      this.showModal = true
    },
    searchMasters: function () {
      axios.get(this.targets[this.target]['apiUrl'], {
        params: {
          master_code: this.form.masterCode,
          master_name: this.form.masterName,
          master_name2: this.form.masterName2,
          master_abbreviation: this.form.masterAbbreviation,
          master_name_kana: this.form.masterNameKana,
          master_address: this.form.masterAddress,
          master_phone_number: this.form.masterPhoneNumber,
          factory_code: this.factoryCode,
          customer_code: this.customerCode
        }
      })
        .then(response => {
          this.masters = response.data
          if (this.masters.length === 0) {
            alert('条件に合致する' + this.targets[this.target].target + 'は見つかりませんでした。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    selectMaster: function (master) {
      this.selectedMaster = master
    },
    fixMaster: function () {
      if (! this.selectedMaster.code) {
        alert(this.targets[this.target]['target'] + 'が未選択です。')
        return
      }

      if (this.target == 'delivery_destination') {
        this.$emit('get-selected-delivery-destination', this.selectedMaster)
      }
      if (this.target == 'end_user') {
        this.$emit('get-selected-end-user', this.selectedMaster)
      }

      this.showModal = false
    },
    cancelSelect: function () {
      this.selectedMaster = {
        code: '',
        name: ''
      }

      if (this.target == 'delivery_destination') {
        this.$emit('get-selected-delivery-destination', {
          code: '',
          name: '',
          end_user: {
            code: '',
            name: ''
          }
        })
      }
      if (this.target == 'end_user') {
        this.$emit('get-selected-end-user', this.selectedMaster)
      }

      this.showModal = false
    }
  }
}
</script>
