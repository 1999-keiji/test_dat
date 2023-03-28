<template>
  <button class="btn btn-sm btn-success" type="button" @click="fixSimulation($event)">確定</button>
</template>

<script>
export default {
  props: {
    firstHarvestingDate: {
      type: String,
      required: true
    },
    hrefToCheckBedNumber: {
      type: String,
      required: true
    },
    actionOfFixSimulation: {
      type: String,
      required: true
    },
    hrefToIndexOfFixedSimulations: {
      type: String,
      required: true
    }
  },
  methods: {
    fixSimulation: function (event) {
      const moment = require('moment')
      if (moment().isAfter(moment(this.firstHarvestingDate, 'YYYY/MM/DD'), 'day')) {
        alert('収穫開始日が過去の日付です。収穫開始日が今日以降の日付になるよう再シミュレーションしてください。')
        return
      }

      event.target.disabled = true
      alert('移動ベッド数の確認処理を実行します。しばらくお待ちください。')

      axios.get(this.hrefToCheckBedNumber)
        .then(res => {
          if (! res.data.result) {
            alert(res.data.date + '以降に設定されていないベッドがあります。\nベッドを設定、保存してから確定処理を行ってください。')
            return
          }

          if (confirm('シミュレーションを確定して生産計画へ反映します。よろしいですか？')) {
            axios.post(this.actionOfFixSimulation, {'_method': 'POST'})
            alert('確定処理を開始しました。')

            location.href = this.hrefToIndexOfFixedSimulations
          }
        })
        .catch(() => {
          alert('ベッド確認処理に失敗しました。しばらくお待ちください。')
        })
        .finally(() => {
          event.target.disabled = false
        })
    }
  }
}
</script>
