module.exports = {
  "env": {
    "browser": true,
    "es6": true,
    "jquery": true,
    "node": true
  },
  "extends": [
    "eslint:recommended"
  ],
  "parser": "vue-eslint-parser",
  "parserOptions": {
    "ecmaVersion": 6,
    "sourceType": "module",
    "ecmaFeatures": {
      "experimentalObjectRestSpread": true
    }
  },
  "rules": {
    "indent": [
      "error",
      2
    ],
    "linebreak-style": [
      "error",
      "unix"
    ],
    "no-console": "off",
    "quotes": [
      "error",
      "single"
    ],
    "semi": [
      "error",
      "never"
    ]
  },
  "globals": {
    "Vue": true,
    "axios": true
  }
}
