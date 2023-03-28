<template>
  <div class="datepicker">
    <input class="form-control datepicker-input" type="text" autocomplete="off"
      v-model="val"
      :class="{'with-reset-button': clearButton}"
      :name="name"
      :placeholder="placeholder"
      :style="{width:width}"
      @click="inputClick"
    />
    <button v-if="clearButton && val" type="button" class="close" @click="val = ''">
      <span>&times;</span>
    </button>
    <div class="datepicker-popup" v-show="displayMonthView">
      <div class="datepicker-inner">
        <div class="datepicker-body">
          <div class="datepicker-ctrl">
            <span :class="preBtnClasses" aria-hidden="true" @click="preNextYearClick(0)"></span>
            <span :class="nextBtnClasses" aria-hidden="true" @click="preNextYearClick(1)"></span>
            <p>{{stringifyYearHeader(currDate)}}</p>
          </div>
          <div class="datepicker-monthRange">
            <template v-for="(m, index) in text.months">
              <span :key="index"
                v-text="m.substr(0,3)"
                :class="{'datepicker-dateRange-item-active':
                  (text.months[parse(val).getMonth()] === m) &&
                  currDate.getFullYear() === parse(val).getFullYear()}"
                @click="monthSelect(index)"
              ></span>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    value: {type: String},
    format: {default: 'yyyy/MM'},
    width: {type: String},
    clearButton: {type: Boolean, default: false},
    lang: {type: String, default: navigator.language},
    name: {type: String},
    placeholder: {type: String},
    iconsFont: {type: String, default: 'glyphicon'},
    allowEmpty: {type: Boolean, default: false}
  },
  data () {
    return {
      currDate: new Date(),
      displayMonthView: false,
      val: this.value
    }
  },
  watch: {
    format () {
      this.val = this.stringify(this.currDate)
    },
    val (val) {
      this.$emit('input', val)
    },
    value (val) {
      if (this.val !== val) { this.val = val }
    }
  },
  computed: {
    text () {
      return {months: [
        '1月', '2月', '3月', '4月', '5月', '6月',
        '7月', '8月', '9月', '10月', '11月', '12月'
      ]}
    },
    preBtnClasses () {
      return `datepicker-preBtn ${this.iconsFont} ${this.iconsFont}-chevron-left`
    },
    nextBtnClasses () {
      return `datepicker-nextBtn ${this.iconsFont} ${this.iconsFont}-chevron-right`
    }
  },
  methods: {
    close () {
      this.displayMonthView = false
    },
    inputClick () {
      this.currDate = this.parse(this.val) || this.parse(new Date())
      this.displayMonthView = true
    },
    preNextYearClick (flag) {
      const year = this.currDate.getFullYear()
      const months = this.currDate.getMonth()

      if (flag === 0) {
        this.currDate = new Date(year - 1, months, 1)
      } else {
        this.currDate = new Date(year + 1, months, 1)
      }
    },
    monthSelect (index) {
      this.displayMonthView = false
      this.displayDayView = true
      this.currDate = new Date(this.currDate.getFullYear(), index, 1)
      this.val = this.stringify(this.currDate)
      this.displayDayView = false
    },
    parseMonth (date) {
      return this.text.months[date.getMonth()]
    },
    stringifyYearHeader (date) {
      return date.getFullYear()
    },
    stringify (date, format = this.format) {
      if (!date) date = this.parse()
      if (!date) return ''
      const year = date.getFullYear()
      const month = date.getMonth() + 1
      const monthName = this.parseMonth(date)
      return format
        .replace(/yyyy/g, year)
        .replace(/yy/g, year)
        .replace(/MMMM/g, monthName)
        .replace(/MMM/g, monthName.substring(0, 3))
        .replace(/MM/g, ('0' + month).slice(-2))
        .replace(/M(?!a)/g, month)
    },
    parse (str) {
      if (str === undefined || str === null) { str = this.val }
      if (str === '') { return new Date() }
      let date = new Date(str.substring(0, 4), str.substring(5, 7)-1, 1)
      return isNaN(date.getFullYear()) ? new Date() : date
    },
  },
  mounted () {
    this.$emit('child-created', this)
    this.currDate = this.parse(this.val) || this.parse(new Date())
    if (this.val === '' && ! this.allowEmpty) {
      this.val = this.stringify(this.currDate)
    }
    this._blur = e => {
      if (!this.$el.contains(e.target))
        this.close()
    }
    window.addEventListener('click', this._blur)
  },
  beforeDestroy () {
    window.removeEventListener('click', this._blur)
  }
}
</script>

<style scoped>
.datepicker {
  position: relative;
  display: inline-block;
}
input.datepicker-input.with-reset-button {
  padding-right: 25px;
}
.datepicker > button.close {
  position: absolute;
  top: 0;
  right: 0;
  outline: none;
  z-index: 2;
  display: block;
  width: 34px;
  height: 34px;
  line-height: 34px;
  text-align: center;
}
.datepicker > button.close:focus {
  opacity: .2;
}
.datepicker-popup {
  position: absolute;
  border: 1px solid #ccc;
  border-radius: 5px;
  background: #fff;
  margin-top: 2px;
  z-index: 1000;
  box-shadow: 0 6px 12px rgba(0,0,0,0.175);
}
.datepicker-inner {
  width: 218px;
}
.datepicker-body {
  padding: 10px 10px;
}
.datepicker-ctrl p,
.datepicker-ctrl span,
.datepicker-body span {
  display: inline-block;
  width: 28px;
  line-height: 28px;
  height: 28px;
  border-radius: 4px;
}
.datepicker-ctrl p {
  width: 65%;
}
.datepicker-ctrl span {
  position: absolute;
}
.datepicker-body span {
  text-align: center;
}
.datepicker-monthRange span {
  width: 48px;
  height: 50px;
  line-height: 45px;
}

.datepicker-dateRange-item-active:hover,
.datepicker-dateRange-item-active {
  background: rgb(50, 118, 177)!important;
  color: white!important;
}
.datepicker-monthRange {
  margin-top: 10px
}
.datepicker-monthRange span,
.datepicker-ctrl span,
.datepicker-ctrl p,
.datepicker-dateRange span {
  cursor: pointer;
}
.datepicker-monthRange span:hover,
.datepicker-ctrl p:hover,
.datepicker-ctrl i:hover,
.datepicker-dateRange span:hover,
.datepicker-dateRange-item-hover {
  background-color : #eeeeee;
}
.datepicker-ctrl {
  position: relative;
  height: 30px;
  line-height: 30px;
  font-weight: bold;
  text-align: center;
}
.datepicker-preBtn {
  left: 2px;
}
.datepicker-nextBtn {
  right: 2px;
}
</style>
