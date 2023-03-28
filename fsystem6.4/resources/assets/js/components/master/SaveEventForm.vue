<template>
  <div>
    <a @click="initModal" role="button">{{ date.day }}</a>
    <modal title="カレンダー設定" effect="fade" v-model="showModal">
      <form ref="save_form" class="form-horizontal basic-form" :action="currentUrl" method="POST">
        <div class="row">
          <div class="col-sm-7 col-xs-8 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-sm-5 control-label">日付</label>
              <div class="col-sm-7 text-left">
                <span class="shown_label">{{ date.formatted_date }}</span>
                <input name="date" type="hidden" :value="date.date">
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-7 col-xs-8 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 control-label">
                行事区分
              </label>
              <div class="col-sm-7 text-left">
                <span class="shown_label">{{ eventClassList[eventClass] }}</span>
                <input name="event_class" type="hidden" :value="eventClass">
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-7 col-xs-8 col-sm-offset-1">
            <div class="row form-group">
              <label for="event" class="col-md-5 col-sm-5 control-label required">
                行事
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7">
                <input class="form-control" name="event" maxlength="30" type="text" :value="calendar.event || ''" :disabled="! canSaveEvent" required>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-7 col-xs-8 col-sm-offset-1">
            <div class="row form-group">
              <label for="remark" class="col-md-5 col-sm-5 control-label">備考</label>
              <div class="col-sm-7">
                <input id="remark" class="form-control" name="remark" maxlength="50" type="text" :value="calendar.remark || ''" :disabled="! canSaveEvent">
              </div>
            </div>
          </div>
        </div>
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="POST">
      </form>

      <div slot="modal-footer" class="modal-footer">
        <button v-if="canSaveEvent" class="btn btn-success" type="button" @click="saveEvent($event)">保存</button>
        <button v-if="canSaveEvent && savedEvent" class="btn btn-danger" type="button" @click="deleteEvent($event)">削除</button>
        <button class="btn btn-default" type="button" @click="showModal = false">キャンセル</button>
      </div>

      <form ref="delete_form" :action="actionOfDeleteEvent" method="POST">
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="DELETE">
      </form>
    </modal>
  </div>
</template>

<script>
import VueStrap from 'vue-strap'

export default {
  components: {Modal: VueStrap.modal},
  props: {
    date: {
      type: Object,
      required: true
    },
    calendar: {
      type: [Array, Object],
      required: true
    },
    eventClass: {
      type: Number,
      required: true
    },
    eventClassList: {
      type: Object,
      required: true
    },
    actionOfDeleteEvent: {
      type: String,
      required: true
    },
    canSaveEvent: {
      type: Boolean,
      required: true
    }
  },
  data: function () {
    return {
      showModal: false,
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      currentUrl: location.href.split('?')[0],
    }
  },
  computed: {
    savedEvent: function () {
      return (this.calendar.date || '') !== ''
    }
  },
  methods: {
    initModal: function () {
      this.showModal = true
    },
    saveEvent: function (event) {
      if (confirm('データを登録しますか？')) {
        event.target.disabled = true
        this.$refs.save_form.submit()
      }
    },
    deleteEvent: function (event) {
      if (confirm('選択したマスタを削除しますか？')) {
        event.target.disabled = true
        this.$refs.delete_form.submit()
      }
    }
  }
}
</script>
