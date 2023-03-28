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
    <span v-if="selectedUser.user_name" class="shown_label">
      {{ selectedUser.user_name }}
    </span>
    <input type="hidden" name="user_name" :value="selectedUser.user_name">
    <input type="hidden" name="user_code" :value="selectedUser.user_code">
    <button v-if="! disabled" class="btn btn-default btn-xs pull-right" type="button" @click="initModal">
      <i class="fa fa-search" aria-hidden="true"></i>
    </button>
    <modal title="ユーザ検索" effect="fade" large v-model="showModal">
      <div class="form-horizontal basic-form">
        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 control-label">ユーザコード</label>
              <div class="col-md-7 col-sm-7">
                <input class="form-control ime-inactive" maxlength="15" type="text" v-model="form.userCode">
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 control-label">ユーザ名</label>
              <div class="col-md-7 col-sm-7">
                <input class="form-control ime-active" maxlength="50" type="text" v-model="form.userName">
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 control-label">所属</label>
              <div class="col-md-7 col-sm-7">
                <select class="form-control ime-active" v-model="form.affiliation">
                  <option value=""></option>
                  <option v-for="(value, label) in affiliationList" :key="value" :value="value">{{ label }}</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 control-label">メールアドレス</label>
              <div class="col-md-7 col-sm-7">
                <input class="form-control ime-active" maxlength="40" type="text" v-model="form.mailAddress">
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-10 col-sm-offset-10 col-xs-offset-9">
            <button class="btn btn-default" type="button" @click="searchUsers">
              <i class="fa fa-search"></i>&nbsp;検索
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
                  <th>ユーザコード</th>
                  <th>ユーザ名</th>
                  <th>所属</th>
                  <th>メールアドレス</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="u in users" :key="u.user_code">
                  <td>
                    <input type="radio" name="selected_master" @click="selectUser(u)" :value="u.user_code">
                  </td>
                  <td class="text-left">{{ u.user_code }}</td>
                  <td class="text-left">{{ u.user_name }}</td>
                  <td class="text-left">{{ u.affiliation.label }}</td>
                  <td class="text-left">{{ u.mail_address }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div slot="modal-footer" class="modal-footer">
        <button class="btn btn-success" type="button" @click="fixUser">選択</button>
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
    userCode: {
      type: String,
      default: ''
    },
    userName: {
      type: String,
      default: ''
    },
    affiliationList: {
      type: Object,
      required: true
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data: function () {
    return {
      showModal: false,
      form: {
        userCode: '',
        userName: '',
        affiliation: '',
        mailAddress: ''
      },
      users: [],
      selectedUser: {
        userCode: this.userCode,
        userName: this.userName
      }
    }
  },
  methods: {
    initModal: function () {
      this.form = {
        userCode: '',
        userName: '',
        affiliation: '',
        mailAddress: ''
      }
      this.users = []
      this.showModal = true
    },
    searchUsers: function () {
      axios.get('/api/search-users', {
        params: {
          user_code: this.form.userCode,
          user_name: this.form.userName,
          affiliation: this.form.affiliation,
          mail_address: this.form.mailAddress
        }
      })
        .then(response => {
          this.users = response.data
          if (this.users.length === 0) {
            alert('条件に合致するユーザは見つかりませんでした。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    selectUser: function (user) {
      this.selectedUser = user
    },
    fixUser: function () {
      if (! this.selectedUser.user_code) {
        alert('ユーザが未選択です。')
        return
      }

      this.showModal = false
    },
    cancelSelect: function () {
      this.selectedUser = {
        userCode: '',
        userName: ''
      }

      this.showModal = false
    }
  }
}
</script>
